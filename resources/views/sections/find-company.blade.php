<!-- <div class="find-company section">
    <div class="container">
        <h2 class="text-center">Rinktis pagal sritį:</h2>

        <v-expansion-panels
            class="v-expansion-panels--front"
            accordion
            focusable
        >
            @foreach($property_types as $type)
                <v-expansion-panel>
                    <v-expansion-panel-header expand-icon="mdi-menu-down">{{ $type->name }}</v-expansion-panel-header>
                    <v-expansion-panel-content>
                        <ul class="simple-list">
                            @foreach($type->properties as $property)
                                <li class="simple-list__item">
                                    <a href="{{ route('companies.index') . sprintf('?type=%s&property=%s', $type->id, $property->id) }}" class="simple-list__item-link">{{ $property->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </v-expansion-panel-content>
                </v-expansion-panel>
            @endforeach
        </v-expansion-panels>
    </div>
</div> -->
