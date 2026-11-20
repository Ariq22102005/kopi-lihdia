@extends('layouts.admin')
@section('title', 'Reservasi')

@section('content')
<div class="kl-pagehead">
    <div>
        <h1>Reservasi</h1>
        <p>
            Kelola reservasi meja dari pelanggan.
            @if($pendingCount > 0)<strong class="text-body">{{ $pendingCount }} menunggu konfirmasi.</strong>@endif
        </p>
    </div>
</div>

<form method="GET" action="{{ route('admin.reservations.index') }}" class="ad-card ad-toolbar">
    <input type="search" name="q" value="{{ $search }}" class="form-control ad-grow" placeholder="Cari kode, nama, atau nomor telepon" aria-label="Cari reservasi">
    <select name="status" class="form-select" aria-label="Status reservasi">
        <option value="">Semua status</option>
        @foreach(\App\Models\Reservation::STATUSES as $v => $l)
            <option value="{{ $v }}" @selected(request('status') === $v)>{{ $l }}</option>
        @endforeach
    </select>
    <input type="date" name="date" value="{{ request('date') }}" class="form-control" aria-label="Tanggal">
    <button class="btn btn-dark"><x-icon name="search" :size="16" /> Filter</button>
</form>

<div class="ad-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr><th>Kode</th><th>Pemesan</th><th>Jadwal</th><th>Orang</th><th>Catatan</th><th>Status</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
            @forelse($reservations as $r)
                @php
                    $color = ['pending' => 'warning', 'confirmed' => 'success', 'cancelled' => 'danger'][$r->status] ?? 'secondary';
                    $wa = preg_replace('/^0/', '62', preg_replace('/\D+/', '', $r->phone));
                @endphp
                <tr>
                    <td class="fw-semibold text-nowrap">{{ $r->code }}</td>
                    <td>
                        <div class="fw-semibold">{{ $r->name }}</div>
                        <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener" class="small">{{ $r->phone }}</a>
                    </td>
                    <td class="text-nowrap">{{ $r->date->format('d M Y') }}<div class="small text-muted">{{ $r->time }}</div></td>
                    <td>{{ $r->guests }}</td>
                    <td class="small text-muted" style="max-width: 220px">{{ $r->notes ?: '-' }}</td>
                    <td><span class="kl-status kl-st-{{ $color }}">{{ \App\Models\Reservation::STATUSES[$r->status] ?? $r->status }}</span></td>
                    <td class="text-end text-nowrap">
                        @if($r->status !== 'confirmed')
                            <form method="POST" action="{{ route('admin.reservations.status', $r) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="confirmed">
                                <button class="btn btn-sm btn-dark"><x-icon name="check" :size="16" /> Konfirmasi</button>
                            </form>
                        @endif
                        @if($r->status !== 'cancelled')
                            <form method="POST" action="{{ route('admin.reservations.status', $r) }}" class="d-inline"
                                  onsubmit="return confirm('Batalkan reservasi ini?')">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="cancelled">
                                <button class="btn btn-sm btn-outline-danger"><x-icon name="x" :size="16" /> Batalkan</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5">Belum ada reservasi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $reservations->links('pagination::bootstrap-5') }}</div>
@endsection
