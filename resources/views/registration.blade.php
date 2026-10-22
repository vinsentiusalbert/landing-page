<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff">
    <title>Pendaftaran Calon Debitur | Prisma Dana Gorontalo</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v={{ filemtime(public_path('css/landing.css')) }}">
    <script src="{{ asset('js/registration.js') }}" defer></script>
</head>
<body>
<main class="registration-card" aria-labelledby="form-title">
    <header class="form-header">
        <p class="brand-name">Prisma Dana <span>Gorontalo</span></p>
        <p class="form-caption">FORMULIR PENDAFTARAN</p>
    </header>
    @if(session('registered'))
        <div class="success" role="status">
            <h1 id="form-title">Pendaftaran berhasil</h1>
            <p>Data pendaftaran Anda berhasil disimpan.</p>
            <a class="submit-button" href="{{ route('registration.create') }}">Kembali ke formulir</a>
        </div>
    @else
        <div class="form-intro">
            <h1 id="form-title">Pendaftaran Calon Debitur</h1>
            <p>Lengkapi data diri dan informasi usaha Anda.</p>
            <p class="required-note"><span>*</span> Kolom wajib diisi</p>
        </div>
        @if($errors->any())
            <div class="error-summary" role="alert" tabindex="-1">
                <strong>Pendaftaran belum terkirim.</strong>
                <p>Periksa kembali kolom yang ditandai di bawah ini.</p>
            </div>
        @endif
        <form action="{{ route('registration.store') }}" method="post" id="registration-form">
            @csrf
            <fieldset class="form-section">
                <legend><span class="section-number">01</span> Data diri</legend>
                <x-form-field name="nama" label="Nama lengkap" required placeholder="Nama sesuai identitas" autocomplete="name" maxlength="150" />
                <div class="field-grid"><x-form-field name="jenis_kelamin" label="Jenis kelamin" :options="['Pria', 'Wanita']" placeholder="Pilih jenis kelamin" /><x-form-field name="tanggal_lahir" label="Tanggal lahir" type="date" autocomplete="bday" min="1900-01-01" :max="now()->toDateString()" /></div>
                <x-form-field name="no_hp" label="No. HP (WhatsApp)" type="tel" required placeholder="08xxxxxxxxxx" autocomplete="tel" inputmode="tel" maxlength="25" />
                <p class="field-hint">Gunakan nomor aktif agar Anda mudah dihubungi.</p>
            </fieldset>
            <fieldset class="form-section">
                <legend><span class="section-number">02</span> Informasi usaha</legend>
                <div class="field-grid"><x-form-field name="jenis_usaha" label="Jenis usaha" :options="$businessTypes" placeholder="Pilih jenis usaha" /><x-form-field name="lama_usaha_thn" label="Lama usaha (tahun)" type="number" min="0" max="100" step="1" inputmode="numeric" placeholder="Contoh: 3" /></div>
                <x-form-field name="omset_bulanan" label="Omset per bulan" type="number" prefix="Rp" min="0" max="999999999999.99" step="0.01" inputmode="decimal" placeholder="Contoh: 15000000" />
                <x-form-field name="alamat" label="Alamat usaha" type="textarea" placeholder="Jalan, nomor, kelurahan, kecamatan, kota / kabupaten" autocomplete="street-address" maxlength="1000" />
            </fieldset>
            <fieldset class="form-section">
                <legend><span class="section-number">03</span> Kebutuhan pembiayaan</legend>
                <x-form-field name="pinjaman_eksisting" label="Pinjaman Berjalan" type="number" prefix="Rp" min="0" max="999999999999.99" step="0.01" inputmode="decimal" placeholder="Isi 0 jika tidak ada" />
                <x-form-field name="fasilitas_diajukan" label="Fasilitas yang diajukan" :options="$facilities" placeholder="Pilih fasilitas pembiayaan" />
                <x-form-field name="pinjaman_diajukan" label="Jumlah pinjaman diajukan" type="number" prefix="Rp" min="0" max="999999999999.99" step="0.01" inputmode="decimal" placeholder="Contoh: 25000000" />
            </fieldset>
            <button class="submit-button" type="submit"><span class="button-label">Daftar sekarang</span></button>
            <p class="privacy-note"><span>Data Anda hanya digunakan untuk proses pengajuan pembiayaan Prisma Dana Gorontalo.</span></p>
        </form>
    @endif
</main>
</body>
</html>
