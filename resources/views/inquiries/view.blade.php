@extends('layouts.templates.contained')

@section('content')

    <div class="inquiry view">
        <h2>{!! Request::get('fresh') ? 'Jūsų Užklausa <strong>Nr. ' . $inquiry->id . '</strong> sukurta' : 'Užklausa <strong>Nr. ' . $inquiry->id . '</strong>' !!} <small>Susidomėjo: <span id="offers_total">{{ $offers->total() }}</span></small></h2>

        <div class="inquiry-info">
            <strong class="inquiry-info__service">Paslauga, objektas, lokacija, kaina:</strong>
            <span class="inquiry-info__property_type">{{ $inquiry->sum_up }}</span>
        </div>

        <div class="inquiry-info">
            <strong class="inquiry-info__service">Užklausa</strong>
            <span class="inquiry-info__property_type">{!! nl2br($inquiry->requirements) !!}</span>
        </div>

        @if (Request::get('fresh'))
            <div class="inquiry__info">
                Šiame puslapyje matysite paslaugos teikėjų pasiūlymus. <u>Pasiūlymai siunčiami Jūsų nurodytu el. pašto adresu</u>. {{ Auth::check() ? 'Šis puslapis pasiekiamas ir iš MENIU' : '' }}
            </div>
        @endif

        <offers-list
            :pages_number="{{ $offers->lastPage() }}"
            :total_visible="{{ $offers->perPage() }}"
            :current_page="{{ $offers->currentPage() }}"
            :current_items="{{ json_encode($offers->items()) }}"
            :accepted="{{ $accepted }}"
            {{ Request::get('fresh') ? "fresh" : '' }}
            inquiry="{{ $inquiry->getID() }}"
            create_inquiry_link="{{ route('inquiry.create') }}"
            :chose_broker_link="'{{ route('companies.index') . ($inquiry->token ? sprintf('/?inquiry_token=%s', $inquiry->token) : sprintf('/?inquiry=%s', $inquiry->id)) }}'"
        >
        </offers-list>
        <accept-offer
            inquiryID="{{ $inquiry->getID() }}"
        ></accept-offer>
    </div>

@endsection
