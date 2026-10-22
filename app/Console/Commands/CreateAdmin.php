<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create {email?} {--name=Administrator}';

    protected $description = 'Buat akun admin Prisma Dana Gorontalo';

    public function handle(): int
    {
        $email = Str::lower(trim($this->argument('email') ?? $this->ask('Email admin')));
        $name = $this->option('name');
        $validator = Validator::make(compact('email', 'name'), [
            'email' => ['required', 'email', 'max:254', 'unique:users,email'],
            'name' => ['required', 'string', 'max:150'],
        ]);

        if ($validator->fails()) {
            $this->error('Email tidak valid, sudah terdaftar, atau nama tidak valid.');

            return self::FAILURE;
        }

        $password = $this->secret('Kata sandi (minimal 12 karakter)');
        $confirmation = $this->secret('Ulangi kata sandi');
        if (! is_string($password) || Str::length($password) < 12 || strlen($password) > 72 || $password !== $confirmation) {
            $this->error('Kata sandi harus 12–72 byte dan konfirmasinya harus sama.');

            return self::FAILURE;
        }

        $user = new User(compact('name', 'email', 'password'));
        $user->is_admin = true;
        $user->save();
        $this->info('Admin berhasil dibuat. Masuk melalui /admin/login.');

        return self::SUCCESS;
    }
}
