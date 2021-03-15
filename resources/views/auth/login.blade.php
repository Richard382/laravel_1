@extends('layouts.templates.contained')

@section('content')
<div class="background--white">
    <div class="register register--broker ml-5 mr-5 mt-10 mb-5">
        <h2>Prisijungimas</h2>
        @csrf
        <login-form
            reset-password-link="{{ route('password.refresh') }}"
            post-route="{{ route('login') }}"
        ></login-form>

        <div class=" mt-8 font-weight-bold">
            <h2><a href="{{ route('register.regular') }}">Neturite vartotojo?</a></h2>
        </div>
        <div class="text-center ">
        <a href="registracija/vartotojas" class="regBtn">REGISTRACIJA NT UŽSAKOVAMS</a>
        </div>
        <div class="text-center mt-6 ">
        <a href="registracija/brokeris" class="regBtn">REGISTRACIJA NT SPECIALISTAMS</a>
        </div>
    </div>
</div>

@endsection

