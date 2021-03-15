@extends('layouts.templates.contained')

@section('content')

    <div class="payment">
        <h2>Užsakymas</h2>

        @if (session()->has('message'))
            <v-alert
                outlined
                :type="'error'"
                class="error"
            >
                {{ session('message') }}
            </v-alert>
        @endif

        <!--Laukiame patvirtinimo apie apmokėtą užsakymą, kai jis bus patvirtintas, gausite informaciją el. paštu.-->
        Laukiame mokėjimo patvirtinimo. Kai jis bus patvirtintas, gausite kliento informaciją el. paštu bei galėsite ją peržiūrėti 
        <a href="{{url('uzklausos?klientu-uzklausos')}}">savo pasiūlymų sraute</a>
{{--        @if ($order->payment_status === \App\Order::PAYMENT_COMPLETED)--}}
{{--            {!! $order->getCompletedInfo() !!}--}}
{{--        @endif--}}

        <div class="payment__info">
            <table class="simple">
                <tbody>
                    <tr>
                        <th>Informacija</th>
                        <td>{{ $order->getProductName() }}</td>
                    </tr>
                    <tr>
                        <th>Kaina</th>
                        <td>{{ $order->getProductPrice() }}</td>
                    </tr>
                    <tr>
                        <th>Mokėjimo būdas</th>
                        <td>{{ strtoupper($order->payment_method) }}</td>
                    </tr>
                </tbody>
            </table>
            <br>
            <div class="text-center">
                <a href="{{url('uzklausos?klientu-uzklausos')}}"><button class="v-btn v-size--default primary">GRĮŽTI Į PASIŪLYMUS</button></a>
            </div>
        </div>
    </div>

@endsection
