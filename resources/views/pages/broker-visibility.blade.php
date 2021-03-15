@extends('layouts.templates.contained')

@section('content')

    <div class="register register--broker">
        <h2>Tapk matomu</h2>

        <broker-become-visible
            :places="{{ $positions }}"
            :initial-durations="{{ $durations }}"
            post_link="{{ route('broker.visibility') }}"
            payment_link="{{ route('payment.view', ['payment_type' => 'visibility']) }}"
        ></broker-become-visible>
    </div>

@endsection
