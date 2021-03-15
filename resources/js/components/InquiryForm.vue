<template>
   <ValidationObserver v-slot="{ valid }" ref="validator">
      <v-form
         class="form form--inquiry inquiry"
         ref="inquiry_form"
      >
         <div
            v-if="service"
            class="inquiry-info"
         >
            <strong class="inquiry-info__service">{{ service.type_user }}
               <v-btn
                  x-small
                  v-text="'Keisti'"
                  class="mt-0 ml-1"
                  color="primary"
                  @click="linkToHome"
               />
            </strong>
            <span
               class="inquiry-info__property_type"
            >{{ listPropertyTypes() }}</span><br>
            <span
               class="inquiry-info__property_type"
            >{{ listProperties() }}</span>
         </div>

         <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="service"
         >
            <v-select
               v-model="form.service"
               :items="services"
               item-text="name"
               item-value="id"
               label="Pasirinkite reikalingą paslaugą"
               :class="[(! service ? '' : 'd-none'), 'v-input--default v-input--labeled']"
               :error-messages="errors"
               :menu-props="{bottom: true, offsetY: true, maxHeight: 240}"
            ></v-select>
         </ValidationProvider>

         <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="property_type"
         >
            <v-autocomplete
               v-model="form.property_type"
               :items="property_types"
               name="property_types"
               item-text="name"
               item-value="id"
               label="Pasirinkite objekto tipą"
               :class="[(! property_types_pre ? '' : 'd-none'), 'v-input--default v-input--labeled']"
               class="v-input--default v-input--labeled"
               :error-messages="errors"
               multiple
               :menu-props="{bottom: true, offsetY: true, maxHeight: 240}"
               @change="updateObjects"
            >
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
                     class="caption"
                  >(+{{ form.property_type.length - 1 }} kiti)</span>
               </template>
            </v-autocomplete>
         </ValidationProvider>

         <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="property_type"
         >
            <v-autocomplete
               v-model="form.properties"
               :items="temporary_properties"
               name="property_types"
               item-text="name"
               item-value="id"
               label="Pasirinkite objektą"
               :class="[(! properties_pre ? '' : 'd-none'), 'v-input--default v-input--labeled']"
               class="v-input--default v-input--labeled"
               :error-messages="errors"
               multiple
               :menu-props="{bottom: true, offsetY: true, maxHeight: 240}"
            >
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
                     class="caption"
                  >(+{{ form.properties.length - 1 }} kiti)</span>
               </template>
            </v-autocomplete>
         </ValidationProvider>

         <ValidationProvider
            rules="required|max:300"
            v-slot="{ errors }"
            name="requirements"
         >
            <v-textarea
               v-model="form.requirements"
               label="Įrašykite savo poreikius"
               class="v-input--default v-input--labeled v-input--hint-bold"
               color="custom"
               :error-messages="errors"
               persistent-hint
               hint="Kuo tiksliau aprašykite savo poreikius. Tokiu būdu jūsų užklausa susidomės tik ypač suinteresuoti NT paslaugų teikėjai."
            ></v-textarea>
         </ValidationProvider>

         <template v-if=" ! $root.authenticated()">
            <ValidationProvider
               rules="required"
               v-slot="{ errors }"
               name="name"
            >
               <v-text-field
                  v-model="form.name"
                  label="Jūsų vardas"
                  placeholder="Įrašykite.."
                  class="v-input--default v-input--labeled"
                  color="custom"
                  :error-messages="errors"
               ></v-text-field>
            </ValidationProvider>

            <ValidationProvider
               rules="required|isPhone:lt"
               v-slot="{ errors }"
               name="phone"
            >
               <v-text-field
                  v-model="form.phone"
                  label="Telefono numeris"
                  placeholder="+370 600 44 200"
                  class="v-input--default v-input--labeled"
                  color="custom"
                  :error-messages="errors"
                  type="tel"
                  v-mask="'+370 ### ## ###'"
               ></v-text-field>
            </ValidationProvider>

            <ValidationProvider
               rules="required|email"
               v-slot="{ errors }"
               name="email"
            >
               <v-text-field
                  v-model="form.email"
                  label="El. pašto adresas"
                  placeholder="vardas.pavarde@gmail.com"
                  class="v-input--default v-input--labeled"
                  color="custom"
                  :error-messages="errors"
                  type="email"
               ></v-text-field>
            </ValidationProvider>
         </template>

         <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="location"
         >
             <v-select
                 v-if="checkLocation('single')"
               v-model="form.location[0]"
               :items="cities"
               item-text="name"
               item-value="id"
               dense
               no-data-text="Nėra tokios vietovės"
               class="v-input--labeled v-input--no-label"
               placeholder="Pasirinkite vietovę"
               :error-messages="errors"
               :menu-props="{bottom: true, offsetY: true, maxHeight: 240}"
            ></v-select>
            <v-select
                v-if="checkLocation('multiple')"
               v-model="form.location"
               :items="cities"
               item-text="name"
               item-value="id"
               dense
               chips
               multiple
               no-data-text="Nėra tokios vietovės"
               class="v-input--labeled v-input--no-label"
               placeholder="Pasirinkite vietovę"
               :error-messages="errors"
               :menu-props="{bottom: true, offsetY: true, maxHeight: 240}"
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
                     class="black--text caption"
                  >(+{{ form.location.length - 1 }} kiti)</span>
               </template>
            </v-select>
         </ValidationProvider>

         <v-row>
            <v-col  v-if="checkPriceButton('pricefrom')">
               <ValidationProvider
                  v-slot="{ errors }"
                  name="price_from"
               >
                  <number-field
                     v-model="form.price_from"
                     :label="checkPriceButton('priceto')?'Kaina nuo':'Kaina'"
                     placeholder="pvz. nuo €5000"
                     :errors="errors"
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
                     label="Kaina iki"
                     placeholder="pvz. iki €20 000"
                     :errors="errors"
                  />
               </ValidationProvider>
            </v-col>
         </v-row>

         <ValidationProvider
            v-slot="{ errors }"
            name="in_hurry"
         >
            <v-checkbox
               v-model="form.in_hurry"
               on-icon=""
               off-icon=""
               value="1"
               class="v-input--condition v-input--custom-checkbox"
               :ripple="false"
               :error-messages="errors"
            >
               <template v-slot:label>
                  <div>
                     <strong>Skubate? Pažymėkite šį langelį ir su Jumis NT paslaugų teikėjas susisieks per
                        30min.</strong>
                  </div>
               </template>
            </v-checkbox>
         </ValidationProvider>

         <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="agreement"
         >
            <v-checkbox
               v-if="agreementLink"
               v-model="form.agreement"
               on-icon=""
               off-icon=""
               value="1"
               class="v-input--condition v-input--custom-checkbox v-input--halved-margin"
               :ripple="false"
               :error-messages="errors"
            >
               <template v-slot:label>
                  <div>
                     Siųsdamas užklausą sutinkate su <a
                     target="_blank"
                     @click.stop
                     :href="agreementLink"
                  >sąlygomis ir taisyklėmis</a>
                  </div>
               </template>
            </v-checkbox>
         </ValidationProvider>

         <template v-if=" ! $root.authenticated()">
            <ValidationProvider
               v-slot="{ errors }"
               name="register"
            >
               <v-checkbox
                  v-model="form.register"
                  on-icon=""
                  off-icon=""
                  value="1"
                  class="v-input--condition v-input--custom-checkbox v-input--halved-margin"
                  :ripple="false"
                  :error-messages="errors"
               >
                  <template v-slot:label>
                     <div>
                        Norite užsiregistruoti sistemoje?
                     </div>
                  </template>
               </v-checkbox>
            </ValidationProvider>

            <template v-if="form.register">
               <ValidationProvider
                  vid="password"
                  rules="required|min:8"
                  v-slot="{ errors }"
                  name="password"
               >
                  <v-text-field
                     v-model="form.password"
                     :error-messages="errors"
                     label="Slaptažodis"
                     type="password"
                     class="v-input--default v-input--labeled"
                  >
                  </v-text-field>
               </ValidationProvider>
               <ValidationProvider
                  vid="password_confirmation"
                  rules="required|confirmed:password"
                  v-slot="{ errors }"
                  name="password_confirmation"
               >
                  <v-text-field
                     v-model="form.password_confirmation"
                     :error-messages="errors"
                     label="Patvirtinkite Slaptažodį"
                     type="password"
                     class="v-input--default v-input--labeled"
                  ></v-text-field>
               </ValidationProvider>
            </template>
         </template>

         <v-btn
            color="primary"
            width="100%"
            @click="validate"
            class="v-btn--submit"
            :loading="loading"
         >
            GAUTI PASIŪLYMUS
         </v-btn>
         <v-alert
            dense
            outlined
            :type="global_message_type"
            v-if="global_message"
         >
            {{ global_message }}
         </v-alert>
      </v-form>
   </ValidationObserver>
</template>

<script>
   import FormEssentials from "./base/FormEssentials";
   import {ValidationProvider, ValidationObserver} from "vee-validate";
   import {mask} from "vue-the-mask";
   import Redirect from "../utils/Redirect";
   import updateObjects from "../mixins/updateObjects";
   import NumberField from "./form/NumberField";
   import {Toaster} from "../helpers/Toaster";

   export default {
      extends: FormEssentials,
      mixins: [updateObjects],
      directives: {
         mask,
      },
      components: {
         NumberField,
         ValidationProvider,
         ValidationObserver,
      },
      data() {
         return {
            form: {
               // name: 'Albinas Venclovas',
               // requirements: 'Vivamus quis mi. Sed magna purus, fermentum eu, tincidunt eu, varius ut, felis. Cras dapibus. Nam adipiscing. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.',
               // location: [5, 8],
               // price: 'nuo 300e',
               // phone: '+370 545 65 465',
               // email: 'guoga@gmail.com',
               // agreement: null,
               // in_hurry: null,
               // register: null,
               // password: null,
               // password_confirmation: null,
               // property_type: this.property_type.id,
               // service: this.service.id
               name: null,
               requirements: null,
               location: [],
               phone: null,
               email: null,
               agreement: null,
               in_hurry: null,
               register: null,
               price_from: this.price_from,
               price_to: this.price_to,
               password: null,
               password_confirmation: null,
               property_type: [],
               properties: [],
               service: this.service.id,
               contact_company: this.contactCompany
            },
            form_empty: {
               name: null,
               requirements: null,
               location: [],
               phone: null,
               email: null,
               agreement: null,
               in_hurry: null,
               register: null,
               price_from: null,
               price_to: null,
               password: null,
               password_confirmation: null,
               property_type: [],
               properties: [],
               service: this.service.id
            },
            loading: false,
         }
      },
      props: {
         cities: {
            type: Array,
         },
         services: {
            type: Array,
         },
         property_types: {
            type: Array,
         },
         price_from: {
            type: String
         },
         price_to: {
            type: String
         },
         location: {
            type: Array
         },
         agreementLink: {
            type: [String, Boolean],
            default() {
               return false;
            }
         },
         property_types_pre: {
            type: [Array, Boolean],
            required: false,
            default() {
               return false
            }
         },
         properties_pre: {
            type: [Array, Boolean],
            required: false,
            default() {
               return false
            }
         },
         service: {
            type: [Object, Boolean],
            required: false,
            default() {
               return false
            }
         },
         contactCompany: {
            type: [Number, Boolean],
            required: false,
            default() {
               return 0
            }
         },
         postRoute: {
            type: String
         }
      },
      created() {
         this.location.forEach(item => {
            this.form.location.push(parseInt(item));
         });

         if (this.property_types_pre) {
            this.property_types_pre.forEach(type => {
               this.form.property_type.push(type.id)
            });
         }

         if (this.properties_pre) {
            this.properties_pre.forEach(type => {
               this.form.properties.push(type.id)
            });
         }
      },
      methods: {
         async validate() {
            let form = await this.$refs.validator.validate();

            this.resetFormStates();

            if (form) {
               if (!this.$root.authenticated()) {
                  this.form.phone = this.form.phone.replace(/\s/g, '').trim();
               }

               this.loading = true;
               this.submit();
            }
         },

         listPropertyTypes() {
            return this.property_types_pre.map(function (type) {
               return type.name;
            }).join(", ");
         },

         listProperties() {
            return this.properties_pre.map(function (prop) {
               return prop.name;
            }).join(", ");
         },

         linkToHome() {
            let url = '/?';
            let params = {
               location: this.form.location,
               price_from: this.form.price_from,
               price_to: this.form.price_to,
               service: this.form.service,
               property_type: this.form.property_type,
               properties: this.form.properties
            };

            Redirect(url, params);
         },

         selectedAllCities(data) {
            return data.location.length === (this.cities.length - 1)
         },

         selectedSomeCities(data) {
            return data.location.length > 0 && !this.selectedAllCities(data)
         },

         icon(data) {
            if (this.selectedAllCities(data)) return 'mdi-close-box'
            if (this.selectedSomeCities(data)) return 'mdi-minus-box'
            return 'mdi-checkbox-blank-outline'
         },

         toggleCities(data) {
            this.$nextTick(() => {
               if (this.selectedAllCities(data)) {
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

         resetFormStates() {
            this.hideGlobalMessage();
         },

         clearForm() {
            this.form = this.form_empty;
            this.$refs.validator.reset();
         },

         submit() {
            axios.post(this.postRoute, this.getPreparedData(this.form))
               .then(response => {

                  Toaster('success', response.data)

                  setTimeout(function() {
                     Redirect(response.data.redirect);
                  }, 5000)

               })
               .catch(error => {

                  if (error.response.status === 422) {
                     this.$refs.validator.setErrors(error.response.data.errors);
                  }

                  if (error.response.data.message) {
                     Toaster('error', error.response.data)
                  }

                  this.loading = false;
               });
         },
         checkPriceButton(type){
            if(this.form.properties.length){
                let pricetype = '';
                let proprty = this.properties_pre.find(x => x.id == this.form.properties[this.form.properties.length-1])
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
