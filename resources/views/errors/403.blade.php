@extends('layouts.app')

@section('title', 'Akses ditolak · Skyreach')
@section('crumb', 'Akses ditolak')

@section('content')
    <section class="panel narrow">
        <p class="eyebrow">403</p>
        <h1>Akses ditolak</h1>
        <p class="lede">Akun ini tidak dapat melakukan tindakan tersebut.</p>
        <a class="btn btn-gold" href="{{ route('dashboard') }}">Kembali ke dasbor</a>
    </section>
@endsection
