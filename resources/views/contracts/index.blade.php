@extends('layouts.app')

@section('title', 'Kontrak · Skyreach')
@section('crumb', 'Beranda / Kontrak')
@section('body-class', ($detail || $dialog) ? 'has-overlay' : '')

@section('topbar')
    <label class="search">
        <input form="contract-filters" type="search" name="q" value="{{ request('q') }}" placeholder="Cari nomor kontrak" aria-label="Cari nomor kontrak">
    </label>
    @if (auth()->user()->isStaff())
        <a class="btn btn-gold" href="{{ route('contracts.create') }}">Buat kontrak</a>
    @endif
@endsection

@php
    $filters = request()->except(['detail', 'preview', 'reject', 'approve', 'delete']);
    $sortLink = function ($column) use ($filters) {
        $dir = (request('sort') === $column && request('dir') === 'asc') ? 'desc' : 'asc';
        $query = array_merge($filters, ['sort' => $column, 'dir' => $dir]);
        unset($query['page']);
        return route('contracts.index', $query);
    };
    $segment = function ($status) use ($filters) {
        $query = $filters;
        unset($query['page']);
        if ($status === 'all') {
            unset($query['status']);
        } else {
            $query['status'] = $status;
        }
        return route('contracts.index', $query);
    };
    $open = function (array $extra) use ($filters) {
        return route('contracts.index', array_merge($filters, $extra));
    };
    $closeUrl = route('contracts.index', $filters);
@endphp

@section('content')
    <div class="page-head">
        <div>
            <h1>Kontrak</h1>
            <p class="lede">
                @if (auth()->user()->isSupervisor())
                    Tinjau, setujui, tolak, atau hapus kontrak.
                @else
                    Dokumen yang Anda buat dan yang menunggu persetujuan.
                @endif
            </p>
        </div>
    </div>

    @if (auth()->user()->isSupervisor())
        <div class="segments">
            <a class="{{ $activeStatus === 'all' ? 'is-active' : '' }}" href="{{ $segment('all') }}">Semua <b>{{ $totalContracts }}</b></a>
            <a class="{{ $activeStatus === 'pending' ? 'is-active' : '' }}" href="{{ $segment('pending') }}">Menunggu <b>{{ (int) ($counts['pending'] ?? 0) }}</b></a>
            <a class="{{ $activeStatus === 'approved' ? 'is-active' : '' }}" href="{{ $segment('approved') }}">Disetujui <b>{{ (int) ($counts['approved'] ?? 0) }}</b></a>
            <a class="{{ $activeStatus === 'rejected' ? 'is-active' : '' }}" href="{{ $segment('rejected') }}">Ditolak <b>{{ (int) ($counts['rejected'] ?? 0) }}</b></a>
        </div>
    @endif

    <form id="contract-filters" class="filters" method="GET" action="{{ route('contracts.index') }}">
        @if (request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            <input type="hidden" name="dir" value="{{ request('dir', 'desc') }}">
        @endif
        <label class="field">
            <span>Status</span>
            <select name="status">
                <option value="">Semua</option>
                @foreach (['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $value => $label)
                    <option value="{{ $value }}" {{ $activeStatus === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="field">
            <span>Aktivasi dari</span>
            <input type="date" name="from" value="{{ request('from') }}">
        </label>
        <label class="field">
            <span>sampai</span>
            <input type="date" name="to" value="{{ request('to') }}">
        </label>
        <label class="field">
            <span>Pembuat</span>
            <select name="creator">
                <option value="">Semua</option>
                @foreach ($creators as $creator)
                    <option value="{{ $creator->id }}" {{ (string) request('creator') === (string) $creator->id ? 'selected' : '' }}>{{ $creator->name }}</option>
                @endforeach
            </select>
        </label>
        <button class="btn btn-line" type="submit">Apply</button>
        <a class="btn-text" href="{{ route('contracts.index') }}">Reset</a>
    </form>

    <section class="table-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th><a href="{{ $sortLink('number') }}">Nomor @if (request('sort') === 'number') {{ request('dir') === 'desc' ? '↓' : '↑' }} @endif</a></th>
                        <th><a href="{{ $sortLink('subject') }}">Perihal @if (request('sort') === 'subject') {{ request('dir') === 'desc' ? '↓' : '↑' }} @endif</a></th>
                        <th><a href="{{ $sortLink('activation') }}">Aktivasi @if (request('sort') === 'activation') {{ request('dir') === 'desc' ? '↓' : '↑' }} @endif</a></th>
                        <th><a href="{{ $sortLink('status') }}">Status @if (request('sort') === 'status') {{ request('dir') === 'desc' ? '↓' : '↑' }} @endif</a></th>
                        <th><a href="{{ $sortLink('creator') }}">Dibuat oleh @if (request('sort') === 'creator') {{ request('dir') === 'desc' ? '↓' : '↑' }} @endif</a></th>
                        <th><a href="{{ $sortLink('approver') }}">Disetujui oleh @if (request('sort') === 'approver') {{ request('dir') === 'desc' ? '↓' : '↑' }} @endif</a></th>
                        <th><a href="{{ $sortLink('approved') }}">Tgl disetujui @if (request('sort') === 'approved') {{ request('dir') === 'desc' ? '↓' : '↑' }} @endif</a></th>
                        <th>Dokumen</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contracts as $contract)
                        <tr class="{{ $selectedId === $contract->id ? 'is-selected' : '' }}">
                            <td><a class="mono" href="{{ $open(['detail' => $contract->id]) }}">{{ $contract->number }}</a></td>
                            <td class="subject">{{ $contract->subject }}</td>
                            <td>{{ $contract->activation_date->translatedFormat('d M Y') }}</td>
                            <td><span class="pill pill-{{ $contract->status }}">{{ $contract->statusLabel() }}</span></td>
                            <td>{{ $contract->creator->name }}</td>
                            <td>{{ $contract->approver->name ?? '—' }}</td>
                            <td>{{ $contract->approved_at ? $contract->approved_at->translatedFormat('d M Y') : '—' }}</td>
                            <td><span class="file-chip">{{ $contract->documentChip() }}</span></td>
                            <td>
                                <div class="actions">
                                    <a class="act-detail" href="{{ $open(['detail' => $contract->id]) }}">Detail</a>
                                    @if (auth()->user()->isStaff() && $contract->isPending())
                                        <a class="act-edit" href="{{ route('contracts.edit', $contract) }}">Edit</a>
                                    @endif
                                    @if (auth()->user()->isSupervisor() && $contract->isPending())
                                        <a class="act-approve" href="{{ $open(['approve' => $contract->id]) }}">Setujui</a>
                                        <a class="act-reject" href="{{ $open(['reject' => $contract->id]) }}">Tolak</a>
                                    @endif
                                    @if (auth()->user()->isSupervisor())
                                        <a class="act-delete" href="{{ $open(['delete' => $contract->id]) }}">Hapus</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="empty" colspan="9">Tidak ada kontrak yang cocok dengan filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer">
            <span>
                @if ($contracts->total())
                    Menampilkan {{ $contracts->firstItem() }}–{{ $contracts->lastItem() }} dari {{ $contracts->total() }} kontrak
                @else
                    Tidak ada kontrak
                @endif
            </span>
            {{ $contracts->onEachSide(1)->links('partials.pagination') }}
            @include('partials.pulse')
        </div>
    </section>

    @if ($detail || $dialog)
        <a class="scrim" href="{{ $closeUrl }}" aria-label="Tutup"></a>
    @endif

    @if ($detail)
        <aside class="drawer" role="dialog" aria-modal="true" aria-labelledby="drawer-title">
            <div class="drawer-head">
                <div>
                    <div class="drawer-kicker">
                        <span class="mono">{{ $detail->number }}</span>
                        <span class="pill pill-{{ $detail->status }}">{{ $detail->statusLabel() }}</span>
                    </div>
                    <h2 id="drawer-title">{{ $detail->subject }}</h2>
                </div>
                <a class="icon-btn" href="{{ $closeUrl }}" aria-label="Tutup">✕</a>
            </div>
            <div class="drawer-body">
                <div class="meta-grid">
                    <div>
                        <span>Aktivasi</span>
                        <strong>{{ $detail->activation_date->translatedFormat('d F Y') }}</strong>
                    </div>
                    <div>
                        <span>Dibuat oleh</span>
                        <strong>{{ $detail->creator->name }} · {{ $detail->creator->roleLabel() }}</strong>
                        <span>{{ $detail->created_at->translatedFormat('d M Y') }}, {{ $detail->created_at->format('H.i') }}</span>
                    </div>
                    <div>
                        <span>{{ $detail->status === 'rejected' ? 'Diputuskan oleh' : 'Disetujui oleh' }}</span>
                        <strong>{{ $detail->approver->name ?? 'belum ada' }}</strong>
                    </div>
                    <div>
                        <span>Tanggal keputusan</span>
                        <strong>{{ $detail->approved_at ? $detail->approved_at->translatedFormat('d F Y') : '—' }}</strong>
                    </div>
                </div>

                @if ($detail->rejection_reason)
                    <div class="reason">{{ $detail->rejection_reason }}</div>
                @endif

                <ol class="timeline">
                    <li class="is-done">
                        <i class="mark"></i>
                        <div><strong>Dibuat</strong><div class="muted">{{ $detail->creator->name }}</div></div>
                    </li>
                    <li class="{{ $detail->isPending() ? 'is-current' : 'is-done' }}">
                        <i class="mark"></i>
                        <div><strong>Menunggu tinjauan Supervisor</strong></div>
                    </li>
                    <li class="{{ $detail->isPending() ? 'is-upcoming' : 'is-done' }}">
                        <i class="mark"></i>
                        <div><strong>{{ $detail->isPending() ? 'Disetujui / Ditolak' : $detail->statusLabel() }}</strong></div>
                    </li>
                </ol>

                <h3>Dokumen pendukung</h3>
                @forelse ($detail->documents as $document)
                    <article class="doc-card">
                        <div>
                            <strong>{{ $document->original_name }}</strong>
                            <p>{{ $document->kindLabel() }} · {{ $document->sizeLabel() }}</p>
                        </div>
                        <div class="doc-actions">
                            <a class="doc-btn doc-btn-preview" href="{{ $open(['detail' => $detail->id, 'preview' => $document->id]) }}">Preview</a>
                            @unless ($document->isImage())
                                <a class="doc-btn doc-btn-tab" href="{{ route('documents.preview', [$detail, $document]) }}" target="_blank" rel="noopener">Tab baru</a>
                            @endunless
                            <a class="doc-btn doc-btn-download" href="{{ route('documents.download', [$detail, $document]) }}">Download</a>
                        </div>
                    </article>
                @empty
                    <p class="muted">Belum ada lampiran.</p>
                @endforelse

                @if ($previewDocument)
                    <div class="preview-frame">
                        @if ($previewDocument->isImage())
                            <img src="{{ route('documents.preview', [$detail, $previewDocument]) }}" alt="Preview {{ $previewDocument->original_name }}">
                        @else
                            <div
                                data-pdf-preview="{{ route('documents.preview', [$detail, $previewDocument]) }}"
                                data-pdf-worker="{{ asset('js/pdfjs/pdf.worker.min.js') }}"
                            >
                                <canvas hidden></canvas>
                                <p class="preview-status" data-pdf-status>Memuat preview…</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
            <div class="drawer-foot">
                @if (auth()->user()->isStaff() && $detail->isPending())
                    <a class="btn btn-line" href="{{ route('contracts.edit', $detail) }}">Edit kontrak</a>
                @endif
                @if (auth()->user()->isSupervisor() && $detail->isPending())
                    <a class="btn btn-teal" href="{{ $open(['approve' => $detail->id]) }}">Setujui</a>
                    <a class="btn btn-rose" href="{{ $open(['reject' => $detail->id]) }}">Tolak</a>
                @endif
                @if (auth()->user()->isSupervisor())
                    <a class="btn-text" href="{{ $open(['delete' => $detail->id]) }}">Hapus</a>
                @endif
            </div>
        </aside>
    @endif

    @if ($dialog && $dialogContract)
        <div class="dialog" role="dialog" aria-modal="true">
            @if ($dialog === 'reject')
                <p class="eyebrow">Tolak kontrak</p>
                <h2 class="mono">{{ $dialogContract->number }}</h2>
                <p>{{ $dialogContract->subject }} akan ditandai Ditolak. Staff tidak bisa mengeditnya lagi.</p>
                <form method="POST" action="{{ route('contracts.reject', $dialogContract) }}">
                    @csrf
                    <label for="reason">Alasan</label>
                    <textarea id="reason" name="reason" required placeholder="Contoh: Lampiran NPWP buram, mohon unggah ulang.">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                    <div class="actions">
                        <a class="btn-text" href="{{ $closeUrl }}">Batal</a>
                        <button class="btn btn-rose" type="submit">Tolak kontrak</button>
                    </div>
                </form>
            @elseif ($dialog === 'approve')
                <p class="eyebrow" style="color: var(--teal);">Setujui kontrak</p>
                <h2 class="mono">{{ $dialogContract->number }}</h2>
                <p>{{ $dialogContract->subject }} akan ditandai Disetujui. Staff tidak bisa mengeditnya lagi.</p>
                <form method="POST" action="{{ route('contracts.approve', $dialogContract) }}">
                    @csrf
                    <div class="actions">
                        <a class="btn-text" href="{{ $closeUrl }}">Batal</a>
                        <button class="btn btn-teal" type="submit">Setujui kontrak</button>
                    </div>
                </form>
            @else
                <p class="eyebrow">Hapus kontrak</p>
                <h2 class="mono">{{ $dialogContract->number }}</h2>
                <p>{{ $dialogContract->subject }} dan dokumen pendukungnya akan dihapus.</p>
                <form method="POST" action="{{ route('contracts.destroy', $dialogContract) }}">
                    @csrf
                    @method('DELETE')
                    <div class="actions">
                        <a class="btn-text" href="{{ $closeUrl }}">Batal</a>
                        <button class="btn btn-rose" type="submit">Hapus kontrak</button>
                    </div>
                </form>
            @endif
        </div>
    @endif

    @if ($previewDocument && ! $previewDocument->isImage())
        <script src="{{ asset('js/pdfjs/pdf.min.js') }}"></script>
        <script src="{{ asset('js/pdf-preview.js') }}"></script>
    @endif
@endsection
