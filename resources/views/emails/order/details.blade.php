@component('mail::message')
# Sveiki,

Siunčiame Nr. {{ $inquiry->id }} informaciją ir kliento kontaktus:

<table class="simple">
    <tbdoy>
        <tr>
            <th>Vardas, pavardė:</th>
            <td>{{ $inquiry->getRealCreatorUser()->name }}</td>
        </tr>
        <tr>
            <th>El. paštas</th>
            <td>{{ $inquiry->getRealCreatorUser()->email }}</td>
        </tr>
        <tr>
            <th>Telefonas</th>
            <td>{{ $inquiry->getRealCreatorUser()->phone }}</td>
        </tr>
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
