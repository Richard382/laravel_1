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

            <v-btn
                color="primary"
                width="100%"
                @click="validate"
                class="v-btn--submit"
                :loading="loading"
                :disabled="loading"
            >
                Siųsti
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
  import {ValidationProvider, ValidationObserver} from "vee-validate";
  import objectToFormData from 'object-to-formdata';
  import Redirect from "../utils/Redirect";

  export default {
    components: {
      ValidationProvider,
      ValidationObserver,
    },
    data() {
      return {
        form: {
          email: null,
        },
        loading: false,
        global_message: null,
        global_message_type: 'error',
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

        window.axios.post('/auth/slaptazodzio-priminimas', data)
        .then(response => {
          this.loading = false;
          this.showGlobalMessage(response.data.message, 'success');
          this.form.email = '';
          this.$refs.login.reset();

        }).catch(error => {
          if (error.response.status == 422) {
            this.$refs.login.setErrors(error.response.data.errors);
          }

          if (error.response.data.message) {
            this.showGlobalMessage(error.response.data.message);
          }

          this.loading = false;
        });
      }
    }
  }
</script>

<style scoped>

</style>
