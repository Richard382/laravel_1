@extends('layouts.templates.full-width')

@section('content')
    <div class="inquiries main_contents">
        @include('inquiries.partials.tabs')

        <broker-inquiry-list
            :regions="{{ $regions }}"
            :types="{{ $property_types }}"
            :service_types="{{ collect([['id' => \App\Service::TYPE_OTHER, 'name' => 'Kita'], ['id' => \App\Service::TYPE_OFFER, 'name' => 'Siūlo'], ['id' => \App\Service::TYPE_LOOKIN, 'name' => 'Ieško']]) }}"
            :pages_number="{{ $inquiries->lastPage() }}"
            :total_visible="{{ $inquiries->perPage() }}"
            :current_page="{{ $inquiries->currentPage() }}"
            :current_items="{{ json_encode($inquiries->items()) }}"
            get-route="{{ route('inquiry.index') . ( Request::has('klientu-uzklausos') ? '?klientu-uzklausos&expand='.$expand : '?expand='.$expand ) }}"
            expand={{(int)($expand?:0)}}
        >
        </broker-inquiry-list>
    </div>

@endsection
