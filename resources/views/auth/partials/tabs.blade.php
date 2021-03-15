<tabs
    :tabs="{{ collect([
        [
            'text' => 'Registracija Užsakovams',
            'link' => route('register.regular'),
            'class' => 'register_regular',
            'disabled' => false
        ],
        [
            'text' => 'Registracija NT specialistams',
            'link' => route('register.broker'),
            'class' => 'register_broker',
            'disabled' => false,
        ]
        ]) }}"
    current-link="{{ Request::fullUrl() }}"
></tabs>
