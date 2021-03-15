@extends('layouts.templates.contained')

@section('content')
<div class="background--white">
    <div class="register register--broker">
        <h2>Atstatyti slaptažodį</h2>

        <reset-form
            initial-email="{{ $email }}"
            initial-token="{{ $token }}"
        />
    </div>
</div>
@endsection
