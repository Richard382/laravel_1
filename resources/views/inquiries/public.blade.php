@extends('layouts.templates.full-width')

@section('content')

<div class="inquiries">
    <v-card
            class="mx-auto"
            max-width="1200"
          >
        <inquiry-list
            :regions="{{ $regions }}"
            :types="{{ $property_types }}"
            :service_types="{{ collect([['id' => \App\Service::TYPE_OTHER, 'name' => 'Kita'], ['id' => \App\Service::TYPE_OFFER, 'name' => 'Siūlo'], ['id' => \App\Service::TYPE_LOOKIN, 'name' => 'Ieško']]) }}"
            :pages_number="{{ $inquiries->lastPage() }}"
            :total_visible="{{ $inquiries->perPage() }}"
            :current_page="{{ $inquiries->currentPage() }}"
            :current_items="{{ json_encode($inquiries->items()) }}"
        >
        </inquiry-list>
    </v-card>
</div>

@endsection
