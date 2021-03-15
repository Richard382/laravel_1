<template>
   <ValidationObserver v-slot="{ valid }" ref="getanoffer">
      <v-form
         v-if="show"
         class="form form--getanoffer"
         action="register"
         ref="form"
      >
         <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="service"
         >
            <v-select
               v-model="form.service"
               :items="services"
               name="service"
               item-text="name"
               item-value="id"
               placeholder="Pasirinkite reikalingą paslaugą"
               :error-messages="errors"
               :menu-props="{bottom: true, offsetY: true, maxHeight: 400}"
               solo
               attach
               @change="handleChange"
            ></v-select>
         </ValidationProvider>

         <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="property_type"
         >
            <v-select
               v-model="form.property_type[0]"
               :items="property_types"
               name="property_types"
               item-text="name"
               item-value="id"
               placeholder="Pasirinkite objekto tipą"
               :error-messages="errors"
               attach
               :menu-props="{bottom: true, offsetY: true, maxHeight: 400}"
               solo
               @change="handleChange(); updateObjects()"
            >
            </v-select>
         </ValidationProvider>

         <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            nam="properties"
         >
            <template v-if="partOne">
               <v-select
                  v-model="form.properties[0]"
                  :items="temporary_properties"
                  item-text="name"
                  item-value="id"
                  placeholder="Pasirinkite objektą"
                  :error-messages="errors"
                  attach
                  :menu-props="{bottom: true, offsetY: true, maxHeight: 400}"
                  solo
               >

               </v-select>
            </template>
         </ValidationProvider>

         <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="cities"
         >
             <template v-if="partOne && checkLocation('single')">
               <v-select
                  v-model="form.location[0]"
                  :items="cities"
                  attach
                  placeholder="Vietovės"
                  item-text="name"
                  item-value="id"
                  no-data-text="Nėra tokios vietovės"
                  :error-messages="errors"
                  :menu-props="{bottom: true, offsetY: true, maxHeight: 400}"
                  solo
               >
               </v-select>
            </template>
             <template v-if="partOne && checkLocation('multiple')">
               <v-select
                  v-model="form.location"
                  :items="cities"
                  placeholder="Vietovės"
                  item-text="name"
                  item-value="id"
                  chips
                  multiple
                  attach
                  no-data-text="Nėra tokios vietovės"
                  :error-messages="errors"
                  :menu-props="{bottom: true, offsetY: true, maxHeight: 400}"
                  solo
               >
                  <template v-slot:prepend-item>
                     <v-list-item
                        ripple
                        @click="toggleCities(form)"
                     >
                        <v-list-item-action>
                           <v-icon :color="form.location.length > 0 ? 'indigo darken-4' : ''">{{ icon(form) }}</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                           <v-list-item-title>Visa Lietuva</v-list-item-title>
                        </v-list-item-content>
                     </v-list-item>
                  </template>
                  <template v-slot:item="data">
                     <v-list-item-action>
                        <v-checkbox
                           :input-value="data.attrs.inputValue"
                           color="primary"
                        ></v-checkbox>
                     </v-list-item-action>
                     <v-list-item-content>
                        <v-list-item-title v-html="data.item.name"></v-list-item-title>
                     </v-list-item-content>
                  </template>
                  <template v-slot:selection="{ item, index }">
                     <v-chip v-if="index < 1">
                        <span>{{ item.name }}</span>
                     </v-chip>
                     <span
                        v-if="index === 1"
                        class="white--text caption"
                     >(+{{ form.location.length - 1 }} kiti)</span>
                  </template>
               </v-select>
            </template>
         </ValidationProvider>

         <template v-if="partOne">
            <v-row>
               <v-col v-if="checkPriceButton('pricefrom')">
                  <ValidationProvider
                     v-slot="{ errors }"
                     name="price_from"
                  >
                      
                        <!--placeholder="Kaina nuo"-->
                     <number-field
                        v-model="form.price_from"
                        :placeholder="checkPriceButton('priceto')?'Kaina nuo':'Kaina'"
                        custom-class="v-text-field--justt"
                        :errors="errors"
                        solo
                        @input="validateErrors"
                     />
                  </ValidationProvider>
               </v-col>
                <v-col v-if="checkPriceButton('priceto')">
                  <ValidationProvider
                     :rules="{'min_value': (form.price_from ? (parseInt(form.price_from)+1) : 1)}"
                     v-slot="{ errors }"
                     name="price_to"
                  >
                     <number-field
                        v-model="form.price_to"
                        placeholder="Kaina iki"
                        custom-class="v-text-field--justt"
                        :errors="errors"
                        solo
                        @input="validateErrors"
                     />
                  </ValidationProvider>
               </v-col>
            </v-row>
         </template>

         <v-btn
            color="primary"
            width="100%"
            @click="validate"
            class="v-btn--submit v-btn--no-gutter"
            :loading="loading"
            :disabled=" ! valid"
         >
            Tęsti
         </v-btn>
      </v-form>
   </ValidationObserver>
</template>

<script>
   import {ValidationProvider, ValidationObserver} from "vee-validate";
   import Redirect from "../utils/Redirect";
   import URLParams from "../utils/URLParams";
   import updateObjects from "../mixins/updateObjects";
   import NumberField from "./form/NumberField";

   export default {
      components: {
         NumberField,
         ValidationProvider,
         ValidationObserver,
      },
      mixins: [updateObjects],
      data() {
         return {
            partOne: false,
            show: false,
            form: {
               location: [],
               service: null,
               property_type: [],
               properties: [],
               price_from: null,
               price_to: null,
            },
            loading: false,
            priceerror:false,
         }
      },
      props: {
         preset_cities: {
            type: Array,
            default() {
               return [];
            }
         },
         preset_service: {
            type: [Number, Boolean],
            default() {
               return false;
            }
         },
         preset_property_type: {
            type: [Number, Boolean, Array],
            default() {
               return false;
            }
         },
         preset_properties: {
            type: [Number, Boolean, Array],
            default() {
               return false;
            }
         },
         preset_price_from: {
            type: [String, Boolean],
            default() {
               return false
            }
         },
         preset_price_to: {
            type: [String, Boolean],
            default() {
               return false
            }
         },
         services: {
            type: Array,
         },
         property_types: {
            type: Array,
         },
         cities: {
            type: Array,
         },
         redirectRoute: {
            type: String,
         }
      },
      created() {
         let self = this;

         // Fix for safari
         setTimeout(function() {
            self.show = true;
         })

         if (this.preset_cities) {
            this.preset_cities.forEach(item => {
               this.form.location.push(parseInt(item));
            });
         }

         if (this.preset_service) {
            this.form.service = parseInt(this.preset_service)
         }

         if (this.preset_property_type) {
            this.preset_property_type.forEach(item => {
               this.form.property_type.push(parseInt(item));
            });

            this.updateObjects()
         }

         if (this.preset_properties) {
            this.preset_properties.forEach(item => {
               this.form.properties.push(parseInt(item));
            });
         }

         if (this.preset_price_from) {
            this.form.price_from = this.preset_price_from
         }

         if (this.preset_price_to) {
            this.form.price_to = this.preset_price_to
         }

         this.handleChange()
      },
      methods: {
          validateErrors(){
              this.$refs.getanoffer.validate();
          },
          
         async validate() {
            let form = await this.$refs.getanoffer.validate();

            if(this.checkPriceButton('pricefrom')){
                if (
                   !this.form.price_from
                   && ! this.form.price_to
                ) {
//                    this.priceerror = true;
                   this.$refs.getanoffer.setErrors({
                      price_from: 'Privaloma įvesti bent vieną reikšmę!'
                   })

                   return;
                }
            }

            if (form) {
               this.loading = true;

               let parameters = this.form;
               Redirect(this.redirectRoute + '?', parameters);
            }
         },

         handleChange() {
            if (this.form.service && this.form.property_type.length > 0) {
               this.partOne = true;

               return;
            }

            this.partOne = false;
         },

         selectedAllObjects(data) {
            return data.property_type.length === this.property_types.length
         },

         selectedSomeObjects(data) {
            return data.property_type.length > 0 && !this.selectedAllObjects(data)
         },

         selectedAllCities(data) {
            return data.location.length === (this.cities.length - 2)
         },

         selectedSomeCities(data) {
            return data.location.length > 0 && !this.selectedAllCities(data)
         },

         iconProperties(data) {
            if (this.selectedAllObjects(data)) return 'mdi-close-box'
            if (this.selectedSomeObjects(data)) return 'mdi-minus-box'
            return 'mdi-checkbox-blank-outline'
         },

         icon(data) {
            if (this.selectedAllCities(data)) return 'mdi-close-box'
            if (this.selectedSomeCities(data)) return 'mdi-minus-box'
            return 'mdi-checkbox-blank-outline'
         },

         toggleObjects(data) {
            this.$nextTick(() => {
               if (this.selectedAllObjects(data)) {
                  data.property_type = ''
               } else {
                  data.property_type = [];

                  this.property_types.forEach(property => {
                     if (!property.divider) {
                        data.property_type.push(property)
                     }
                  });
                  //data.cities = this.cities.slice()
               }
            })
         },

         toggleCities(data) {
            this.$nextTick(() => {
//               if (this.selectedAllCities(data)) {
               if (data.location.length) {
                  data.location = ''
               } else {
                  data.location = [];

                  this.cities.forEach(city => {
                     if (!city.divider) {
                        data.location.push(city.id)
                     }
                  });
                  //data.cities = this.cities.slice()
               }
            })
         },
         checkPriceButton(type){
            if(this.form.properties.length){
                let pricetype = '';
                let proprty = this.temporary_properties.find(x => x.id == this.form.properties[this.form.properties.length-1])
                if(proprty && (proprty.pricetype && proprty.pricetype!="silenced")){
                    pricetype = proprty.pricetype;
                }else{
                    let servc = this.services.find(x => x.id == this.form.service);
                    if(this.form.service && servc){
                        pricetype = servc.pricetype;
                    }
                }
                if(pricetype){
                    if((type == 'pricefrom' && pricetype !='noprice') ||
                       (type == 'priceto' && pricetype =='range')){
                        return true;
                    }
                }
            }
            return false;
            
         },
         checkLocation(type){
            if(type == 'single') {
                return (this.cities.length == 1 || this.services.find(x => x.id == this.form.service).fixedlocation == '1')
            }else{
                return this.cities.length > 1 && this.services.find(x => x.id == this.form.service).fixedlocation == '0';
            }
         }
      }
   }
</script>
