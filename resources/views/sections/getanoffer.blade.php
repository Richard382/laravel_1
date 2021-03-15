<div class="getanoffer">
    <v-container>
        <div class="text-center">
            <h1 class="font-weight-light mb-0">Čia renkasi visi Lietuvos NT profesionalai</h1>
             <span class=" mb-0 font-weight-bold" style="font-size:28px;">Gauk NT pasiūlymą per 1 val.<span> <span class="font-weight-light "style="font-size:16px; display:none;"> -garantuotai darbo laiku<span>

        </div><br>
        <get-an-offer
            {{ $preset_city ? sprintf(':preset_cities=%s', $preset_city) : '' }}
            {{ $preset_service ? sprintf(':preset_service=%s', $preset_service) : '' }}
            {{ $preset_property_type ? sprintf(':preset_property_type=%s', $preset_property_type) : '' }}
            {{ $preset_properties ? sprintf(':preset_properties=%s', $preset_properties) : '' }}
            preset_price_to="{{ $preset_price_to }}"
            preset_price_from="{{ $preset_price_from }}"
            :cities="{{ $cities }}"
            :services="{{ $services }}"
            :property_types="{{ $property_types->toJson() }}"
            redirect-route="{{ route('inquiry.create') }}"
        ></get-an-offer>
    </v-container>
</div>
