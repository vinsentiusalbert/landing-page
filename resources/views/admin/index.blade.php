@extends('admin.layout')
@section('title', 'Data Pendaftar')
@section('content')
<div class="page-heading"><div><p class="eyebrow">PENDAFTARAN CALON DEBITUR</p><h1>Data pendaftar</h1><p class="muted">Tinjau informasi dan kebutuhan pembiayaan calon debitur.</p></div><a class="button secondary" href="{{ route('registration.create') }}" target="_blank" rel="noopener">Lihat form publik &nearr;</a></div>
<div class="stats" aria-label="Ringkasan seluruh pendaftaran">
    <div class="panel stat"><span>Total pendaftar</span><strong>{{ number_format($total, 0, ',', '.') }}</strong></div>
    <div class="panel stat"><span>Pendaftar hari ini</span><strong>{{ number_format($today, 0, ',', '.') }}</strong></div>
    <div class="panel stat"><span>Total nominal diajukan</span><strong class="amount">Rp {{ number_format($requested, 0, ',', '.') }}</strong><small>Akumulasi pengajuan, bukan persetujuan.</small></div>
</div>
<section class="panel records" aria-labelledby="records-title">
    <div class="records-heading"><h2 id="records-title">Daftar pendaftaran <span class="count">{{ $registrations->total() }}</span></h2><a class="button" href="{{ route('admin.export', request()->only('q', 'from', 'to')) }}">Ekspor CSV</a></div>
    <form class="filters" method="get" action="{{ route('admin.index') }}">
        <div class="field search"><label for="q">Cari pendaftar</label><input id="q" name="q" value="{{ is_string(request('q')) ? request('q') : '' }}" placeholder="Nama, nomor HP, atau jenis usaha" maxlength="150" type="search"></div>
        <div class="field"><label for="from">Dari tanggal</label><input id="from" name="from" type="date" value="{{ is_string(request('from')) ? request('from') : '' }}"></div>
        <div class="field"><label for="to">Sampai tanggal</label><input id="to" name="to" type="date" value="{{ is_string(request('to')) ? request('to') : '' }}"></div>
        <button class="button secondary" type="submit">Terapkan</button><a class="reset-link" href="{{ route('admin.index') }}">Reset</a>
    </form>
    <div class="table-wrap"><table><caption class="sr-only">Daftar calon debitur sesuai filter</caption><thead><tr><th>Pendaftar</th><th>WhatsApp / HP</th><th>Jenis usaha</th><th>Pinjaman diajukan</th><th>Tanggal daftar</th><th><span class="sr-only">Tindakan</span></th></tr></thead><tbody>
    @forelse($registrations as $registration)
        <tr><td><strong>{{ $registration->nama }}</strong><small>#{{ $registration->id }}</small></td><td>{{ $registration->no_hp }}</td><td>{{ $registration->jenis_usaha ?? '—' }}</td><td class="nowrap">{{ $registration->pinjaman_diajukan !== null ? 'Rp '.number_format($registration->pinjaman_diajukan, 0, ',', '.') : '—' }}</td><td class="nowrap">{{ $registration->created_at->format('d/m/Y') }}<small>{{ $registration->created_at->format('H:i') }} WIB</small></td><td><a class="detail-link" href="{{ route('admin.registrations.show', $registration) }}">Detail &rarr;</a></td></tr>
    @empty
        <tr><td colspan="6" class="empty"><strong>{{ request()->hasAny(['q', 'from', 'to']) ? 'Tidak ada pendaftar yang cocok.' : 'Belum ada pendaftaran.' }}</strong><p>{{ request()->hasAny(['q', 'from', 'to']) ? 'Ubah kata kunci atau rentang tanggal pencarian.' : 'Data akan tampil di sini setelah formulir publik dikirim.' }}</p></td></tr>
    @endforelse
    </tbody></table></div>
    @if($registrations->hasPages())
        <nav class="pagination" aria-label="Halaman pendaftar"><span>{{ $registrations->firstItem() }}–{{ $registrations->lastItem() }} dari {{ $registrations->total() }} pendaftar</span><div>@if($registrations->onFirstPage())<span aria-disabled="true">Sebelumnya</span>@else<a href="{{ $registrations->previousPageUrl() }}" rel="prev">Sebelumnya</a>@endif <span>{{ $registrations->currentPage() }} / {{ $registrations->lastPage() }}</span> @if($registrations->hasMorePages())<a href="{{ $registrations->nextPageUrl() }}" rel="next">Berikutnya</a>@else<span aria-disabled="true">Berikutnya</span>@endif</div></nav>
    @endif
</section>
@endsection
