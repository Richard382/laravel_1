<tabs
    :tabs="{{ collect([
    [
        'text' => 'Klientų užklausos',
        'link' => route('inquiry.index'),
        'disabled' => Auth::user()->isBroker() ? false : true
    ],
    [
        'text' => 'Mano užklausos',
        'link' => route('inquiry.my'),
        'disabled' => false
    ]
    ]) }}"
    current-link="{{ Request::fullUrl() }}"
></tabs>
<!--'text' => (Auth::user()->isBroker())?'Klientų užklausos':'Paklausimai',
'text' => (Auth::user()->isBroker())?'Mano užklausos':'Pasiūlymai',-->
