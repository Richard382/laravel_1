<template>
   <div :class="['inquiries__list']">

      <div class="inquiries__list-filter" v-if=" !nofilter">
         <v-form class="form">

            <v-navigation-drawer
               v-model="item_filter"
               app
               overlay-color="transparent"
               temporary
               width="230"
               class="inquiries__list-drawer"
            >
               <div class="inquiries__list-drawer-title">
                  Patikslinkit paieška
               </div>
               <div class="inquiries__list-drawer-filters">
                  <v-select
                     v-model="form.region"
                     class="v-select--single v-input--labeled v-input--no-label"
                     no-data-text="Nėra duomenų"
                     placeholder="Miestas"
                     :items="regions"
                     item-text="name"
                     item-value="id"
                     clearable
                     :menu-props="{bottom: true, offsetY: true, maxHeight: 240}"
                  ></v-select>
                  <v-select
                     v-model="form.type"
                     key="form.type"
                     class="v-select--single v-input--labeled v-input--no-label"
                     no-data-text="Nėra duomenų"
                     placeholder="Tipas"
                     :items="types"
                     item-text="name"
                     item-value="id"
                     clearable
                     @change="changeObjects"
                     :menu-props="{bottom: true, offsetY: true, maxHeight: 240}"
                  ></v-select>
                  <v-select
                     v-model="form.object"
                     key="form.object"
                     class="v-select--single v-input--labeled v-input--no-label"
                     no-data-text="Nėra duomenų"
                     placeholder="Objektas"
                     :items="objects"
                     item-text="name"
                     item-value="id"
                     clearable
                     multiple
                     :disabled="!form.type"
                     :menu-props="{bottom: true, offsetY: true, maxHeight: 240}"
                  ></v-select>
               </div>
            </v-navigation-drawer>

            <v-text-field
               v-model="form.search"
               name="search"
               placeholder="Brokerio paieška…"
               append-icon="mdi-magnify"
               autocomplete="off"
               class="v-input--default v-input--labeled v-input--no-label v-input--with-submit-inside"
               type="text"
               color="custom"
               @click:append="this.submit"
            >
               <template v-slot:append-outer>
                  <v-btn
                     color="primary"
                     width="42"
                     @click="item_filter = true"
                  >
                     <v-icon>mdi-filter</v-icon>
                  </v-btn>
               </template>
            </v-text-field>
         </v-form>
      </div>

      <div class="inquiries__list-label" v-if="top_number && show_list_label && ! nofilter">
         Top {{ top_number }} mūsų siūlomi brokeriai
      </div>

      <div class="inquiries__list-loader" v-if="loading">
         <v-progress-circular
            :size="50"
            color="primary"
            indeterminate
         ></v-progress-circular>
      </div>

      <template v-if=" ! loading">
         <div
            v-for="item in items"
            :class="['inquiries__list-item inquiries__list-item--offer list-item', nofilter ? 'list-item--nofilter' : '']"
         >
            <div class="list-item__broker">
               <div class="list-item__broker-avatar">
                  <v-img
                     :src="item.avatar_url"
                     :width="nofilter ? 50 : 65"
                     :height="nofilter ? 50 : 65"
                     v-if="item.avatar_url"
                  >
                  </v-img>
               </div>
               <div class="list-item__broker-info">
                  <div class="list-item__broker-info-rating">
                     <v-rating
                        readonly
                        half-increments
                        dense
                        :size="20"
                        color="primary"
                        background-color="primary"
                        :value="item.rating"
                     >
                     </v-rating>
                  </div>
                  <div class="list-item__broker-info-name">{{ item.name }}</div>
               </div>
            </div>
            <div class="list-item__offer">
               <div class="list-item__offer-types" v-if="nofilter">{{ item.spheres_sum_up }}</div>
               <div class="list-item__offer-description">
                  <span class="list-item__offer-description-title" v-if="nofilter">"{{ item.name }}"</span>
                  <nl2br tag="span" :text="item.description"/>
               </div>
               <v-btn
                  color="primary"
                  width="100%"
                  class="v-btn--submit"
                  @click="handleClick(item)"
                  v-if=" ! hide_contact && item.canContact && inquiry_available.active"
               >
                  Susisiekti
               </v-btn>
               <v-btn
                  color="primary"
                  width="100%"
                  class="v-btn--submit"
                  :href="item.link"
                  v-if="hide_contact"
               >
                  Plačiau
               </v-btn>
            </div>
         </div>

         <template v-if=" ! items.length">
            <v-alert
               outlined
               class="v-alert--nt-info"
            >
               {{ nofilter ? 'Šiuo metu nėra pakankamai įmonių!' : 'Pasirinkti kriterijai neatitinka jokių įmonių!' }}
            </v-alert>
         </template>
      </template>

      <div class="inquiries__list-pagination" v-if="this.pages_number > 1">
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
   import URLParams from "../utils/URLParams";

   export default {
      name: "BrokerList",
      components: {
         Nl2br
      },
      data() {
         return {
            page: this.prop_current_page,
            pages_number: this.prop_pages_number,
            total_visible: this.prop_total_visible,
            items: this.prop_items,
            hide_contact: !this.inquiry_available,
            item_filter: false,
            show_list_label: true,
            loading: false,
            objects: [],
            form: {
               search: null,
               region: null,
               type: null,
               object: [],
            }
         }
      },
      props: {
         top_number: {
            type: [Number, Boolean],
            default() {
               return false
            }
         },
         prop_pages_number: {
            type: Number,
            default: 1,
         },
         prop_total_visible: {
            type: Number,
            default: 15
         },
         prop_current_page: {
            type: Number,
            default: 1,
         },
         prop_items: {
            type: Array
         },
         regions: {
            type: Array,
         },
         types: {
            type: Array,
         },
         inquiry_available: {
            type: [Boolean, Object],
            default: false
         },
         nofilter: {
            type: Boolean,
            default: false
         },
         inquiries: {
            type: Boolean,
            default: false,
         },
         getRoute: {
            type: String,
         }
      },
      methods: {
         changeObjects() {
            const self = this;

            self.form.object = [];

            this.types.forEach(function (type) {
               if (type.id === self.form.type) {
                  self.objects = type.properties;
               }
            })
         },

         handleClick(company) {
            this.$root.$emit('openMakeConnectionForm', company);
         },

         submit() {
            this.loading = true;
            this.show_list_label = false;

            axios.get(this.getRoute, {
               params: {
                  search: this.form.search,
                  region: this.form.region,
                  type: this.form.type,
                  properties: this.form.object,
                  inquiry: this.inquiry_available && this.inquiry_available.owner ? this.inquiry_available.id : false,
                  inquiry_token: this.inquiry_available && !this.inquiry_available.owner ? URLParams('inquiry_token', false) : false
               }
            })
               .then(response => {
                  this.items = response.data.items;
                  this.page = response.data.current_page;
                  this.pages_number = response.data.pages_number;
                  this.total_visible = response.data.total_visible;
                  this.current_page = response.data.current_page;
                  this.loading = false;
               })
         },

         nextPage(value) {
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
         },

         getUrlParam(parameter, defaultvalue = false) {
            var urlParams = new URLSearchParams(window.location.search);

            if (urlParams.has(parameter)) {
               return urlParams.get(parameter);
            } else {
               return defaultvalue
            }
         }
      },
      created: function () {
         let refresh_list = false;

         if (this.getUrlParam('type')) {
            this.form.type = Number(this.getUrlParam('type'));

            this.changeObjects();

            refresh_list = true;
         }

         if (this.getUrlParam('property')) {
            this.form.object.push(Number(this.getUrlParam('property')));

            refresh_list = true;
         }

         if (refresh_list) {
            this.submit();
         }
         // if (this.getUrlParam('type')) {
         //   console.log('vaziuojam')
         // }
      },
   }
</script>

<style scoped>

</style>
