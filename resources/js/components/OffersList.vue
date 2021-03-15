<template>
    <div class="inquiries__list">
        <v-alert
            v-if="fresh"
            outlined
            class="v-alert--nt-info"
        >
            <!--Netrukus turėtumėte sulaukti pasiūlymų nurodytu el. paštu-->
            Netrukus turėtumėte sulaukti pasiūlymų šiame lange ir el. paštu!
        </v-alert>

        <div
            v-if="fresh"
            class="text-center pt-6 pb-5"
        >
            <a
                :href="create_inquiry_link"
                class="link link--underlined link--bold"
            >
                Sukurk naują užklausą
            </a>
        </div>

<!--        <v-btn
            color="primary"
            width="100%"
            class="v-btn--submit v-btn--no-gutter"
            :href="chose_broker_link"
        >
            NT brokerių sąrašas
        </v-btn>-->

        <div
            v-for="offer in items"
            :class="['inquiries__list-item inquiries__list-item--offer list-item', offer.payed ? 'list-item--accepted' : '']"
        >
            <div class="list-item__broker">
                <div class="list-item__broker-avatar">
                    <v-img
                        :src="offer.company.avatar_url"
                        width="65"
                        height="65"
                        v-if="offer.company.avatar_url"
                    >
                    </v-img>
                </div>
                <div class="list-item__broker-info">
                    <div class="list-item__broker-info-rating">
                        <v-rating
                            readonly
                            half-increments
                            dense
                            size="20"
                            :color="offer.payed ? 'white' : 'primary'"
                            background-color="primary"
                            :value="offer.company.rating"
                        >
                        </v-rating>
                    </div>
                    <div class="list-item__broker-info-name">{{ offer.company.name }}</div>
                </div>
                <div v-if="offer.accepted" class="primary white--text" style="min-width: 140px;margin-top: -20px;padding: 5px;">
                    <span>LAUKIAMA</span>
                    <countdown
                       :time="offer.endtime"
                       :transform="transform"
                       class="list-item__counter primary white--text"
                    >
                     <template slot-scope="props">{{ props.minutes }}:{{ props.seconds }}</template>
                  </countdown>
               </div>
            </div>
            <div class="list-item__offer">
                <nl2br tag="span" :text="offer.text" />
               <v-btn
                  v-if="offer.status === 3"
                  color="primary"
                  width="100%"
                  class="v-btn--submit v-btn--no-gutter"
                  disabled
               >
                  Priimtas pasiūlymas tapo nebegaliojantis
               </v-btn>
                <v-btn
                    color="primary"
                    width="100%"
                    class="v-btn--submit"
                    @click="acceptOffer(offer)"
                    v-if=" ! payed && ! offer.payed && !offer.accepted"
                >
                    Susisiekti
                </v-btn>
                <v-btn
                    color="primary"
                    width="100%"
                    class="v-btn--submit"
                    @click="evaluate(offer.company_id)"
                    v-if="offer.payed && ! offer.rated"
                >
                    Įvertinti
                </v-btn>
            </div>
        </div>

        <div class="inquiries__list-pagination" v-if="items.length > 0">
            <v-pagination
                v-model="page"
                :length="pages_number"
                :total-visible="pages_number"
                @input="nextPage"
                circle
            ></v-pagination>
        </div>
    </div>
</template>

<script>
  import Nl2br from 'vue-nl2br';
  import objectToFormData from 'object-to-formdata';
  import Redirect from "../utils/Redirect";
  import countdown from '@chenfengyuan/vue-countdown';

  export default {
    name: "OffersList",
    components: {
      Nl2br,countdown
    },
    data() {
      return {
        page: this.current_page,
        items: this.current_items,
        payed: this.accepted,
        currPage:1
      }
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
      offers: {
        type: Object
      },
      current_items: {
        type: Array
      },
      create_inquiry_link: {
        type: String,
      },
      chose_broker_link: {
        type: String,
      },
      accepted: {
        type: Number
      },
      inquiry: {
        type: String,
      },
      fresh: {
        type: Boolean,
        default() {
          return false;
        }
      }
    },
    mounted() {
        let self = this;
        setInterval(function(){
            self.nextPage(self.currPage,false);
        } ,10000);
    },
    methods: {
        evaluate(company)
        {
          Redirect('/nt-paslaugu-teikejai/' + company + '/' + this.inquiry + '/ivertinti');
        },

        acceptOffer(offer)
        {
          this.$root.$emit('openAcceptOfferModal', offer);
        },
        transform(props) {
            Object.entries(props).forEach(([key, value]) => {
               // Adds leading zero
               const digits = value < 10 ? `0${value}` : value;

               props[key] = `${digits}`;
            });

            return props;
         },
      nextPage(value,gotop=true)
      {
          this.currPage = value;
        const data = objectToFormData({
          page: value
        }, {
          indices: true,
          nullsAsUndefineds: true
        });

        axios.get('?page=' + value)
        .then(response => {
          this.items = response.data.items;
            var offtot = document.getElementById('offers_total');
            if(offtot) {
                offtot.innerHTML = response.data.total;
            }
            if(gotop){
                this.$root.toTop();
            }
        })
      }
    }
  }
</script>

<style scoped>

</style>
