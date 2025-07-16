@extends('layouts.app')

@section('content')
    <h1>Paiement d’une livraison</h1>

    <p>Prix de la livraison : {{ number_format($delivery->price, 2) }} €</p>

    <form method="POST" action="{{ route('delivery.pay', $delivery->id) }}">
        @csrf
        <button type="submit">Payer la livraison</button>
    </form>
@endsection
