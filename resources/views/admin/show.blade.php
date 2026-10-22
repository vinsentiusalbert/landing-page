@extends('admin.layout')
@section('title', 'Detail Pendaftar')
@section('content')
<a class="back-link" href="{{ route('admin.index') }}">&larr; Kembali ke daftar pendaftar</a>
<div class="page-heading"><div><p class="eyebrow">PENDAFTARAN #{{ $registration->id }}</p><h1>{{ $registration->nama }}</h1><p class="muted">Diterima {{ $registration->created_at->format('d/m/Y, H:i') }} WIB</p></div></div>
<div class="detail-grid">
    <section class="panel detail"><h2>Data diri</h2><dl><dt>Nama lengkap</dt><dd>{{ $registration->nama }}</dd><dt>No. HP (WhatsApp)</dt><dd>{{ $registration->no_hp }}</dd><dt>Jenis kelamin</dt><dd>{{ $registration->jenis_kelamin ?? 'Belum diisi' }}</dd><dt>Tanggal lahir</dt><dd>{{ $registration->tanggal_lahir?->format('d/m/Y') ?? 'Belum diisi' }}</dd></dl></section>
    <section class="panel detail"><h2>Informasi usaha</h2><dl><dt>Jenis usaha</dt><dd>{{ $registration->jenis_usaha ?? 'Belum diisi' }}</dd><dt>Lama usaha</dt><dd>{{ $registration->lama_usaha_thn !== null ? $registration->lama_usaha_thn.' tahun' : 'Belum diisi' }}</dd><dt>Omset per bulan</dt><dd>{{ $registration->omset_bulanan !== null ? 'Rp '.number_format($registration->omset_bulanan, 2, ',', '.') : 'Belum diisi' }}</dd><dt>Alamat usaha</dt><dd class="address">{{ $registration->alamat ?? 'Belum diisi' }}</dd></dl></section>
    <section class="panel detail"><h2>Kebutuhan pembiayaan</h2><dl><dt>Pinjaman Berjalan</dt><dd>{{ $registration->pinjaman_eksisting !== null ? 'Rp '.number_format($registration->pinjaman_eksisting, 2, ',', '.') : 'Belum diisi' }}</dd><dt>Fasilitas yang diajukan</dt><dd>{{ $registration->fasilitas_diajukan ?? 'Belum diisi' }}</dd><dt>Jumlah pinjaman diajukan</dt><dd class="highlight">{{ $registration->pinjaman_diajukan !== null ? 'Rp '.number_format($registration->pinjaman_diajukan, 2, ',', '.') : 'Belum diisi' }}</dd></dl></section>
</div>
@endsection
