@extends('layouts.app')

@section('title', ($contract ? 'Edit kontrak' : 'Kontrak baru').' · Skyreach')
@section('crumb', 'Beranda / Kontrak / '.($contract ? 'Edit' : 'Baru'))

@section('content')
    <div class="form-wrap">
        <form class="form-card panel" method="POST" action="{{ $contract ? route('contracts.update', $contract) : route('contracts.store') }}" enctype="multipart/form-data">
            @csrf
            @if ($contract)
                @method('PUT')
            @endif

            @if ($contract)
                <div class="banner">Masih menunggu. Perubahan dikunci setelah Supervisor menyetujui atau menolak.</div>
            @endif

            <h1>{{ $contract ? 'Edit kontrak' : 'Kontrak baru' }}</h1>
            <p class="lede">Nomor dibuat otomatis dari bulan dan urutan. Contoh INV/10/05.</p>

            <div class="stack">
                <label>Nomor kontrak</label>
                <div class="readonly mono">
                    <span>{{ $previewNumber }}</span>
                    <span aria-hidden="true">🔒</span>
                </div>
                <p class="caption">
                    @if ($contract)
                        Nomor terkunci
                    @else
                        Otomatis · {{ now()->translatedFormat('F Y') }} · urutan {{ substr($previewNumber, -2) }}
                    @endif
                </p>
            </div>

            <div class="stack">
                <label for="subject">Perihal</label>
                <input id="subject" name="subject" value="{{ old('subject', $contract->subject ?? '') }}" placeholder="Contoh: Sewa kapasitas VSAT site Pontianak" required>
                @error('subject')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="stack">
                <label for="activation_date">Tanggal aktivasi</label>
                <input id="activation_date" type="date" name="activation_date" value="{{ old('activation_date', $contract ? $contract->activation_date->format('Y-m-d') : '') }}" required>
                @error('activation_date')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="stack">
                <label>Dibuat oleh</label>
                <div class="readonly">{{ $contract ? $contract->creator->name.' · '.$contract->creator->roleLabel() : auth()->user()->name.' · '.auth()->user()->roleLabel() }}</div>
            </div>

            <div class="stack">
                <label>Dokumen pendukung</label>
                <div data-remove-holder>
                    @if ($contract)
                        @foreach ($contract->documents as $document)
                            <div class="file-row">
                                <span>{{ $document->original_name }}</span>
                                <button type="button" data-remove-doc="{{ $document->id }}" aria-label="Hapus {{ $document->original_name }}">Lepas</button>
                            </div>
                        @endforeach
                    @endif
                </div>
                <label class="dropzone">
                    <input data-files type="file" name="documents[]" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/png,image/jpeg" multiple>
                    <strong>Letakkan PDF atau gambar di sini</strong>
                    <span class="caption">PDF, JPG, atau PNG · bisa lebih dari satu file</span>
                    <span class="caption" data-file-list></span>
                </label>
                @foreach ($errors->get('documents.*') as $messages)
                    @foreach ($messages as $message)
                        <p class="field-error">{{ $message }}</p>
                    @endforeach
                @endforeach
            </div>

            <div class="form-actions">
                <a class="btn-text" href="{{ $contract ? route('contracts.index', ['detail' => $contract->id]) : route('contracts.index') }}">Batal</a>
                <button class="btn btn-gold" type="submit">{{ $contract ? 'Simpan perubahan' : 'Simpan kontrak' }}</button>
            </div>
            <p class="form-note">Kontrak masuk sebagai Menunggu dan hanya bisa diedit sebelum disetujui atau ditolak.</p>
        </form>
    </div>
@endsection
