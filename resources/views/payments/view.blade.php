@extends('layouts.templates.contained')

@section('content')

    <div class="payment">
        <h2>Apmokėti paslaugas</h2>

        <div class="payment__info">
            @if (session()->has('message'))
                <v-alert
                    outlined
                    :type="'error'"
                    class="error"
                >
                    {{ session('message') }}
                </v-alert>
            @endif

            <table class="simple">
                <thead>
                    <tr>
                        <th>Informacija</th>
                        <th>Kaina</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $model->getProductName() }}</td>
                        <td>{{ $model->getProductServicePrice() }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="payment__methods">
                <form method="POST">
                    <input type="hidden" name=gateway value="paysera">
                    {{ csrf_field() }}
                    <v-btn
                        color="primary"
                        width="100%"
                        class="v-btn--submit v-btn--no-gutter"
                        type="submit"
                    >
                        Apmokėti su PaySera
                    </v-btn>
                </form>
            </div>
        </div>
    </div>

@endsection
