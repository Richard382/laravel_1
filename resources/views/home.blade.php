@extends('layouts.templates.full-width')

@section('content')
<div class="main_content">
    <center-box
    {{ $preset_city ? sprintf(':preset_cities=%s', $preset_city) : '' }}
            {{ $preset_service ? sprintf(':preset_service=%s', $preset_service) : '' }}
            {{ $preset_property_type ? sprintf(':preset_property_type=%s', $preset_property_type) : '' }}
            {{ $preset_properties ? sprintf(':preset_properties=%s', $preset_properties) : '' }}
            preset_price_to="{{ $preset_price_to }}"
            preset_price_from="{{ $preset_price_from }}"
            :cities="{{ $cities }}"
            :services="{{ $services }}"
            :property_types="{{ $property_types->toJson() }}"
            redirect-route="{{ route('inquiry.create') }}"
    ></center-box>
    <content-list></content-list>
</div>
@endsection
