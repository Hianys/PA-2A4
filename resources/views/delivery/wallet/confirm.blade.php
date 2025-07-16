@extends('layouts.app')

@section('content')
    <h1>Confirmer la livraison</h1>

    <p>Livraison #{{ $delivery->id }} - Montant : {{ number_format($delivery->price, 2) }} €</p>

    <form method="POST" action="{{ route('delivery.confirm', $delivery->id) }}">
        @csrf
        <button type="submit">Confirmer la livraison</button>
    </form>
@endsection
