@extends('layouts.templates.contained')

@section('content')

    @if(isset($invalid) && $invalid)
        <div class="register register--broker">
            <h2 style="color:#cc0000;">Nuoroda nebegalioja!</h2>
            @if(Auth::check())
            Eikite į savo <a href="{{ route('profile.me') }}">profilį</a>.
            @else
            Eikite į  <a href="{{ url('prisijungti') }}">prisijungimo puslapį</a>.
            @endif
        </div>
    @else
        <div class="register register--broker">
            <h2>Sėkmingai patvirtintas!</h2>
            Jūsų el.paštas buvo sėkmingai patvirtintas, 
            <!--dabar galite <a href="{{ route('login') }}">prisijungti</a> prie sistemos.-->
            @if(Auth::check())
            Eikite į savo <a href="{{ route('profile.me') }}">profilį</a>.
            @else
            Eikite į  <a href="{{ url('prisijungti') }}">prisijungimo puslapį</a>.
            @endif
        </div>
    @endif
<!--http://127.0.0.1:8000/patvirtinti-vartotoja/CLsoo3IvduJr7SdqIvvd7uuKpGrZ1MDxOk69FvSAJ6v6C3zxRAiB7WcyE3scd-->
@endsection
