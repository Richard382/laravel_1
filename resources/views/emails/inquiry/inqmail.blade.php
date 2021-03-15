@component('mail::message')
# Sveiki,

<table class="simple">
    <tbdoy>
        <tr>
            <th style="width:170px">Vardas Pavardė:</th>
            <td>{{ Auth::user()->name }}</td>
        </tr>
        <tr>
            <th>El. pašto adresas:</th>
            <td>{{ Auth::user()->email }}</td>
        </tr>
        <tr>
            <th>Telefono numeris:</th>
            <td>{{ Auth::user()->phone }}</td>
        </tr>
    </tbdoy>
</table>

{{ config('app.name') }}
@endcomponent