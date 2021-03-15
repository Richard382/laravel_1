@extends('layouts.templates.contained')

@section('content')

    <div class="register">
        <h2>Baikite pildyti informaciją sėkmingai užklausai!</h2>
        <inquiry-form
            :cities="{{ $cities }}"
            price_from="{{ $price_from }}"
            price_to="{{ $price_to }}"
            :location="{{ collect($location)->toJson() }}"
            :services="{{ $services }}"
            :service="{{ $service ? $service : 'false' }}"
            :property_types_pre="{{ $property_types_pre ? $property_types_pre : 'false' }}"
            :property_types="{{ $property_types ? $property_types : 'false' }}"
            :properties_pre="{{ $properties_pre ? $properties_pre : 'false' }}"
            agreement-link="{{ env('TEST_DATA') ? 'test' : setting('site.agreement_url') }}"
            {{ Request::get('contact_company') ? sprintf('contact-company=%s', Request::get('contact_company')) : '' }}
            post-route="{{ route('inquiry.index') }}"
        >
        </inquiry-form>
    </div>

@endsection
