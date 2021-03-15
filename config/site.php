<?php

return [
    'price' => [
        'amount' => 10,
        'currency' => 'eur'
    ],
    'roles' => [
        0 => [
            'display_name' => 'Brokeris',
            'name' => 'broker',
            'slug' => 'broker'
        ],
        1 => [
            'display_name' => 'Vartotojas',
            'name' => 'regular',
            'slug' => 'regular'
        ]
    ],
    'menu' => [
        'main' => [
            0 => [
                'title' => 'Registracija',
                'route' => 'register.broker',
                'button' => true
            ],
            1 => [
                'title' => 'Kaip tai veikia?',
                'route' => 'how-it-works'
            ],
            2 => [
                'title' => 'Rekomenduojami partneriai',
                'route' => 'recommended-partners'
            ],
            3 => [
                'title' => 'Ieškomas turtas',
                'route' => 'inquiry.index'
            ],
            4 => [
                'title' => 'Brokerių sąrašas',
                'route' => 'companies.index'
            ],
            5 => [
                'title' => 'Kontaktai',
                'route' => 'contacts'
            ],
            6 => [
                'title' => 'Prisijungti',
                'route' => 'login',
                'class' => 'v-list-item--spacer-top'
            ]
        ],
        'main_online' => [
            0 => [
                'profile' => true,
                'route' => 'profile.me',
            ],
            1 => [
                'title' => 'Aktyvūs paklausimai (%s)',
                'route' => 'inquiry.index',
                'regular_inquiries_amount' => true
            ],
            2 => [
                'title' => 'Kaip tai veikia?',
                'route' => 'how-it-works',
                'class' => 'v-list-item--spacer-top'
            ],
            3 => [
                'title' => 'Kontaktai',
                'route' => 'contacts'
            ],
            4 => [
                'title' => 'Atsijungti',
                'route' => 'logout',
                'class' => 'v-list-item--spacer-top v-list-item--logout'
            ]
        ],
        'footer_one' => [
            0 => [
                'title' => 'Kaip tai veikia?',
                'route' => 'how-it-works',
            ],
            1 => [
                'title' => 'Sąlygos ir taisyklės',
                'route' => 'terms-conditions'
            ],
            2 => [
                'title' => 'Privatumo politika',
                'route' => 'privacy-policy'
            ]
        ],
        'footer_two' => [
            0 => [
                'title' => 'Prisijungti',
                'route' => 'login',
                'auth' => true
            ],
            1 => [
                'title' => 'Registracija',
                'route' => 'register.broker',
                'auth' => true
            ]
        ]
    ],
    'taxos' => [
        'Gyvenamasis sektorius' => [
            'Butai, loftai, apartamentai',
            'Namai, kotedžai',
            'Sodybos, vilos'
        ],
        'Komercinis turtas' => [
            'Administracinės patalpos',
            'Prekybinės patalpos',
            'Paslaugų patalpos',
            'Pramonės - gamybos patalpos',
            'Maitinimo įstaigų patalpos',
            'Gydymo (medicinos) patalpos',
            'Apgyvendinimo patalpos',
            'Sandėliavimo patalpos',
            'Ūkiniai pastatai',
            'Rekreacinės paskirties patalpos',
        ],
        'Žemės sklypai' => [
            'Namų valda, sodo sklypai',
            'Prekybos ir paslaugų (K1)',
            'Pramonės ir sandėliavimo (P/P1/P2)',
            'Daugiabučiams (G2)',
            'Žemės ūkio žemė',
            'Miškų ūkio sklypai',
            'Rekreacinės paskirties sklypai',
            'Vandens telkiniai',
        ],
        'Garažai / parkavimo vietos' => [
            'Garažai / parkavimo vietos',
        ]
    ],
    'languages' => [
        'Lietuvių',
        'Anglų',
        'Rusų',
        'Lenkų',
        'Vokiečių'
    ],
    'services' => [
        'Siūlau Parduoti NT' => [
            'type' => \App\Service::TYPE_OFFER,
            'broker' => 'Pardavimas',
        ],
        'Ieškau Pirkti NT' => [
            'type' => \App\Service::TYPE_LOOKIN,
            'broker' => 'Paieška parduodamo NT'
        ],
        'Išnuomoti NT' => [
            'type' => \App\Service::TYPE_OFFER,
            'broker' => 'Nuoma',
        ],
        'Ieškau Nuomotis NT' => [
            'type' => \App\Service::TYPE_LOOKIN,
            'broker' => 'Paieška Nuomojamo NT',
        ],
        'NT paskolos' => [
            'type' => \App\Service::TYPE_OTHER,
            'broker' => 'Paskolos'
        ],
        'NT vertinimas' => [
            'type' => \App\Service::TYPE_OTHER,
            'broker' => 'Vertinimas'
        ],
        'Matininkų paslaugos' => [
            'type' => \App\Service::TYPE_OTHER,
            'broker' => 'Matininkų paslaugos'
        ],
        'NT valdymas ir administravimas' => [
            'type' => \App\Service::TYPE_OTHER,
            'broker' => 'Valdymas ir administravimas'
        ],
        'NT konsultacija' => [
            'type' => \App\Service::TYPE_OTHER,
            'broker' => 'Konsultavimas',
        ]
    ],
];
