@extends('layouts.templates.contained')

@section('content')
<div class="">
    <div class="company background--white mt-10">
        <div class="mr-5 ml-5 mt-20 company__brand">
            <div class="company__brand-logo">
                @if ($company->avatar_url)
                    <v-img
                        src="{{ $company->avatar_url }}"
                        width="100"
                        height="100"
                    >
                    </v-img>
                @endif
            </div>
            <div class="company__brand-info">
                <v-rating
                    readonly
                    half-increments
                    dense
                    size="25"
                    color="primary"
                    background-color="primary"
                    value="{{ $company->rating }}"
                >
                </v-rating>
                <h2 class="company__brand-info-headline">{{ $company->name }}</h2>
            </div>
        </div>
        <div class="mr-5 ml-5 company__objects">
            <div class="label-item">Segmentas, objektas, paslaugos, lokacija, kainos:</div>

            @foreach($spheres as $sphere)
                <div class="company__objects-item">
                    {{ isset($sphere->type->name) ? $sphere->type->name : 'nėra' }} - {{ isset($sphere->property->name) ? $sphere->property->name : 'nėra' }} - {{ isset($sphere->services) && $sphere->services->pluck('type_broker')->implode(', ') ? $sphere->services->pluck('type_broker')->implode(', ') : 'nėra' }} - {{ isset($sphere->regions) && $sphere->regions->pluck('name')->implode(', ') ? $sphere->regions->pluck('name')->implode(', ') : 'nėra' }} - €{{ $sphere->price_from . '-' . $sphere->price_to }}
                </div>
            @endforeach
        </div>
        <div class="mr-5 ml-5 company__languages">
            <div class="label-item">Kalbos:</div>
            <p>{{ $languages->pluck('name')->implode(', ') }}</p>
        </div>
        <div class="mr-5 ml-5 company__description">
            <div class="label-item">Apie paslaugos tiekėja:</div>
            <p>{{ nl2br($company->description) }}</p>
        </div>
        <div class="mr-5 ml-5 company__actions">

            @if ($company->representor)
                <v-btn
                    color="primary"
                    width="100%"
                    class="v-btn--submit"
                    href="{{ route('inquiry.create') }}?contact_company={{ $company->id }}"
                >
                    Susisiekti
                </v-btn>
                <v-btn
                    color="primary"
                    width="100%"
                    class="v-btn--submit"
                    href="tel:{{ $company->representor->phone }}"
                >
                    Tel. Nr.: {{ $company->representor->phone }}
                </v-btn>
                <v-btn
                    color="dark-blue"
                    width="100%"
                    class="v-btn--submit"
                    href="mailto:{{ $company->representor->email }}"
                >
                    El. p.: <span class="text-lowercase">{{ $company->representor->email }}</span>
                </v-btn>
            @else
                <p class="text-center mt-5">Įmonė neturi reprezentuojančio asmens!</p>
            @endif

        </div>

        @if ( ! $comments->isEmpty() )
            <div class="company__comments joined-items">
                @foreach($comments as $comment)
                    <div class="joined-items__item rating-item">
                        @if ($loop->first)
                            <span class="joined-items__item-label label-item">Komentarai:</span>
                        @endif
                        @if(!empty($comment->inquiry))
                        <div class="rating-item__info">
                            <div class="rating-item__info-name">
                                {{ $comment->inquiry->getCreatorName() }}
                            </div>
                            <div class="rating-item__info-rating">
                                <v-rating
                                    readonly
                                    half-increments
                                    dense
                                    size="22"
                                    color="primary"
                                    background-color="primary"
                                    value="{{ $comment->rating }}"
                                >
                                </v-rating>
                            </div>
                        </div>
                        <div class="rating-item__objects">
                            <div class="label-item">Tipas, objektas, lokacija:</div>
                            {{ $comment->inquiry->sum_up }}
                        </div>
                        @endif
                        <div class="rating-item__review">
                            <div class="label-item">Atsiliepimas</div>
                            {!! nl2br($comment->text) !!}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
@endsection
