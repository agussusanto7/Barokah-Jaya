<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class Register extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ];
    }

    protected $messages = [
        'email.unique' => 'Email ini sudah terdaftar, silakan gunakan email lain.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
        'password.min' => 'Password minimal harus 6 karakter.',
    ];

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'customer', // Default role untuk publik
        ]);

        // Auto login setelah registrasi
        Auth::login($user);

        // Redirect ke halaman katalog
        return redirect()->route('catalog');
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('components.layouts.guest');
    }
}