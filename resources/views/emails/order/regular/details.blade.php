@component('mail::message')
# Sveiki,

Siunčiame paslaugų teikėjo kontaktus ir informaciją Jūsų paklausimui Nr. {{ $inquiry->id }}:

<table class="simple">
    <tbdoy>
        <tr>
            <th>Vardas, pavardė:</th>
            <td>{{ $representator->name }}</td>
        </tr>
        <tr>
            <th>El. paštas</th>
            <td>{{ $representator->email }}</td>
        </tr>
        <tr>
            <th>Telefonas</th>
            <td>{{ $representator->phone }}</td>
        </tr>
        <tr>
            <th>Brokerio puslapis</th>
            <td><a href="{{ $company->link }}">PAŽIŪRĖTI</a></td>
        </tr>
    </tbdoy>
</table>

<table class="simple">
    <tbdoy>
        <tr>
            <th>Tikslas</th>
            <td>{{ $inquiry->service->type_user }}</td>
        </tr>
        <tr>
            <th>Reikalavimai</th>
            <td>{{ $inquiry->sum_up }}</td>
        </tr>
        <tr>
            <th>Išsamesnė informacija:</th>
            <td>{!! nl2br($inquiry->requirements) !!}</td>
        </tr>
    </tbdoy>
</table>

Jeigu turite klausimų, susisiekiti galite <a href="mailto:info@cont.lt">info@cont.lt</a>

Pagarbiai,<br>
{{ config('app.name') }}
@endcomponent
