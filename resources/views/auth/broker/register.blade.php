@extends('layouts.templates.contained')

@section('content')

<div class="background--white">
    <div class="text-center  font-weight-bold mb-3">
        <a href="{{ route('login') }}">Esate narys? Prisijungti</a>
    </div>
    <div class="register register--broker">
        @include('auth.partials.tabs')

        <!-- <h2>Paslaugų teikėjo registracija</h2> -->
		
		<div class="video-responsive ml-5 mr-5">
			<iframe width="100%" class="mx-auto d-block" src="https://www.youtube.com/embed/V2TNpThEc4o" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		</div>  

        <broker-form
            :languages="{{ $languages }}"
            :property_types="{{ $property_types }}"
            :regions="{{ $regions }}"
            :services="{{ $services }}"
            agreement-link="{{ env('TEST_DATA') ? 'test' : setting('site.agreement_url') }}"
            post-route="{{ route('register.broker') }}"
        >
        </broker-form>

    </div>
</div>
@endsection
 