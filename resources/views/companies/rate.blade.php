@extends('layouts.templates.contained')

@section('content')

    <div class="company-rate">
        <h2>Palikite atsiliepimą apie paslaugos teikeją</h2>

        <rate-form
            route="{{ route('companies.rate', ['company' => $company->id, 'inquiry' => $inquiry->getId()]) }}"
        >
        </rate-form>
    </div>

@endsection
