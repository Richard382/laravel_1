<template>
   <ValidationObserver v-slot="{ valid }" ref="validator">
      <v-form
         class="form form--inquiry inquiry ml-5 mr-5" autocomplete="off"
      >
         <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="name"
         >
            <v-text-field
               v-model="form.name"
               label="Vardas Pavardė"
               placeholder="Vardenis Pavardenis"
               class="v-input--default v-input--labeled"
               color="custom"
               :error-messages="errors"
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
               autocomplete="new-password"
            ></v-text-field>
         </ValidationProvider>

          
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
                autocomplete="new-password"
                
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
               autocomplete="new-password"
            ></v-text-field>
         </ValidationProvider>
          
          <!--rules="required|isPhone:lt"-->
         <ValidationProvider
            :rules="{ required: true, isPhone: checkphonevalue()?'lt':false }"
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
               autocomplete="new-password"
            ></v-text-field>
         </ValidationProvider>

         <template v-if="agreementLink">
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
                   <!--Siųsdamas užklausą-->
                  <template v-slot:label>
                     <div>
                        Registruojantis sutinkate su <a
                        target="_blank"
                        @click.stop
                        :href="agreementLink"
                     >sąlygomis ir taisyklėmis</a>
                     </div>
                  </template>
               </v-checkbox>
            </ValidationProvider>
         </template>

         <v-btn
            color="primary"
            width="100%"
            @click="validate"
            class="v-btn--submit"
            :loading="loading"
         >
            Registruotis
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
   import {Toaster} from "../helpers/Toaster";

   export default {
      extends: FormEssentials,
      directives: {
         mask,
      },
      props: {
         agreementLink: {
            type: [String, Boolean],
            default() {
               return false;
            }
         },
         postRoute: {
            type: String
         }
      },
      components: {
         ValidationProvider,
         ValidationObserver,
      },
      data() {
         return {
            form: {
               name: null,
               email: null,
               phone: null,
               agreement: null,
               password: null,
               password_confirmation: null,
            },
            loading: false,
            submitted:false
         }
      },
      methods: {
          checkphonevalue(){
              let vali = false;
              if((this.form.phone && this.form.phone.replace(/[a-z|A-Z]/g, '').trim() !== "+370") || this.submitted ){
                  vali = true;
              }
              return vali;              
          },
         async validate() {
             this.submitted = true;
            console.log('validating');
            let form = await this.$refs.validator.validate();

            this.resetFormStates();

            if (form) {
               this.form.phone = this.form.phone.replace(/\s/g, '').trim();

               this.loading = true;
               this.submit();
            }else{
                const el = document.querySelector(".v-messages.error--text:first-of-type");
//                el.scrollIntoView();
                this.scrollToTargetAdjusted(el);
            }
         },
         scrollToTargetAdjusted(element){
            var headerOffset = 100;
            var elementPosition = element.offsetTop;
            var offsetPosition = elementPosition - headerOffset;

            window.scrollTo({
                 top: offsetPosition,
                 behavior: "smooth"
            });
        },
         handleSubmit(){},
         resetFormStates() {
            this.hideGlobalMessage();
         },

         submit() {
            axios.post(this.postRoute, this.getPreparedData(this.form))
               .then(response => {

                  this.clearForm();
                  Toaster('success', response.data)
                  setTimeout(function(){
                      window.location.href = '/';
                  },3000)
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
         }
      }
   }
</script>
