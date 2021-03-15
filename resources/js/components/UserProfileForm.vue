<template>
   <ValidationObserver v-slot="{ valid }" ref="validator">
      <v-form
         class="form form--inquiry inquiry"
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
            rules="min:8|confirmed:password_confirmation"
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

         <v-btn
            color="primary"
            width="100%"
            @click="validate"
            class="v-btn--submit"
            :loading="loading"
         >
            Atnaujinti
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
         user: {
            type: Object,
         },
         postRoute: {
            type: String,
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
               password: null,
               password_confirmation: null,
            },
            clearFormData: false,
         }
      },
      methods: {
         async validate() {
            let form = await this.$refs.validator.validate();

            this.resetFormStates();

            if (form) {
               this.form.phone = this.form.phone.replace(/\s/g, '').trim();

               this.loading = true;
               this.submit();
            }
         },

         resetFormStates() {
            this.hideGlobalMessage();
         },

         // clearForm() {
         //    this.form = this.form_empty;
         //    this.$refs.validator.reset();
         // },

         submit() {
            axios.post(this.postRoute, this.getPreparedData(this.form))
               .then(response => {

                  this.clearForm()
                  Toaster('success', response.data)

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
      },
      created() {
         this.form.name = this.user.name
         this.form.email = this.user.email
         this.form.phone = this.user.phone
      }
   }
</script>
