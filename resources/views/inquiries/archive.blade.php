@extends('layouts.templates.contained')

@section('content')

    <div class="inquiries">
        @if (Auth::user()->isBroker())
            <broker-inquiry-list
                nofilter
                :current-company-route="'{{ Auth::user()->company->link }}'"
                :pages_number="{{ $inquiries->lastPage() }}"
                :total_visible="{{ $inquiries->perPage() }}"
                :current_page="{{ $inquiries->currentPage() }}"
                :current_items="{{ json_encode($inquiries->items()) }}"
                get-route="{{ route('inquiry.index') }}"
            >
            </broker-inquiry-list>
        @else
            <my-inquiry-list
                :pages_number="{{ $inquiries->lastPage() }}"
                :total_visible="{{ $inquiries->perPage() }}"
                :current_page="{{ $inquiries->currentPage() }}"
                :current_items="{{ json_encode($inquiries->items()) }}"
            >
            </my-inquiry-list>
        @endif
    </div>

@endsection
