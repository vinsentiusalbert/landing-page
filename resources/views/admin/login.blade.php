@extends('admin.layout')
@section('title', 'Login Admin')
@section('page-class', 'login-page')
@section('content')
<section class="panel login-panel">
    <p class="eyebrow">AKSES ADMINISTRATOR</p>
    <h1>Masuk ke admin</h1>
    <p class="muted">Kelola pendaftaran calon debitur Prisma Dana Gorontalo.</p>
    <form action="{{ route('admin.login.store') }}" method="post">
        @csrf
        <x-form-field name="email" label="Email admin" type="email" required autocomplete="username" maxlength="254" />
        <div class="field"><label for="password">Kata sandi <span aria-hidden="true">*</span></label><input type="password" id="password" name="password" required autocomplete="current-password" maxlength="1024"></div>
        <button class="button full-width" type="submit">Masuk</button>
    </form>
    <a class="back-link" href="{{ route('registration.create') }}">Kembali ke formulir pendaftaran</a>
</section>
@endsection
