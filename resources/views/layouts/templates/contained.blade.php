@extends('layouts.app')

@section('template')

    <v-content
        class="v-content--cont"
    >
        <v-container>
            @yield('content')
        </v-container>
    </v-content>

@endsection
