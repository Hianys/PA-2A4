<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture - Livraison #{{ $annonce->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 14px; line-height: 1.5; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        h1, h2 { margin: 0; padding: 0; }
    </style>
</head>
<body>

<h1>Facture Livraison</h1>
<p><strong>Annonce #{{ $annonce->id }}</strong></p>
<p><strong>Titre :</strong> {{ $annonce->title }}</p>
<p><strong>Date :</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
<p><strong>Montant total :</strong> {{ number_format($total, 2) }} €</p>

<h2 style="margin-top: 30px;">Répartition des paiements</h2>

<table>
    <thead>
    <tr>
        <th>Livreur</th>
        <th>Distance (km)</th>
        <th>Part (%)</th>
        <th>Montant (€)</th>
    </tr>
    </thead>
    <tbody>
    @foreach($parts as $part)
        <tr>
            <td>{{ $part['livreur'] }}</td>
            <td>{{ number_format($part['distance'], 2) }}</td>
            <td>{{ number_format($part['part'], 1) }}%</td>
            <td>{{ number_format($part['montant'], 2) }} €</td>
        </tr>
    @endforeach
    </tbody>
</table>

<p style="margin-top: 30px;">Merci pour votre confiance.</p>

</body>
</html>
