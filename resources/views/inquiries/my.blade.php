@extends('layouts.templates.full-width')

@section('content')

    <div class="inquiries">
        @include('inquiries.partials.tabs')

        <my-inquiry-list
            :pages_number="{{ $inquiries->lastPage() }}"
            :total_visible="{{ $inquiries->perPage() }}"
            :current_page="{{ $inquiries->currentPage() }}"
            :current_items="{{ json_encode($inquiries->items()) }}"
            :isbroker="{{(Auth::user()->isBroker())?1:0}}"
            homepage_url="{{url('/')}}"
        >
        </my-inquiry-list>
    </div>

@endsection
