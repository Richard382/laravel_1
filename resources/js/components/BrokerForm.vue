<template>
   <ValidationObserver v-slot="{ valid }" ref="validator">
      <v-form
         class="form ml-5 mr-5"
         autocomplete="off"
      >
<input autocomplete="off" name="hidden" type="text" style="display:none;">
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
            ref="avatar"
            name="avatar"
            label="Pridėkite savo profilio nuotrauką"
            sub_text=""
            :file.sync="form.avatar"
         >
         </image-upload-field>

         <image-upload-field
            ref="company_logo"
            name="company_logo"
            label="Pridėkite savo logotipo nuotrauką"
            sub_text=""
            :file.sync="form.company_logo"
         >
         </image-upload-field>

         <multiple-file-upload-field
            ref="certificates"
            name="certificates"
            label="Pridėkite sertifikatų, padėkų nuotraukas"
            :file.sync="form.certificates"
         >
         </multiple-file-upload-field>

         <MultiStepTypeCity
            ref="multiselect"
            v-model="form.paths"
            :property_types="property_types"
            :services="services"
            :regions="regions"
            :path_errors="path_errors"
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
            <v-textarea
               v-model="form.description"
               label="jūsų aprašymas"
               placeholder="Įrašykite.."
               class="v-input--default v-input--labeled"
               color="custom"
               :error-messages="errors"
               persistent-hint
               hint="Nuo to labai priklauso užsakymų kokybė, išsamiai pristatykite, detalizuokite savo patirtį, teikiamas paslaugas"
            ></v-textarea>
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
                autocomplete="off"
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
            v-slot="{ errors }"
            rules="required|confirmed:password"
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
               label="Pristatomasis video (neprivaloma)"
               placeholder="YouTube nuoroda"
               class="v-input--default v-input--labeled"
               color="custom"
               :error-messages="errors"
               autocomplete="new-password"
            ></v-text-field>
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
               class="v-input--condition v-input--custom-checkbox"
               :ripple="false"
               :error-messages="errors"
            >
               <template v-slot:label>
                  <div>
                     Sutinku su <a
                     target="_blank"
                     @click.stop
                     :href="agreementLink"
                  >sąlygomis ir taisyklėmis</a>
                  </div>
               </template>
            </v-checkbox>
         </ValidationProvider>
         <v-btn
            color="primary"
            width="100%"
            @click="validate"
            class="v-btn--submit"
            :loading="loading"
            :disabled="loading"
         >
            REGISTRUOTIS
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
            submitted:false
         }
      },
      props: {
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
         agreementLink: {
            type: [String, Boolean],
            default() {
               return false;
            }
         },
         postRoute: {
            type: String,
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
         resetFormStates() {
            this.path_errors = [];
            this.hideGlobalMessage();
         },

         updatePaths(paths) {
            this.form.paths = paths;
         },

         clearForm() {
            if (this.clearFormData) {
               this.form = Object.assign({}, this.cleanFormData);
               this.$refs.multiselect.clearPaths()
               this.$refs.avatar.clear()
               this.$refs.company_logo.clear()
               this.$refs.certificates.clear()
            }

            this.loading = false
            this.$refs.validator.reset();
         },

         submit() {
            axios.post(this.postRoute, this.getPreparedData(this.form))
               .then(response => {

                  this.clearForm();
                  Toaster('success', response.data)
                  setTimeout(function(){
                      window.location.href = '/';
                  },3000)

               }).catch(error => {

               var self = this;

               if (error.response.status === 422) {
                  this.$refs.validator.setErrors(error.response.data.errors);

                  for (var property in error.response.data.errors) {
                     this.form.paths.forEach(function (value, key) {
                        var error_key = 'paths.' + key;
                        if (property.includes(error_key)) {
                           self.path_errors.push(key);
                        }
                     });
                  }
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
