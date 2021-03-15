<template>
<v-container fluid grid-list-md text-xs-center id="centerbox_container">
<v-card
    class="mx-auto center-box my-12"
    max-width="1200"
    outlined
  >
    <v-list-item>
      <v-list-item-content class="ribbon-holder">
        <div class="ribbon ribbon-top-left"><span>Pirk, parduok, ieškok</span></div>
        <v-list-item-title class="headline mb-1 mt-4 text-center">
           <b class="hidden-sm-and-down">NT PASIŪLYMAI per 1 val. !</b>
           <b class="hidden-md-and-up">NT PASIŪLYMAI <br /> per 1 val. !</b>
        </v-list-item-title>
      </v-list-item-content>

    </v-list-item>
    <ValidationObserver v-slot="{ valid }" ref="getanoffer">
      <div
         class="form form--getanoffer"
         action="registeroffer"
         ref="form"
      >
      <v-row class="ml-2 mr-2">
        <v-col cols="12" sm="6">
            <v-row class="">
                <v-col
                  cols="12"
                  sm="2"
                  class="hidden-sm-and-down text-center"
                >
                  <v-avatar
                    color="#fff"
                    size="30"
                    top="18"
                    class="select_index_circle"
                  >1</v-avatar>
                  </v-col>
                  <v-col
                cols="12"
                sm="10">
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
                      placeholder="Pasirinkite paslaugą"
                      :error-messages="errors"
                      :menu-props="{bottom: true, offsetY: true, maxHeight: 400}"
                      solo
                      attach
                      @change="handleChange"
                    ></v-select>
                </ValidationProvider>
                </v-col>
            </v-row>

            <v-row class="">
                <v-col
                  class="hidden-sm-and-down text-center"
                  cols="12"
                  sm="2">
                  <v-avatar
                    color="#fff"
                    size="30"
                    top="18"
                    class="select_index_circle"
                  >2</v-avatar>
                  </v-col>
                  <v-col
                cols="12"
                sm="10">
                <ValidationProvider
                  rules="required"
                  v-slot="{ errors }"
                  name="property_type"
               >
                  <v-select
                     v-if="form.service"
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
                  <v-select
                     v-else
                     name="property_types"
                     item-text="name"
                     item-value="id"
                     placeholder="Pasirinkite objekto tipą"
                     attach
                     solo
                     disabled
                  >
                  </v-select>
               </ValidationProvider>

                </v-col>
            </v-row>

            <v-row class="">
                <v-col
                  class="hidden-sm-and-down text-center"
                  cols="12"
                  sm="2">
                  <v-avatar
                    color="#fff"
                    size="30"
                    top="18"
                    class="select_index_circle"
                  >3</v-avatar>
                  </v-col>
                  <v-col
                cols="12"
                sm="10">
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
                  <template v-else>
                     <v-select
                        placeholder="Pasirinkite objektą"
                        attach
                        solo
                        disabled
                     ></v-select>
                  </template>
               </ValidationProvider>

                </v-col>
            </v-row>
        </v-col>
        <v-col cols="12" sm="6">
            <v-row class="">
                <v-col
                  class="hidden-sm-and-down text-center"
                  cols="12"
                   sm="2">
                  <v-avatar
                    color="#fff"
                    size="30"
                    top="18"
                    class="select_index_circle"
                  >4
                  </v-avatar>
                  </v-col>
                  <v-col
                cols="12"
                sm="10">
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
                  <template v-if="!partOne">
                     <v-select
                        placeholder="Vietovės"
                        chips
                        attach
                        no-data-text="Nėra tokios vietovės"
                        solo
                        disabled
                     ></v-select>
                  </template>
               </ValidationProvider>
                </v-col>
            </v-row>

            <v-row class="">
                <v-col
                  class="hidden-sm-and-down text-center"
                  cols="12"
                  sm="2">
                  <v-avatar
                    color="#fff"
                    size="30"
                    top="18"
                    class="select_index_circle"
                  >5</v-avatar>
                  </v-col>
               <v-col
                cols="12"
                sm="10">
               <template>
                  <v-row v-if="partOne">
                     <v-col v-if="checkPriceButton('pricefrom')">
                        <ValidationProvider
                           v-slot="{ errors }"
                           name="price_from"
                        >
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
                     <v-col v-else>
                        <ValidationProvider
                           v-slot="{ errors }"
                           name="price_from"
                        >
                           <v-text-field
                              type="number"
                              :placeholder="form.price_from ?'Kaina nuo':'Kaina'"
                              custom-class="v-text-field--justt"
                              :errors="errors"
                              solo
                              disabled
                           />
                        </ValidationProvider>
                     </v-col>
                  </v-row>
                  <v-row v-else>
                     <v-col>
                        <ValidationProvider
                           v-slot="{ errors }"
                           name="price_from"
                        >
                           <v-text-field
                              type="number"
                              :placeholder="checkPriceButton('priceto')?'Kaina nuo':'Kaina'"
                              custom-class="v-text-field--justt"
                              :errors="errors"
                              solo
                              disabled
                           />
                        </ValidationProvider>
                     </v-col>
                  </v-row>
               </template>
                </v-col>
            </v-row>

            <v-row class="">
                <v-col
                  cols="12"
                  sm="2"
                  class="hidden-sm-and-down"
                >
                </v-col>
               <v-col
                cols="12"
                sm="4">
               <v-btn
                  color="primary"
                  width="100%"
                  @click="validate"
                  class="primary"
                  large
                  :loading="loading"
                  :disabled=" ! valid"
               >
                  Tęsti
               </v-btn>
                </v-col>
               <div class="col-sm-12 col-12 mb-1 mt-4 text-center">
                  <p class="white--text">Jūsų užklausą gaus {{specialNT}} NT specialistų</p>
               </div>
            </v-row>
        </v-col>
      </v-row>
   </div>
   </ValidationObserver>
  </v-card>

<v-card
    class="mx-auto"
    max-width="1000"
    outlined
    v-show="showinquiryform"
  >
   <ValidationObserver v-slot="{ valid }" ref="validator">
      <v-form
         class="form form--inquiry inquiry"
         ref="inquiry_form"
      >
         <v-row class="ml-2 mr-2">
            <h4 class="white--text">
               Baikite pildyti informaciją sėkmingai užklausai!
            </h4>
            
            <v-col cols="12" sm="12">
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
            </v-col>
               
            <v-col>
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
            </v-col>
            <v-col
               cols="12"   
               sm="12"
            >
               
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
            </v-col>
            <!-- //////////////////////////// -->
            <v-col
            cols="12"
            sm="12"
            >
               <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="agreement"
         >
            <v-checkbox
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
            </v-col>

            
            <v-col
            cols="12"
            sm="12"
            >
               <template v-if=" ! $root.authenticated()">
                  <ValidationProvider
                     v-slot="{ errors }"
                     name="register"
                  >
                     <v-checkbox
                        v-model="form.register"
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
               color="primary mb-10"
               width="100%"
               @click="validateInpuiry"
               class="v-btn--submit"
               :loading="inquiryloading"
            >
               GAUTI PASIŪLYMUS
            </v-btn>
         
            </v-col>
         </v-row>
      </v-form>
   </ValidationObserver>
  </v-card>
   
  </v-container>
  
  <!-- </v-row> -->
</template>

<script>
   import {ValidationProvider, ValidationObserver} from "vee-validate";
   import FormEssentials from "./base/FormEssentials";
   import Redirect from "../utils/Redirect";
   import URLParams from "../utils/URLParams";
   import updateObjects from "../mixins/updateObjects";
   import NumberField from "./form/NumberField";
   import {mask} from "vue-the-mask";
   import {Toaster} from "../helpers/Toaster";

   export default {
      components: {
         NumberField,
         ValidationProvider,
         ValidationObserver,
      },
      extends: FormEssentials,
      directives: {
         mask
      },
      mixins: [updateObjects],
      data() {
         return {
            specialNT: 0,
            partOne: false,
            show: false,
            form: {
               location: [],
               service: null,
               property_type: [],
               properties: [],
               price_from: null,
               price_to: null,
               
               name: null,
               requirements: null,
               phone: null,
               email: null,
               agreement: null,
               in_hurry: null,
               register: null,
               password: null,
               password_confirmation: null,
               contact_company: null
            },
            loading: false,
            priceerror:false,
            showinquiryform: false,
            inquiryloading: false,
         }
      },
      props: {
         agreementLink: 'https://cont.lt/salygos-ir-taisykles',
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
         },
         agreementLink: {
            type: [String, Boolean],
            default() {
               return false;
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
               // this.loading = true;
               this.showinquiryform = true;
               let parameters = this.form;
               // Redirect(this.redirectRoute + '?', parameters);
               console.log(this.form, "form params")
               this.getspecialNT(parameters)
            }
         },
         getspecialNT(formdata) {
            console.log("ok", formdata, "check if submitted")

            axios.post('/getspecialNT', formdata)
            .then(response => {
               this.specialNT = response.data.data;
               console.log(response.data, "response")
            })
            .catch(error => {
               console.log(error)
               this.specialNT = 0;
            })
         },
         async validateInpuiry() {
            let form = await this.$refs.validator.validate();

            // this.resetFormStates();
            console.log(form, "form ")
            if (form) {
               if (!this.$root.authenticated()) {
                  this.form.phone = this.form.phone.replace(/\s/g, '').trim();
               }

               this.inquiryloading = true;
               this.submit();
            }
         },
         submit() {
            axios.post('/uzklausos', this.getPreparedData(this.form))
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
