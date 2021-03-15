@foreach(config('site.menu.'.$menu) as $link)

    @php
        $profile = Arr::get($link, 'profile', 0);
        $button = Arr::get($link, 'button', 0);
        $auth = Arr::get($link, 'auth', 0);
        $class = Arr::get($link, 'class', false);
        $replace_with_inquiries_amount = Arr::get($link, 'regular_inquiries_amount', false);

        if ($auth && Auth::user()) {
            continue;
        }

    @endphp

    <v-list-item
        href="{{ route(Arr::get($link, 'route')) }}"
        :ripple="{{ ! $button && isset($ripple) && $ripple ? $ripple : 'false' }}"
        class="{{ $button || $profile ? 'v-list-item--button ' : '' . $class }}"
    >
        <v-list-item-content>
            @if ($profile)
                <div class="v-list-item__profile">
                    <span class="v-list-item__profile-image" {!! Auth::user()->avatar ? sprintf('style="background-image: url(\'%s\')"', url(Auth::user()->avatar_url)) : '' !!}></span>
                    <span class="v-list-item__profile-name">{{ Auth::user()->name }}</span>
                    <v-icon>mdi-chevron-right</v-icon>
                </div>
            @elseif ($button)
                <v-btn color="primary">{{ Arr::get($link, 'title', 'no-title') }}</v-btn>
            @else
                <v-list-item-title>
                    @if ($replace_with_inquiries_amount)
                        {{ sprintf(Arr::get($link, 'title', 'no-title'), $inquiries_count) }}
                    @else
                        {{ Arr::get($link, 'title', 'no-title') }}
                    @endif
                </v-list-item-title>
            @endif
        </v-list-item-content>
    </v-list-item>
@endforeach
