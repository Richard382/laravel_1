<template>
   <v-dialog v-model="dialog" width="600px">
      <v-card>
         <v-btn
            icon
            class="v-btn--close-dialog"
            @click="dialog = false"
         >
            <v-icon>mdi-close</v-icon>
         </v-btn>
         <v-card-title>
            <h2>Užklausa nr. {{ item.id }} <small>Susidomėjo: {{ item.offer_count }}</small></h2>
         </v-card-title>
         <v-card-text>
            <div class="inquiry-info">
               <strong class="inquiry-info__service">Paslauga, objektas, lokacija, kaina:</strong>
               <span class="inquiry-info__property_type">{{ item.sum_up }}</span>
            </div>
            <div class="inquiry-info">
               <strong class="inquiry-info__service">Užklausa</strong>
               <nl2br tag="span" :text="item.requirements" class="inquiry-info__property_type"/>
            </div>

            <ValidationObserver v-slot="{ valid }" ref="validator">
               <v-form
                  class="form"
               >
                  <ValidationProvider
                     rules="required|max:150"
                     v-slot="{ errors }"
                     name="price"
                  >
                     <number-field
                        v-model="form.price"
                        label="Siūlyti kainą"
                        placeholder="Įrašykite sumą"
                        :errors="errors"
                     />
                  </ValidationProvider>

                  <ValidationProvider
                     rules="required|max:300"
                     v-slot="{ errors }"
                     name="offer"
                  >
                     <v-textarea
                        v-model="form.offer"
                        label="Jūsų pasiulymas"
                        class="v-input--default v-input--labeled"
                        placeholder="Įrašykite.."
                        color="custom"
                        :error-messages="errors"
                     ></v-textarea>
                  </ValidationProvider>

                  <v-btn
                     color="primary"
                     width="100%"
                     @click="validate"
                     class="v-btn--submit"
                     :loading="loading"
                  >
                     Siūlyti
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
         </v-card-text>
      </v-card>
   </v-dialog>
</template>

<script>
   import FormEssentials from "./base/FormEssentials";
   import {ValidationProvider, ValidationObserver} from "vee-validate";
   import NumberField from "./form/NumberField";

   export default {
      extends: FormEssentials,
      name: "OfferForm",
      components: {
         NumberField,
         ValidationProvider,
         ValidationObserver,
      },
      data() {
         return {
            form: {
               price: null,
               offer: null,
               inquiry_id: null,
            },
            form_empty: {
               price: null,
               offer: null,
               inquiry_id: null
            },
            item: {
               id: null,
               sum_up: null,
               requirements: '',
            },
            dialog: false,
            loading: false,
         }
      },
      methods: {
         async validate() {
            let form = await this.$refs.validator.validate();

            this.resetFormStates();

            if (form) {
               this.loading = true;
               this.submit();
            }
         },

         resetFormStates() {
            this.hideGlobalMessage();
         },

         clearForm() {
            this.form = this.form_empty;
            //this.$refs.validator.reset();
         },

         submit() {
            let self = this;
            this.form.inquiry_id = this.item.id;

            axios.post('offers', this.getPreparedData(this.form))
               .then(response => {

                  this.clearForm();
                  this.showGlobalMessage(response.data.message, 'success');

                  setTimeout(function () {
                     self.redirectTo(response.data.redirect);
                  }, 5000)

               }).catch(error => {

               if (error.response.status == 422) {
                  this.$refs.validator.setErrors(error.response.data.errors);
               }

               if (error.response.data.message) {
                  this.showGlobalMessage(error.response.data.message);
               }

               this.loading = false;
            });
         }
      },
      mounted() {
         this.$root.$on('openModal', (item) => {
            this.resetFormStates();
            this.clearForm();
            this.item = item;
            this.dialog = true;
         })
      }
   }
</script>
