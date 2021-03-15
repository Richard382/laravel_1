<v-navigation-drawer
        v-model="drawer"
        app
        overlay-color="transparent"
        temporary
        width="250"
    >
    <v-list class="v-list--small v-list--offline v-list--header">
        @if (Auth::check())
            <v-list-item
                href="{{ route('profile.me') }}"
                class="v-list-item--button"
            >
                <v-list-item-content>
                    <div class="v-list-item__profile">
                        <span class="v-list-item__profile-image" {!! Auth::user()->avatar ? sprintf('style="background-image: url(\'%s\')"', url(Auth::user()->avatar_url)) : '' !!}></span>
                        <span class="v-list-item__profile-name">{{ Auth::user()->name }}</span>
                        <v-icon>mdi-chevron-right</v-icon>
                    </div>
                </v-list-item-content>
            </v-list-item>
            <v-list-item
                href="{{ Auth::user()->isBroker() ? route('inquiry.index') : route('inquiry.my') }}"
            >
                <v-list-item-content>
                    <v-list-item-title class="primary--text">
                        @if (Auth::user()->isBroker())
                        <!--{{ sprintf(__('Aktyvūs paklausimai (%s)'), $inquiries_count) }}-->
                        {{ sprintf(__('Klientų užklausos (%s)'), $inquiries_count) }}
                        @else
                        {{ sprintf(__('Mano užklausos (%s)'), $inquiries_count) }}
                        @endif
                    </v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            @if (Auth::user()->isBroker())
                <v-list-item
                    class="v-list-item--spacer-top"
                    href="{{ route('inquiry.index') }}?klientu-uzklausos"
                >
                    <v-list-item-content>
                        <v-list-item-title>
                            <!--{{ __('Jūsų paklausimai') }}-->
                            {{ __('Mano pasiūlymai') }}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            @endif
            <v-list-item
                href="{{ route('inquiry.archive') }}"
            >
                <v-list-item-content>
                    <v-list-item-title>
                        <!--Paklausimų archyvas-->
                        Archyvas
                    </v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            @if (Auth::user()->isBroker())
                <v-list-item
                    class="v-list-item--spacer-bot"
                    href="{{ route('broker.visibility') }}"
                >
                    <v-list-item-content>
                        <v-list-item-title>
                            Tapk matomu
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            @endif
        @endif

        {!! menu(Auth::user() ? 'loggedin' : 'primary', 'partials.vmenu') !!}

        @if (Auth::check())
            <v-list-item
                href="{{ route('logout') }}"
                class="v-list-item--spacer-top v-list-item--logout"
            >
                <v-list-item-content>
                    <v-list-item-title>
                        {{ __('Atsijungti') }}
                    </v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        @endif
    </v-list>
</v-navigation-drawer>

<v-app-bar
    apps
    color="primary"
    :height="66"
    class="v-toolbar--header"
>
    <v-btn
        icon
        :ripple="false"
        @click.stop="drawer = !drawer"
        class="menu-with-titles"
    >
        <v-img src="{{ @asset('images/hamburger.png') }}" />

    </v-btn>

    <v-spacer></v-spacer>
    @if (!Auth::check())
    <v-spacer></v-spacer>
    @endif

    <v-toolbar-title>
        <a href="{{ URL::to('/') }}" class="d-flex">
            <img style="max-width: 100px; margin-top: 10px;" src="{{ @asset('images/CONT.png') }}" />
        </a>
    </v-toolbar-title>

    <v-spacer></v-spacer>
    
    <v-btn
        icon
        style="visibility:hidden"
    >
        <v-img src="{{ @asset('images/x.png') }}" max-width="16.4" />
    </v-btn>

    @if (Auth::check())
        <notifications
            v-if="!drawer"
            :notifications="{{ Auth::user()->unreadNotifications }}"
            isallview="{{ Auth::user()->isAllView() }}"
        >
        </notifications>
    @else
        @if(Request::fullUrl() !== route('register.regular') && Request::fullUrl() !== route('register.broker'))
            <!-- <v-btn
                icon
                :ripple="false"
                class="icon icon--person menu-with-title"
                href="{{ route('register.regular') }}"
            >
              
            </v-btn> -->
        @endif
        <div class="subheading ml-12 mr-12 hidden-sm-and-down"><a class="white--text" href="/registracija/vartotojas">REGISTRUOTIS</a></div>
        <div class="subheading ml-12 mr-12 hidden-sm-and-down"><a class="white--text" href="/registracija/brokeris">PRISIJUNGTI</a></div>
    @endif

    </v-btn>

    <v-btn
        icon
        v-if="drawer"
        :ripple="false"
        @click.stop="drawer = !drawer"
    >
        <v-img src="{{ @asset('images/x.png') }}" max-width="16.4" />
    </v-btn>


</v-app-bar>
