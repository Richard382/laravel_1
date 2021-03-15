@extends('layouts.templates.contained')

@section('content')

    <div class="inquiries mt-10">
        @if ($inquiry)
            <h2 class="headline">Rask tinkamiausią nekilnojamo turto partnerį savo poreikiams <small>Užklausa nr. {{ $inquiry->id }}</small></h2>
        @else
            <h2 class="headline">Brokerių sąrašas</h2>
        @endif

        <broker-list
            :prop_items="{{ $companies }}"
            :regions="{{ $regions }}"
            :types="{{ $property_types }}"
            :inquiry_available="{{ $inquiry ? $inquiry : 'false' }}"
            :top_number="{{ $top_number }}"
            get-route="{{ route('companies.index') }}"
        ></broker-list>

        @if ($inquiry)
            <make-connection
                :inquiry="{{ $inquiry->id }}"
            ></make-connection>
        @endif
    </div>

@endsection
