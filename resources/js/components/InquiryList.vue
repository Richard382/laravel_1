<template>
    <div class="inquiries__list ">
        <template v-for="(item, index) in items">
            <div :key="items.id"
                class="inquiries__list-label"
                v-if="item.offer_accepted && index === 0"
            >Jus pasirinko:</div>
            <div
                :class="[
                'inquiries__list-item list-item  notiTab',
                item.offer_accepted ? 'list-item--accepted' : '',
                item.service_id == '1'? 'item-border--red1' : (item.service_id == '2'?'item-border--blue1':'item-border--gray1'),
                item.offer_accepted && checkIfNextNotAccepted(index) ? 'list-item--last-accepted' : ''
            ]"
            >
            <v-row
              class="mx-auto"
            >
              <v-col
                cols="12"
                sm="2">
                <div class="list-item__title">
                  <span class="subNoTag">Užklausa </span><span class="subNo">Nr. {{ item.id }} </span>
                </div>
                <div>
                  <v-btn 
                        v-if="item.service_id == 1"
                        class="specialist_btn white--text"
                        color="#FF5C5C"
                    >
                        IEŠKAU
                    </v-btn>
                    <v-btn 
                        v-if="item.service_id == 2"
                        class="specialist_btn white--text"
                        color="#0000CD"
                    >
                        SIŪLAU
                    </v-btn>
                    <v-btn 
                        v-if="item.service_id == 3"
                        class="specialist_btn white--text"
                        color="#787E89"
                    >
                        KITA
                    </v-btn>
                    <v-btn 
                        v-if="item.service_id == 4"
                        class="specialist_btn white--text"
                        color="#787E89"
                    >
                        IEŠKAU išsinuomoti
                    </v-btn>
                </div>
                <countdown
                    :time="item.end_time"
                    :transform="transform"
                    class="list-item__counter"
                    v-if="item.offer_accepted"
                >
                    <template slot-scope="props">{{ props.minutes }}:{{ props.seconds }}</template>
                </countdown>
              </v-col>
              <v-col
                cols="12"
                sm="8"
              >
                <div class="inquiry-info">
                    <strong class="inquiry-info__service">Paslauga, objektas, lokacija, kaina:</strong>
                    <span class="inquiry-info__property_type">{{ item.sum_up }}</span>
                </div>
                <div class="inquiry-info">
                    <strong class="inquiry-info__service">Užklausa</strong>
                    <nl2br tag="span" :text="item.requirements" class="inquiry-info__property_type" />
                </div>
              </v-col>
              <v-col
                cols="12"
                sm="2"
                v-if="$root.role() && ($root.role() === 'broker' || ($root.role() === 'regular' && item.owner)) "
              >
                <v-btn
                    color="primary"
                    width="100%"
                    class="v-btn--submit v-btn--no-gutter"
                    :href="$root.role() === 'broker' ? (item.made_an_offer ? (item.offer_accepted ? item.pay_link : undefined) : undefined) : '/uzklausos/' + item.id"
                    @click="handleClick(item)"
                    :disabled="(item.made_an_offer && ! item.offer_accepted) || item.offer_declined"
                >
                <!--:href="$root.role() === 'broker' ? (item.made_an_offer ? (item.offer_accepted ? '/payment/' + item.offer_accepted : undefined) : undefined) : '/inquiries/' + item.id"-->
                    {{ $root.role() === 'broker' ? (item.made_an_offer ? (item.offer_accepted ? 'Apmokėti' + ' ' + $root.getPrice() + $root.getCurrency() : (item.offer_declined ? 'Pasiulymas atmestas' : (item.payed_an_offer ? 'Pasiūlymas apmokėtas, duomenys išsiųsti' : 'Pasiulymas jau išsiųstas'))) : 'Siūlyti') : 'Peržiūrėti' }}
                </v-btn>
              </v-col>
            </v-row>
            </div>
        </template>

        <v-alert
            outlined
            class="v-alert--nt-info"
            v-if=" ! items.length"
        >
            Paklausimų nėra
        </v-alert>

        <div class="inquiries__list-pagination" v-if="total_visible && items.length > 0">
            <v-pagination
                v-model="page"
                :length="pages_number"
                :total-visible="pages_number"
                @input="nextPage"
                circle
            ></v-pagination>
        </div>
        <offer-form />
    </div>
</template>

<script>
  import Nl2br from 'vue-nl2br';
  import objectToFormData from 'object-to-formdata';
  import countdown from '@chenfengyuan/vue-countdown';
  import OfferForm from "./OfferForm";

  export default {
    name: "InquiriesList",
    data() {
      return {
        page: this.current_page,
        items: this.current_items
      }
    },
    components: {
      Nl2br,
      countdown,
      OfferForm
    },
    props: {
      pages_number: {
        type: Number
      },
      total_visible: {
        type: Number
      },
      current_page: {
        type: Number
      },
      current_items: {
        type: Array
      },
    },
    methods: {
      checkIfNextNotAccepted(index)
      {
        if (typeof this.items[index + 1] !== undefined && this.items[index + 1])
        {
          let item = this.items[index + 1];

          if (typeof item.offer_accepted !== undefined) {
            return ! item.offer_accepted ? true : false;
          }
        }

        return false;
      },

      transform(props)
      {
        Object.entries(props).forEach(([key, value]) => {
          // Adds leading zero
          const digits = value < 10 ? `0${value}` : value;

          props[key] = `${digits}`;
        });

        return props;
      },

      handleClick(item)
      {
        if (this.$root.role() === 'broker' && ! item.offer_accepted)
        {
          this.$root.$emit('openModal', item);
        }
      },

      nextPage(value)
      {
        const data = objectToFormData({
          page: value
        }, {
          indices: true,
          nullsAsUndefineds: true
        });

        axios.get('?page=' + value)
        .then(response => {
          this.items = response.data.items;
          this.$root.toTop();
        })

      }
    }
  }
</script>

<style scoped>

</style>
