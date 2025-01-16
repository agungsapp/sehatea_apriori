<?php

namespace App\Livewire\Pengguna;

use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;

class PenggunaPage extends Component
{
    use WithPagination;
    use LivewireAlert;

    public $name;
    public $email;
    public $password;
    public $userId;


    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->userId),
            ],
            'password' => 'nullable|min:6',
        ];
    }
    protected function messages()
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan.',
            'password.min' => 'Password minimal harus terdiri dari 6 karakter.',
        ];
    }


    #[On('edit-user')] // Mendengarkan event 'edit-user'
    public function loadUserForEdit($id)
    {
        $user = User::findOrFail($id);

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;

        $this->alert('info', 'Silahkan edit pada form yang tersedia diatas.', [
            'showConfirmButton' => false,
            'timer' => 2000,
        ]);
    }

    public function batalEdit(){
        $this->resetForm();
    }



    // public function updateUser()
    // {
    //     $this->validate();

    //     $user = User::findOrFail($this->userId);
    //     $user->update([
    //         'name' => $this->name,
    //         'email' => $this->email,
    //         'password' => $this->password ? bcrypt($this->password) : $user->password,
    //     ]);

    //     $this->dispatch('user-updated'); // Event untuk notifikasi atau aksi lain
    //     $this->resetForm();
    // }

    // public function storeUser()
    // {
    //     $this->validate();

    //     User::create([
    //         'name' => $this->name,
    //         'email' => $this->email,
    //         'password' => bcrypt($this->password),
    //     ]);

    //     $this->alert('success', 'Berhasil menambahkan pengguna baru!');
    //     $this->reset(['name', 'email', 'password']);
    // }

    public function save()
    {
        $this->validate();

        if ($this->userId) {
            // Update existing user
            $user = User::findOrFail($this->userId);
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password ? bcrypt($this->password) : $user->password,
            ]);
        } else {
            // Create new user
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => bcrypt($this->password),
            ]);
        }

        $this->alert('success', 'Berhasil menyimpan data pengguna!');
        $this->dispatch('pg:eventRefresh-pengguna-table-08tser-table');
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['name', 'email', 'password', 'userId']);
    }


    public function render()
    {
        $users = User::all();

        return view('livewire.pengguna.pengguna-page', ['users' => $users]);
    }
}
