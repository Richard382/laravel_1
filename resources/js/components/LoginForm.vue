<template>
    <ValidationObserver v-slot="{ valid }" ref="login">
        <v-form
            class="form"
        >
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
                rules="required"
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
                rules="required"
                v-slot="{ errors }"
                name="remember"
            >
                <v-checkbox
                    v-model="form.remember"
                    on-icon=""
                    off-icon=""
                    class="v-input--condition v-input--custom-checkbox"
                    :ripple="false"
                    :error-messages="errors"
                >
                    <template v-slot:label>
                        <div>Prisiminti mane</div>
                    </template>
                </v-checkbox>
            </ValidationProvider>

            <div class="v-btn--submit d-flex align-center justify-center">
                <v-btn
                    color="primary"
                    width="50%"
                    @click="validate"
                    class="mt-0"
                    :loading="loading"
                    :disabled="loading"
                >
                    Prisijungti
                </v-btn>
                <div style="width: 50%" class="text-center">
                    <a :href="resetPasswordLink" class="low">Pamiršote slaptažodį?</a>
                </div>
            </div>

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
  import {ValidationProvider, ValidationObserver} from "vee-validate";
  import objectToFormData from 'object-to-formdata';
  import Redirect from "../utils/Redirect";
  import {Toaster} from "../helpers/Toaster";

  export default {
    components: {
      ValidationProvider,
      ValidationObserver,
    },
    data() {
      return {
        form: {
          // email: 'guoga@gmail.com',
          // password: 'sd123456',
          // remember: false,
          email: localStorage.getItem("log-eml"),
          password: localStorage.getItem("log-ps"),
          remember: false,
        },
        loading: false,
        global_message: null,
        global_message_type: 'error',
      }
    },
    props: {
      resetPasswordLink: {
        type: String,
        required: true,
      },
      postRoute: {
         type: String
      }
    },
    
    methods: {
      async validate() {
        let form = await this.$refs.login.validate();

        this.resetFormStates();

        if (form) {
          this.loading = true;
          this.submit();
        }
      },

      resetFormStates() {
        this.hideGlobalMessage();
      },

      showGlobalMessage(message, type = 'error') {
        this.global_message_type = type;
        this.global_message = message;
      },

      hideGlobalMessage() {
        this.global_message = null;
      },

      submit() {
        const data = objectToFormData(this.form, {
          indices: true,
          nullsAsUndefineds: true
        });

        window.axios.post(this.postRoute, data)
        .then(response => {
            this.loading = false;
            Toaster('success', response.data)
            localStorage.setItem("log-eml", this.form.email);
            localStorage.setItem("log-ps", this.form.password);
           setTimeout(function() {
              Redirect(response.data.redirect);
           }, 1000)

        }).catch(error => {
          if (error.response.status == 422) {
            this.$refs.login.setErrors(error.response.data.errors);
          }

          if (error.response.data.message) {
             Toaster('error', error.response.data)
          }

          this.loading = false;
        });
      },
      mounted(){
            console.log('mounted');
        },
    }
  }
</script>

<style scoped>

</style>
