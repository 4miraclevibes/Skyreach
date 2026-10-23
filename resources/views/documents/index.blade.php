@extends('layouts.app')

@section('title', 'Dokumen · Skyreach')
@section('crumb', 'Beranda / Dokumen')

@section('topbar')
    <form class="search" method="GET" action="{{ route('documents.index') }}">
        <input type="search" name="q" value="{{ $search }}" placeholder="Cari berkas atau nomor" aria-label="Cari dokumen">
    </form>
@endsection

@section('content')
    <div class="page-head">
        <div>
            <h1>Dokumen</h1>
            <p class="lede">Berkas yang diunggah pada kontrak. Bukan modul terpisah dari hak akses kontrak.</p>
        </div>
        @include('partials.pulse')
    </div>
    <section class="table-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Berkas</th>
                        <th>Kontrak</th>
                        <th>Jenis</th>
                        <th>Ukuran</th>
                        <th>Diunggah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($documents as $document)
                        <tr>
                            <td>{{ $document->original_name }}</td>
                            <td>
                                <a class="mono" href="{{ route('contracts.index', ['detail' => $document->contract_id]) }}">{{ $document->contract->number }}</a>
                                <div class="muted">{{ $document->contract->subject }}</div>
                            </td>
                            <td><span class="file-chip">{{ $document->kindLabel() }}</span></td>
                            <td>{{ $document->sizeLabel() }}</td>
                            <td>{{ $document->created_at->translatedFormat('d M Y') }}</td>
                            <td class="actions">
                                <a href="{{ route('documents.preview', [$document->contract_id, $document]) }}" target="_blank" rel="noopener">Preview</a>
                                <a href="{{ route('documents.download', [$document->contract_id, $document]) }}">Download</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="empty" colspan="6">Belum ada dokumen.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer">
            <span>@if ($documents->total()) Menampilkan {{ $documents->firstItem() }}–{{ $documents->lastItem() }} dari {{ $documents->total() }} berkas @endif</span>
            {{ $documents->links('partials.pagination') }}
        </div>
    </section>
@endsection
