<template>
   <ValidationObserver v-slot="{ valid }" ref="validator">
      <v-form
         class="form"
      >

         <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="representative"
         >
            <v-text-field
               v-model="form.name"
               label="JŪSŲ VARDAS IR PAVARDĖ"
               placeholder="Vardas Pavardė"
               class="v-input--default v-input--labeled"
               color="custom"
               :error-messages="errors"
            ></v-text-field>
         </ValidationProvider>

         <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="agency"
         >
            <v-text-field
               v-model="form.agency"
               label="KAS ESATE, KĄ ATSTOVAUJATE"
               placeholder="pvz: CONT arba NT brokeris"
               class="v-input--default v-input--labeled"
               color="custom"
               :error-messages="errors"
            ></v-text-field>
         </ValidationProvider>

         <image-upload-field
            name="avatar"
            label="Pridėkite savo profilio nuotrauką"
            sub_text="Maks. nuotraukos dydis 500x500px"
            :file.sync="form.avatar"
            :preset="user.avatar_url"
         >
         </image-upload-field>

         <image-upload-field
            name="company_logo"
            label="Pridėkite savo logotipo nuotrauką"
            sub_text="Maks. nuotraukos dydis 500x500px"
            :file.sync="form.company_logo"
            :preset="company.avatar_url"
         >
         </image-upload-field>

         <multiple-file-upload-field
            name="certificates"
            label="Pridėkite sertifikatų, padėkų nuotraukas"
            :file.sync="form.certificates"
            :preset="company.certificates_list"
         >
         </multiple-file-upload-field>

         <MultiStepTypeCity
            v-model="form.paths"
            :property_types="property_types"
            :services="services"
            :regions="regions"
            :path_errors="path_errors"
            :preset="company.paths"
         ></MultiStepTypeCity>

         <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="language"
         >
            <div class="checkbox-group">
               <div class="checkbox-group__element">
                  <div :class="['checkbox-group__label', errors[0] ?  'checkbox-group__label--error' : '']">
                     Kokiomis kalbomis kalbate
                  </div>
                  <div class="checkbox-group__sub-label">Pasirinkite ir uždėkite varnelę</div>
                  <template v-for="(language, index) in languages">
                     <v-checkbox
                        v-model="form.language"
                        :label="language.name"
                        :value="language.id"
                        multiple
                        hide-details
                        :error-messages="errors"
                        on-icon=""
                        off-icon=""
                        class="v-input--custom-checkbox"
                        :ripple="false"
                     ></v-checkbox>
                  </template>
               </div>
               <div class="checkbox-group__errors" v-if="errors[0]">
                  {{ errors[0] }}
               </div>
            </div>
         </ValidationProvider>

         <ValidationProvider
            rules="required"
            v-slot="{ errors }"
            name="description"
         >
            <v-text-field
               v-model="form.description"
               label="jūsų aprašymas"
               placeholder="Įrašykite.."
               class="v-input--default v-input--labeled"
               color="custom"
               :error-messages="errors"
               persistent-hint
               hint="Nuo to labai priklauso užsakymų kokybė, išsamiai pristatykite, detalizuokite savo patirtį, teikiamas paslaugas"
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
               autocomplete="new-password"
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
            v-slot="{ errors }"
            name="website"
         >
            <v-text-field
               v-model="form.website"
               label="Jūsų tinklapis (neprivaloma)"
               placeholder="www.puslapis.lt"
               class="v-input--default v-input--labeled"
               color="custom"
               :error-messages="errors"
               autocomplete="new-password"
            ></v-text-field>
         </ValidationProvider>

         <ValidationProvider
            rules="isUrl"
            v-slot="{ errors }"
            name="represent_video"
         >
            <v-text-field
               v-model="form.represent_video"
               label="Pristatomasis video"
               placeholder="YouTube nuoroda"
               class="v-input--default v-input--labeled"
               color="custom"
               :error-messages="errors"
               autocomplete="new-password"
            ></v-text-field>
         </ValidationProvider>

         <ValidationProvider
            vid="password"
            rules="min:8"
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
            v-slot="{ errors }"
            rules="confirmed:password"
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
         <v-btn
            color="primary"
            width="100%"
            @click="validate"
            class="v-btn--submit"
            :loading="loading"
            :disabled="loading"
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
   import MultiStepTypeCity from "./form/MultiStepTypeCity";
   import {mask} from "vue-the-mask";
   import ImageUploadField from "./form/ImageUploadField";
   import MultipleFileUploadField from "./form/MultipleFileUploadField";
   import {Toaster} from "../helpers/Toaster";

   export default {
      extends: FormEssentials,
      directives: {
         mask,
      },
      components: {
         MultipleFileUploadField,
         ValidationProvider,
         ValidationObserver,
         ImageUploadField,
         MultiStepTypeCity
      },
      data() {
         return {
            form: {
               // name: 'Albinas Venclovas',
               // agency: 'BesCompany',
               // language: [2, 3],
               // paths: [{"property_type": 4, "properties": [31], "cities": [2, 5], "price":"nuo 700"}], //
               // description: 'Okokokok best description',
               // phone: '+370 545 65 465',
               // email: 'guoga@gmail.com',
               // website: 'http://google.lt',
               // represent_video: 'https://www.youtube.com/watch?v=LAbWDYsCokc',
               // agreement: true,
               // password: 'sd123456',
               // password_confirmation: 'sd123456',
               // profile_image: null,
               // company_logo: null,
               name: null,
               agency: null,
               language: null,
               paths: [],
               description: null,
               phone: null,
               email: null,
               website: null,
               represent_video: null,
               agreement: null,
               password: null,
               password_confirmation: null,
               avatar: null,
               company_logo: null,
               certificates: null
            },
            path_errors: [],
            clearFormData: false,
         }
      },
      props: {
         user: {
            type: Object,
         },
         company: {
            type: Object
         },
         languages: {
            type: Array,
         },
         property_types: {
            type: Array,
         },
         services: {
            type: Array,
         },
         regions: {
            type: Array,
         },
         postRoute: {
            type: String
         },
         becamebroker: {
             type: Number
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
            this.path_errors = [];
            this.hideGlobalMessage();
         },

         updatePaths(paths) {
            this.form.paths = paths;
         },

         submit() {
             let self = this;
             if(this.becamebroker){
                 this.form['becamebroker'] = 1;
             }
            axios.post(this.postRoute, this.getPreparedData(this.form))
               .then(response => {

                  this.clearForm()
                  Toaster('success', response.data);
                  if(self.becamebroker) {
                      window.location.href="/profilis";
                  }

               }).catch(error => {

               var path_errors = this.path_errors;

               if (error.response.status === 422) {
                  this.$refs.validator.setErrors(error.response.data.errors);

                  for (var property in error.response.data.errors) {
                     this.form.paths.forEach(function (value, key) {
                        var error_key = 'paths.' + key;

                        if (property.includes(error_key)) {
                           path_errors.push(key);
                        }
                     });
                  }
               }

               if (error.response.data.message) {
                  Toaster('success', error.response.data)
               }

               this.loading = false;
            });
         }
      },
      created() {
         this.form.name = this.user.name
         this.form.agency = this.company.name
         this.form.language = this.company.languages_list
         this.form.description = this.company.description
         this.form.phone = this.user.phone
         this.form.email = this.user.email
         this.form.website = this.company.website
         this.form.represent_video = this.company.represent_video
         this.form.paths = this.company.paths_form
      }
   }
</script>
