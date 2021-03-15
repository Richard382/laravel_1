<v-slide-y-reverse-transition>
    <v-btn
        small
        min-width="auto"
        width="34"
        height="34"
        v-scroll="onScroll"
        v-show="btotop"
        @click="toTop"
        class="v-btn--icon-btn v-btn--back-to-top"
    >
        <v-icon size="24">mdi-menu-up</v-icon>
    </v-btn>
</v-slide-y-reverse-transition>
