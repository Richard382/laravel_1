@extends('layouts.templates.contained')

@section('content')

    <div class="register register--broker">
        <h2>Profilio redagavimas</h2>

        @if (Auth::user()->isBroker() || $becamebroker)
            <broker-profile-form
                :user="{{ Auth::user() }}"
                :company="{{ Auth::user()->company?Auth::user()->company->appendProfileData():'{}' }}"
                :languages="{{ $languages }}"
                :property_types="{{ $property_types }}"
                :regions="{{ $regions }}"
                :services="{{ $services }}"
                post-route="{{ route('profile.me') }}"
                :becamebroker="{{$becamebroker?1:0}}"
            ></broker-profile-form>
        @else
            <user-profile-form
                :user="{{ Auth::user() }}"
                post-route="{{ route('profile.me') }}"
            >
            </user-profile-form>
        @endif
    </div>

@endsection
