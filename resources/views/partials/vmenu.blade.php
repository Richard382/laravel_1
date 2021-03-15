@foreach($items as $menu_item)

    @php

        $button = 'true' === $menu_item->button;
        $auth = 'true' === $menu_item->button;
        $class = $menu_item->class;
        $replace_with_inquiries_amount = false;

        if ($auth && Auth::user()) {
            continue;
        }

    @endphp

    <v-list-item
        href="{{ $menu_item->url }}"
        :ripple="{{ ! $button && isset($ripple) && $ripple ? $ripple : 'false' }}"
        class="{{ $button ? 'v-list-item--button ' : '' }} {{ $class }}"
    >
        <v-list-item-content>
            @if ($button)
                <v-btn color="primary">{{ $menu_item->title }}</v-btn><br><br>
                <v-btn   href='/registracija/vartotojas' style="" class="sideBarMenu"><div class="text-center"><div>REGISTRACIJA</div><div>UŽSAKOVAMS</div></div></v-btn>
                <v-btn   href='/registracija/brokeris' style="" class="sideBarMenu"><div><div>REGISTRACIJA NT</div><div>SPECIALISTAMS</div></div></v-btn>
            @else
                <v-list-item-title>
                    {{ $menu_item->title }}
                </v-list-item-title>
            @endif
        </v-list-item-content>
    </v-list-item>
@endforeach
