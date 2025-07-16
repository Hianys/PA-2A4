@extends('layouts.app')

@section('content')
    <h1>Opération réussie !</h1>
    <a href="{{ route('wallet.index') }}">Retour au portefeuille</a>
@endsection
