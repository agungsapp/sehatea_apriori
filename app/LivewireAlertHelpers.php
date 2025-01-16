<?php

namespace App;

use Jantinnerezo\LivewireAlert\LivewireAlert;

trait LivewireAlertHelpers
{
    use LivewireAlert;

    public function showError($message)
    {
        return $this->alert('error', $message, [
            'showConfirmButton' => false,
            'timer' => 3000,
            'position' => 'center',
            'toast' => true,
        ]);
    }

    // Helper method untuk menampilkan success
    public function showSuccess($message)
    {
        return $this->alert('success', $message, [
            'showConfirmButton' => false,
            'timer' => 2000,
            'position' => 'center',
            'toast' => false,
        ]);
    }

    public function showCon($message, $onConfirmed, $options = [])
    {
        $defaultOptions = [
            'showConfirmButton' => true,
            'confirmButtonText' => 'Ya, Hapus!',
            'showCancelButton' => true,
            'position' => 'center',
            'toast' => false,
            'cancelButtonText' => 'Batal',
        ];

        $mergedOptions = array_merge($defaultOptions, $options);

        return $this->alert('warning', $message, array_merge($mergedOptions, [
            'onConfirmed' => $onConfirmed,
        ]));
    }
}
