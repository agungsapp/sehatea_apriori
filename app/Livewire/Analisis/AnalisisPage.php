<?php

namespace App\Livewire\Analisis;

use App\Services\AprioriAnalyzer;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Rule;

class AnalisisPage extends Component
{
    #[Rule('required|date')]
    public $startDate;

    #[Rule('required|date')]
    public $endDate;

    #[Rule('required|numeric|max:1')]
    public $minSupport = 0.02;

    #[Rule('required|numeric|max:1')]
    public $minConfidence = 0.2;

    public $results = [];
    public $isAnalyzing = false;

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->minSupport = 0.02;
        $this->minConfidence = 0.2;
    }

    public function analyze()
    {
        $this->validate();
        $this->isAnalyzing = true;

        try {
            $analyzer = new AprioriAnalyzer($this->minSupport, $this->minConfidence);
            $this->results = $analyzer->analyze($this->startDate, $this->endDate);
            logger()->info('Analysis Results:', ['data' => $this->results]);
            session()->flash('message', 'Analisis berhasil dilakukan!');
        } catch (\Exception $e) {
            logger()->error('Analysis Error:', ['message' => $e->getMessage()]);
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        $this->isAnalyzing = false;
    }

    public function render()
    {
        return view('livewire.analisis.analisis-page');
    }
}
