<template>
   <div
      :class="[
                'inquiries__list-item inquiries__list-items--short list-item',
                item.offer_accepted ? 'list-items--accepted' : '',
                item.owner ? 'list-items--owner' : 'list-items--' + item.service_type,
                item.made_an_offer && ! item.offer_declined && ! item.offer_accepted && !item.payed_an_offer ? 'list-items--waitting' : ''
            ]"
   >
   <v-row no-gutters>
      <v-col
      cols="12"
      sm="2"
      >
         <v-list-item>
            <v-list-item-content>
               <v-list-item-title>
                  <p v-if="item.owner">
                     Susidomėjo: {{ item.offer_count }}
                  </p>
                  <p v-else-if="item.made_an_offer && ! item.offer_declined && ! item.offer_accepted && !item.payed_an_offer">
                     Laukiama
                  </p>
                  <p v-else>
                     {{ item.time_ago }}
                  </p>
               </v-list-item-title>
               <v-list-item-title class="inquiry-id">Nr. {{ item.id }}</v-list-item-title>
               <v-list-item-title>
                  <v-btn 
                     v-if="item.service_id == 1"
                     class="specialist_btn_uz white--text"
                     color="#FF5C5C"
                  >
                     IEŠKAU
                  </v-btn>
                  <v-btn 
                     v-if="item.service_id == 2"
                     class="specialist_btn_uz white--text"
                     color="#0000CD"
                  >
                     SIŪLAU
                  </v-btn>
                  <v-btn 
                     v-if="item.service_id == 3"
                     class="specialist_btn_uz white--text"
                     color="#787E89"
                  >
                     KITA
                  </v-btn>
                  <v-btn 
                     v-if="item.service_id == 4"
                     class="specialist_btn_uz white--text"
                     color="#787E89"
                  >
                     IEŠKAU išsinuomoti
                  </v-btn>

                  <v-btn v-if="item.owner"
                     color="red"
                     width="100px"
                     class="v-btn--submit v-btn--no-gutter"
                     @click="openDeleteModal(item.id)"
                  >
                     Trinti
                  </v-btn>
                  
               </v-list-item-title>
               
            </v-list-item-content>
         </v-list-item>
         
      </v-col>
      <v-col
      cols="12"
      sm="2"
      class="pa-4 card-letter-box"
      >
         <div>Tipas</div>
         <div>
            <h4 class="service_title">{{item.property_type[0]['name']}}</h4>
         </div>    
         <div>Objektas</div>
         <div>
            <h4 class="service_title">{{item.properties[0]['name']}}</h4>
         </div>
      </v-col>
      <v-col
      cols="12"
      sm="5"
      class="pa-4 card-letter-box"
      >
         <div @click="open">
         <div>
            <div class="d-flex align-center">
               <div class="flex">

                  <div class="inquiry-info">
                     <strong class="inquiry-info__service">{{ item.sum_up }}</strong>
                  </div>
               </div>
                
               <div v-if="item.offer_accepted" style="min-width: 60px;">
                  <countdown
                     :time="item.end_time"
                     :transform="transform"
                     class="list-item__counter"
                  >
                     <template slot-scope="props">{{ props.minutes }}:{{ props.seconds }}</template>
                  </countdown>
               </div>
            </div>
         </div>

         <div
            :class="[extended ? '' : 'd-none', 'list-item__hidden-part']"
         >
            <ul class="inquiry-data">
               <!--                            <li><span>Segmentas: </span> {{ item.property_type_name }}</li>-->
               <!--                            <li><span>Lokacija: </span> {{ item.region_name }}</li>-->
               <!--                            <li><span>Kaina: </span> {{ item.price }}</li>-->
               <li><span>Aprašymas: </span><br>
                  <nl2br tag="div" :text="item.requirements" class="inquiry-info__property_type"/>
               </li>
               <template v-if="item.made_an_offer && item.payed_an_offer && item.in_archive">
                  <li><span>Tel. Nr.: </span><br>
                     {{ item.secured_data.phone }}
                  </li>
                  <li><span>El. p.:</span><br>
                     {{ item.secured_data.email }}
                  </li>
               </template>
            </ul>
         </div>
      </div>
      <div
         :class="[extended ? '' : 'd-none', '']"
      >
         <template v-if="item.owner">
            <v-btn
               color="primary"
               width="100%"
               class="v-btn--submit v-btn--no-gutter"
               :href="item.link"
            >
               Peržiūrėti
            </v-btn>
            <template v-if="! item.active && item.status === 'reviewed'">
               <v-btn
                  v-if="! item.in_archive"
                  color="primary"
                  width="100%"
                  class="v-btn--submit v-btn--no-gutter mt-4"
                  @click="archive"
               >
                  Įvykdyta
               </v-btn>
               <template
                  v-else
               >
                  <v-btn
                     color="red"
                     width="100%"
                     class="v-btn--submit v-btn--no-gutter mt-4"
                     @click="eraseFromArchive"
                  >
                     Ištrinti
                  </v-btn>
               </template>
            </template>
         </template>

         <template v-else-if="! item.made_an_offer">
            <v-btn
               color="primary"
               width="100%"
               @click="$emit('doOffer', item)"
               class="v-btn--submit v-btn--no-gutter"
            >
               Siūlyti
            </v-btn>
         </template>

         <template v-else-if="item.made_an_offer && item.offer_accepted">
            <v-btn
               color="primary"
               width="100%"
               class="v-btn--submit v-btn--no-gutter"
               :href="item.pay_link"
            >
               Apmokėti {{ item.offer_price }}
            </v-btn>
         </template>

         <template v-else-if="item.made_an_offer && item.payed_an_offer">
            <template
               v-if="! item.archived"
            >
                <a v-bind:href="'tel:'+item.secured_data.phone">
                    <v-btn
                       color="primary"
                       width="100%"
                       class="v-btn--submit v-btn--no-gutter"
                       style="user-select:all"
                    >
                       Tel. Nr.: {{ item.secured_data.phone }}
                    </v-btn>
                </a>
                <a v-bind:href="'mailto:'+item.secured_data.email">
                    <v-btn
                       color="primary"
                       width="100%"
                       class="v-btn--submit v-btn--no-gutter mt-4"
                       style="user-select:all"
                    >
                       El. p.: {{ item.secured_data.email }}
                    </v-btn>
                </a>
            </template>
  
            <template v-if="item.archived_by_owner">
               <v-btn
                  v-if="! item.in_archive"
                  color="primary"
                  width="100%"
                  class="v-btn--submit v-btn--no-gutter mt-4"
                  @click="archive"
               >
                  Įvykdyta?
               </v-btn>
               <template
                  v-else
               >
                  <v-btn
                     v-if="currentCompanyRoute"
                     width="100%"
                     class="v-btn--submit v-btn--no-gutter mt-4"
                     :href="currentCompanyRoute"
                  >
                     Skaityti atsiliepimą
                  </v-btn>
                  <v-btn
                     color="red"
                     width="100%"
                     class="v-btn--submit v-btn--no-gutter mt-4"
                     @click="eraseFromArchive"
                  >
                     Ištrinti
                  </v-btn>
               </template>
            </template>
         </template>

         <template v-else>
            <v-btn
               color="primary"
               width="100%"
               class="v-btn--submit v-btn--no-gutter"
               disabled
            >
               <template v-if="item.made_an_offer && item.offer_declined">
                  Pasiulymas atmestas
               </template>
               <template v-else>
                  Pasiulymas jau išsiųstas
               </template>
            </v-btn>
         </template>
      </div>
      </v-col>

      <v-col
         cols="12"
         sm="3"
         class=""
      >
         <v-list-item two-line>
            <v-list-item-content>
                  <v-list-item-title :class="item.service_id != 2 ? '':'d-none'">
                     <v-row>
                        <v-col sm="3">
                              <h3 class="mt-2">nuo</h3>
                        </v-col>
                        <v-col sm="9">
                              <h3 class="h2 kaina-num">€ {{item.price_from}}</h3>
                        </v-col>
                     </v-row>
                  </v-list-item-title>
                  <v-list-item-title :class="item.service_id != 2 ? '':'d-none'">
                     <v-row>
                        <v-col sm="3">
                              <h3 class="mt-2">iki</h3>
                        </v-col>
                        <v-col sm="9">
                              <h3 class="h2 kaina-num">€ {{item.price_to}}</h3>
                        </v-col>
                     </v-row>
                  </v-list-item-title>
                  <v-list-item-title v-if="item.service_id == 2">
                     <v-row class="mx-auto">
                        <v-col cols="12" sm="12" class="text-center">
                              <h3 class="h2 kaina-num">€ {{item.price_from}}</h3>
                        </v-col>
                     </v-row>
                  </v-list-item-title>
                  <hr />
                  <span class="text-center price_label">
                     kaina
                  </span>
            </v-list-item-content>
         </v-list-item>
      </v-col>
   </v-row>
      
   </div>
</template>

<script>
   import Nl2br from 'vue-nl2br';
   import countdown from '@chenfengyuan/vue-countdown';

   export default {
      name: "Inquiry",
      data() {
         return {
            extended: true,
         }
      },
      props: {
         item: {
            type: Object
         },
         expand: {
            type: String
         },
         currentCompanyRoute: {
            type: [String, Boolean],
            default() {
               return false
            }
         },
      },
      components: {
         Nl2br,
         countdown
      },
      mounted(){
          
        if(this.expand && this.item.id == this.expand) {
            
            this.open();
        }
      },
      methods: {
         openDeleteModal(id){
             console.log("dsafuhgiausf",id);
             this.$parent.openDeleteModal(id);
         },
         transform(props) {
            Object.entries(props).forEach(([key, value]) => {
               // Adds leading zero
               const digits = value < 10 ? `0${value}` : value;

               props[key] = `${digits}`;
            });

            return props;
         },

         archive() {
            this.$parent.openarchiveModal(this.item.id);
            return;
//            axios.post(`/uzklausos/${this.item.id}/archyvuoti`)
//            .then(response => {
//               setTimeout(function(){
//                  location.reload();
//               }, 2000);
//            }).catch(error => {
//               setTimeout(function(){
//                  location.reload();
//               }, 2000);
//            });
         },

         eraseFromArchive() {
            axios.post(`/uzklausos/${this.item.id}/istrinti-archyva`)
            .then(response => {
               setTimeout(function(){
                  location.reload();
               }, 2000);
            }).catch(error => {
               setTimeout(function(){
                  location.reload();
               }, 2000);
            });
         },

         open() {
            if (this.extended) {
               this.extended = false;
            } else {
               this.extended = true;
            }
         }
      }
   }
</script>

<style scoped>

</style>
