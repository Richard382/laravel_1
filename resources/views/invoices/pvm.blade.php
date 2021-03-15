<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 14px; }
        .client-wrapper { height: 60px; }
        .wrapper { height: 130px; }
        .no-border { border: none; }
        .top-row-left {
            width: 400px;
            height: 130px;
            float: left;
        }
        .top-row-right {
            text-align: right;
            width: 400px;
            height: 130px;
            float: right;
        }
        .label {
            font-size: 14px;
            width: 100px;
            height: 60px;
            float: left;
        }
        .label-no-float {
            font-size: 14px;
            width: 100px;
            height: 100px;
        }
        .info {
            width: 300px;
            height: 130px;
            margin-left: 100px;
            float: left;
        }
        .logo {
            width: 300px;
            background-color: #08B973;
            padding: 20px 20px 15px 20px;
            text-align: center;
            margin-bottom: 40px;
        }
        .small-text {
            font-size: 8pt;
        }
        .logo img {
            margin-bottom: 10px;
        }
        .text-center {
            text-align: center;
        }
        table {
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        th, td {
            padding: 5px;
            text-align: center;
        }
    </style>

</head>
<body>

<div class="logo">
    <img src="{{ asset('images/CONT.png') }}" width="180px" alt="logo"><br>
    <span style="color: white;">Greitas NT sandoris</span>
</div>

<div class="wrapper">
    <div class="top-row-left">
        <div class="label">
            Pardavėjas:
        </div>
        <div class="info">
            <strong>
                UAB „Matomumas Vystymas ir optimizavimas“<br>
                305107386 LT1000012320113 <br>
            </strong>
            <br>
            Paliuliškių g. 5, Paliuliškės<br>
        </div>
    </div>
    <div class="top-row-right">
        <strong>
            PVM SĄSKAITA-FAKTŪRA <br>
            Serija CONT Nr. {{ $order->id }} <br>
        </strong>
        <br>
        Data {{ $order->updated_at->format('d-m-Y') }}
    </div>
</div>

<hr>
<div class="client-wrapper">
    <div class="label">Pirkėjas: </div>
    <div><strong>{{ $order->user->name }}</strong></div>
</div>

<table style="width:100%">
    <tr>
        <th>Pavadinimas</th>
        <th>Mato vnt.</th>
        <th>Kiekis</th>
        <th>Kaina Eur</th>
        <th>Suma su PVM</th>
        <th>Suma be PVM</th>
    </tr>
    @php $totalPrice = 0; $totalQty = 0; @endphp

    @foreach($orderedProducts as $product)
        <tr>
            <td>{{ $product->name }}</td>
            <td>vnt</td>
            <td>{{ $product->quantity }}</td>
            <td>{{ \App\Helpers\Shop::formatPrice($product->amount) }}</td>
            <td>{{ \App\Helpers\Shop::formatPrice($product->amount * $product->quantity) }}</td>
            <td>{{ \App\Helpers\Shop::formatPrice(($product->amount * $product->quantity) / 1.21) }}</td>
        </tr>

        @php
            $totalPrice += $product->amount * $product->quantity;
            $totalQty += isset($product->quantity) ? $product->quantity : 1;
        @endphp

    @endforeach
    <tr class="no-border">
        <td class="no-border">Iš viso:</td>
        <td class="no-border"></td>
        <td class="no-border">{{ $totalQty }}</td>
        <td class="no-border"></td>
        <td class="no-border">Suma be PVM <br> PVM suma (21%)</td>
        <td>{{ \App\Helpers\Shop::formatPrice($totalPrice / 1.21) }} <br> {{ \App\Helpers\Shop::formatPrice($totalPrice - $totalPrice / 1.21) }}</td>
    </tr>
    <tr class="no-border">
        <td class="no-border"></td>
        <td class="no-border"></td>
        <td class="no-border"></td>
        <td class="no-border"></td>
        <td class="no-border">Suma su PVM</td>
        <td>{{ \App\Helpers\Shop::formatPrice($totalPrice) }}</td>
    </tr>
</table>

<div class="client-wrapper">
    Sąskaitą išrašė
</div>

<div style="margin-bottom: 30px;">
    Sąskaitą priėmė
</div>

<div class="small-text" style="margin-bottom: 30px;">
    Ačiū, kad naudojatės CONT sistema.
</div>
<div class="small-text" style="margin-bottom: 30px;">
    Tai yra originali PVM sąskaita faktūra, atitinkanti PVM įstatymo 79 ir 80 straipsnių bei LR Vyriausybės nutarimo Nr. 780 "Dėl mokesčiams apskaičiuoti naudojamų apskaitos dokumentų išrašymo ir pripažinimo taisyklių patvirtinimo" nuostatas. Rekomenduojame ją atsispausdinti arba atsisiųsti PDF formatu.
</div>
<div class="text-center small-text">
    <a href="http://cont.lt">www.cont.lt</a> - visais klausimais prašome kreiptis nurodytais kontaktais: <a href="mailto: info@cont.lt">info@cont.lt</a>
</div>

</body>
</html>
