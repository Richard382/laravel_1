<v-footer
    padless
    color="#3d3f4b"
>
    <v-container>
        <v-row
            no-gutters
        >
            <v-col>
                <v-list
                    flat
                    color="transparent"
                    class="v-list--inline"
                >
                    {!! menu('footerOne', 'partials.vmenu') !!}

                </v-list>
            </v-col>
            <v-col>
                @if ( ! Auth::check())
                    <v-list
                        flat
                        color="transparent"
                        class="v-list--inline"
                    >
                        <v-list-item
                            href="{{ route('login') }}"
                        >
                            <v-list-item-content>
                                <v-list-item-title>
                                    {{ __('Prisijungti') }}
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item
                            href="{{ route('register.broker') }}"
                        >
                            <v-list-item-content>
                                <v-list-item-title>
                                    {{ __('Registracija') }}
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
                @endif
            </v-col>
        </v-row>

        <div class="socials mt-6">
            @include('partials.icon-button', [
                'mdi_icon' => 'mdi-facebook',
                'link' => 'https://www.facebook.com/cont.lt'
            ])

            @include('partials.icon-button', [
                'mdi_icon' => 'mdi-instagram',
                'link' => 'https://www.instagram.com/cont.lietuva/'
            ])
        </div>
    </v-container>

    <div class="v-footer__bottom">
        <v-container>CONT.lt © {{ date('Y') }}</v-container>
    </div>
</v-footer>
