@extends('layouts.app')

@section('title', 'Dashboard · Skyreach')
@section('crumb', 'Beranda / Dashboard')

@section('topbar')
    <form class="search" method="GET" action="{{ route('contracts.index') }}">
        <input type="search" name="q" placeholder="Cari nomor kontrak" aria-label="Cari nomor kontrak">
    </form>
@endsection

@section('content')
    <div class="page-head">
        <div>
            <h1>Dashboard</h1>
            <p class="lede">Ringkasan kontrak bulan {{ $monthLabel }}.</p>
        </div>
        @include('partials.pulse')
    </div>

    <section class="stats">
        <article class="stat accent-gold">
            <a href="{{ route('contracts.index', ['status' => 'pending']) }}">
                <span>Menunggu tinjauan</span>
                <b>{{ $pending }}</b>
                <small>Menunggu keputusan Supervisor</small>
            </a>
        </article>
        <article class="stat accent-teal">
            <a href="{{ route('contracts.index', ['status' => 'approved']) }}">
                <span>Disetujui bulan ini</span>
                <b>{{ $approvedMonth }}</b>
                <small>Keputusan dalam {{ $monthLabel }}</small>
            </a>
        </article>
        <article class="stat accent-rose">
            <a href="{{ route('contracts.index', ['status' => 'rejected']) }}">
                <span>Ditolak bulan ini</span>
                <b>{{ $rejectedMonth }}</b>
                <small>Perlu diperbaiki sebelum diajukan ulang</small>
            </a>
        </article>
        <article class="stat accent-navy">
            <a href="{{ route('documents.index') }}">
                <span>Dokumen masuk hari ini</span>
                <b>{{ $documentsToday }}</b>
                <small>{{ $latestDocument ? 'Lampiran terbaru '.$latestDocument->created_at->diffForHumans() : 'Belum ada lampiran' }}</small>
            </a>
        </article>
    </section>

    <section class="split">
        <article class="stack-card">
            <div class="head-row">
                <h2>Perlu tindakan</h2>
                <a href="{{ route('contracts.index', ['status' => 'pending']) }}">Lihat semua</a>
            </div>
            @forelse ($needsAction as $contract)
                <div class="action-row">
                    <div>
                        <strong class="mono">{{ $contract->number }}</strong>
                        <p>{{ $contract->subject }}</p>
                        <p>{{ $contract->creator->name }} · {{ $contract->created_at->diffForHumans() }}</p>
                    </div>
                    <a class="btn btn-line" href="{{ route('contracts.index', ['detail' => $contract->id]) }}">Tinjau</a>
                </div>
            @empty
                <p class="lede">Tidak ada kontrak yang menunggu.</p>
            @endforelse
        </article>
        <div>
            <article class="stack-card">
                <h2>Kontrak menurut status</h2>
                @foreach (['approved' => 'Disetujui', 'pending' => 'Menunggu', 'rejected' => 'Ditolak'] as $key => $label)
                    <div class="bar-row">
                        <span>{{ $label }}</span>
                        <div class="bar"><i class="bar-{{ $key === 'approved' ? 'teal' : ($key === 'pending' ? 'gold' : 'rose') }}" style="width: {{ (int) round(($statusCounts[$key] / $statusMax) * 100) }}%"></i></div>
                        <b>{{ $statusCounts[$key] }}</b>
                    </div>
                @endforeach
            </article>
            <article class="stack-card" style="margin-top: 14px;">
                <div class="head-row">
                    <h2>Aktivitas terakhir</h2>
                    <a href="{{ route('activities.index') }}">Lihat semua</a>
                </div>
                @forelse ($activities as $activity)
                    <div class="activity-row">
                        <div>
                            <strong>{{ $activity->description }}</strong>
                            <p>{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="lede">Belum ada aktivitas.</p>
                @endforelse
            </article>
        </div>
    </section>
@endsection
