<?php

namespace App\Services;

use App\Models\AnalisisApriori;
use App\Models\DetailTransaksi;

class AprioriAnalyzer
{
    private $minSupport;
    private $minConfidence;

    public function __construct($minSupport = 0.1, $minConfidence = 0.5)
    {
        $this->minSupport = $minSupport;
        $this->minConfidence = $minConfidence;
    }

    public function analyze($startDate, $endDate)
    {
        try {
            // Ambil data transaksi dalam periode
            $transactions = DetailTransaksi::with(['transaksi', 'produk'])
                ->whereHas('transaksi', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->get()
                ->groupBy('transaksi_id')
                ->map(function ($items) {
                    return $items->pluck('produk.nama')->toArray();
                })->values()->toArray();

            if (empty($transactions)) {
                throw new \Exception("Tidak ada transaksi dalam periode yang dipilih");
            }

            logger()->info('Transactions:', ['data' => $transactions]);

            $frequent1ItemSet = $this->findFrequent1ItemSet($transactions);

            if (empty($frequent1ItemSet)) {
                throw new \Exception("Tidak ditemukan item yang memenuhi minimum support");
            }

            $frequentItemSets = $this->generateFrequentItemSets($transactions, $frequent1ItemSet);
            $rules = $this->generateAssociationRules($frequentItemSets, $transactions);

            // Simpan hasil analisis
            foreach ($rules as $rule) {
                AnalisisApriori::create([
                    'itemset' => json_encode([
                        'antecedent' => $rule['antecedent'],
                        'consequent' => $rule['consequent']
                    ]),
                    'support' => $rule['support'],
                    'confidence' => $rule['confidence'],
                    'minimum_transactions' => count($transactions),
                    'periode' => $endDate
                ]);
            }

            return $rules;
        } catch (\Exception $e) {
            logger()->error('Apriori Analysis Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function findFrequent1ItemSet($transactions)
    {
        $items = [];
        $totalTransactions = count($transactions);

        logger()->info('Total Transactions:', ['count' => $totalTransactions]);

        foreach ($transactions as $transaction) {
            foreach ($transaction as $item) {
                if (!isset($items[$item])) {
                    $items[$item] = 1;
                } else {
                    $items[$item]++;
                }
            }
        }

        logger()->info('Item Frequencies:', ['data' => $items]);

        $frequent1ItemSet = [];
        foreach ($items as $item => $count) {
            $support = $count / $totalTransactions;
            logger()->info('Item Support:', [
                'item' => $item,
                'count' => $count,
                'support' => $support,
                'minSupport' => $this->minSupport
            ]);

            if ($support >= $this->minSupport) {
                $frequent1ItemSet[$item] = $support;
            }
        }

        logger()->info('Frequent 1-ItemSet:', ['data' => $frequent1ItemSet]);
        return $frequent1ItemSet;
    }

    private function generateCandidateItemSets($frequentItemSets)
    {
        logger()->info('Generating candidates from:', ['itemsets' => $frequentItemSets]);

        if (empty($frequentItemSets)) {
            return [];
        }

        // Jika ini adalah frequent 1-itemset, ubah formatnya
        if (!is_array(reset($frequentItemSets))) {
            $frequentItemSets = array_map(function ($item) {
                return [$item];
            }, $frequentItemSets);
        }

        $candidates = [];
        $k = count(reset($frequentItemSets)) + 1; // ukuran itemset berikutnya

        foreach ($frequentItemSets as $i => $itemSet1) {
            foreach ($frequentItemSets as $j => $itemSet2) {
                if ($i < $j) {
                    $candidate = array_unique(array_merge($itemSet1, $itemSet2));
                    if (count($candidate) == $k) {
                        sort($candidate); // Urutkan items untuk konsistensi
                        $candidates[] = $candidate;
                    }
                }
            }
        }

        logger()->info('Generated candidates:', ['candidates' => $candidates]);
        return $candidates;
    }

    private function generateFrequentItemSets($transactions, $frequent1ItemSet)
    {
        logger()->info('Starting generateFrequentItemSets', [
            'frequent1ItemSet' => $frequent1ItemSet
        ]);

        $allFrequentItemSets = [$frequent1ItemSet];
        $k = 1;
        $currentFrequentSet = array_keys($frequent1ItemSet);
        $totalTransactions = count($transactions);

        while (!empty($currentFrequentSet)) {
            logger()->info("Processing k=$k itemsets");

            // Generate kandidat
            $candidates = $this->generateCandidateItemSets($currentFrequentSet);
            if (empty($candidates)) {
                break;
            }

            // Hitung support untuk setiap kandidat
            $frequentItemSets = [];
            $frequentItemSetsWithSupport = [];

            foreach ($candidates as $candidate) {
                $count = 0;
                foreach ($transactions as $transaction) {
                    if (count(array_intersect($candidate, $transaction)) == count($candidate)) {
                        $count++;
                    }
                }

                $support = $count / $totalTransactions;
                logger()->info("Candidate support:", [
                    'candidate' => $candidate,
                    'support' => $support,
                    'minSupport' => $this->minSupport
                ]);

                if ($support >= $this->minSupport) {
                    $frequentItemSets[] = $candidate;
                    $frequentItemSetsWithSupport[implode(',', $candidate)] = $support;
                }
            }

            if (!empty($frequentItemSets)) {
                $allFrequentItemSets[] = $frequentItemSetsWithSupport;
                $currentFrequentSet = $frequentItemSets;
            } else {
                break;
            }

            $k++;
        }

        logger()->info('Final frequent itemsets:', ['data' => $allFrequentItemSets]);
        return $allFrequentItemSets;
    }

    private function generateAssociationRules($allFrequentItemSets, $transactions)
    {
        $rules = [];
        $totalTransactions = count($transactions);

        // Skip the first itemset (1-itemsets)
        for ($i = 1; $i < count($allFrequentItemSets); $i++) {
            foreach ($allFrequentItemSets[$i] as $itemSet => $support) {
                $items = explode(',', $itemSet);
                if (count($items) < 2) continue;

                // Generate all possible subsets
                $subsets = $this->generateSubsets($items);

                foreach ($subsets as $antecedent) {
                    if (empty($antecedent) || count($antecedent) == count($items)) continue;

                    $consequent = array_values(array_diff($items, $antecedent));

                    // Calculate confidence
                    $antecedentSupport = $this->calculateSupport($antecedent, $transactions);
                    if ($antecedentSupport > 0) {
                        $confidence = $support / $antecedentSupport;

                        logger()->info("Rule confidence:", [
                            'antecedent' => $antecedent,
                            'consequent' => $consequent,
                            'confidence' => $confidence,
                            'support' => $support
                        ]);

                        if ($confidence >= $this->minConfidence) {
                            $rules[] = [
                                'antecedent' => $antecedent,
                                'consequent' => $consequent,
                                'support' => $support,
                                'confidence' => $confidence
                            ];
                        }
                    }
                }
            }
        }

        return $rules;
    }

    private function generateSubsets($itemSet)
    {
        $subsets = [[]];
        foreach ($itemSet as $item) {
            foreach ($subsets as $subset) {
                $subsets[] = array_merge([$item], $subset);
            }
        }
        return $subsets;
    }

    private function calculateSupport($itemSet, $transactions)
    {
        $count = 0;
        $totalTransactions = count($transactions);

        foreach ($transactions as $transaction) {
            if (count(array_intersect($itemSet, $transaction)) == count($itemSet)) {
                $count++;
            }
        }

        return $count / $totalTransactions;
    }
}
