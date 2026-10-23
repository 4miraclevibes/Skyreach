@extends('layouts.app')

@section('title', 'Aktivitas · Skyreach')
@section('crumb', 'Beranda / Aktivitas')

@section('content')
    <div class="page-head">
        <div>
            <h1>Aktivitas</h1>
            <p class="lede">Catatan pembuatan, unggahan, persetujuan, penolakan, dan penghapusan.</p>
        </div>
        @include('partials.pulse')
    </div>
    <section class="table-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Catatan</th>
                        <th>Kontrak</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activities as $activity)
                        <tr>
                            <td>{{ $activity->created_at->translatedFormat('d M Y, H:i') }}</td>
                            <td>{{ $activity->description }}</td>
                            <td>
                                @if ($activity->contract)
                                    <a class="mono" href="{{ route('contracts.index', ['detail' => $activity->contract->id]) }}">{{ $activity->contract->number }}</a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td class="empty" colspan="3">Belum ada aktivitas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer">
            <span>@if ($activities->total()) Menampilkan {{ $activities->firstItem() }}–{{ $activities->lastItem() }} dari {{ $activities->total() }} @endif</span>
            {{ $activities->links('partials.pagination') }}
        </div>
    </section>
@endsection
