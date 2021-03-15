@component('mail::message')
# Sveiki,

Sveikiname užsiregistravus CONT sistemoje. Norint naudotis sistema, Jums reikia patvirtinti savo paskyrą paspaudus žemiau esančią nuorodą:

@component('mail::button', ['url' => $user->getConfirmationLink()])
Patvirtinti
@endcomponent

Jei mygtuko pagalba nuoroda neatsidaro, pasinaudokite šia nuoroda: <a href="{{ $user->getConfirmationLink() }}">PATVIRTINTI</a>

Pagarbiai,<br>
{{ config('app.name') }}
@endcomponent
