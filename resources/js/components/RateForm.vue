<template>
    <ValidationObserver v-slot="{ valid }" ref="validator">
        <v-form
            class="form"
        >
            <div class="checkbox-group">
                <div class="checkbox-group__element">
                    <div class="rating-group">
                        <div class="label-item">Greitis:</div>
                        <ValidationProvider
                            rules="required"
                            v-slot="{ errors }"
                            name="speed"
                        >
                            <v-rating
                                v-model="form.speed"
                                half-increments
                                dense
                                size="22"
                                color="primary"
                                background-color="primary"
                            >
                            </v-rating>
                            <input type="hidden" v-model="form.speed" name="speed">
                            <div class="v-messages theme--light error--text" role="alert" v-if="errors[0]">
                                <div class="v-messages__wrapper">
                                    <div class="v-messages__message" style="">{{ errors[0] }}</div>
                                </div>
                            </div>
                        </ValidationProvider>
                    </div>

                    <div class="rating-group">
                        <div class="label-item">Kaina:</div>
                        <ValidationProvider
                            rules="required"
                            v-slot="{ errors }"
                            name="price"
                        >
                            <v-rating
                                v-model="form.price"
                                half-increments
                                dense
                                size="22"
                                color="primary"
                                background-color="primary"
                            >
                            </v-rating>
                            <input type="hidden" v-model="form.price" name="price">
                            <div class="v-messages theme--light error--text" role="alert" v-if="errors[0]">
                                <div class="v-messages__wrapper">
                                    <div class="v-messages__message" style="">{{ errors[0] }}</div>
                                </div>
                            </div>
                        </ValidationProvider>
                    </div>

                    <div class="rating-group">
                        <div class="label-item">Kokybė:</div>
                        <ValidationProvider
                            rules="required"
                            v-slot="{ errors }"
                            name="quality"
                        >
                            <v-rating
                                v-model="form.quality"
                                half-increments
                                dense
                                size="22"
                                color="primary"
                                background-color="primary"
                            >
                            </v-rating>
                            <input type="hidden" v-model="form.quality" name="quality">
                            <div class="v-messages theme--light error--text" role="alert" v-if="errors[0]">
                                <div class="v-messages__wrapper">
                                    <div class="v-messages__message" style="">{{ errors[0] }}</div>
                                </div>
                            </div>
                        </ValidationProvider>
                    </div>

                    <div class="rating-group">
                        <div class="label-item">Komunikacija:</div>
                        <ValidationProvider
                            rules="required"
                            v-slot="{ errors }"
                            name="communication"
                        >
                            <v-rating
                                v-model="form.communication"
                                half-increments
                                dense
                                size="22"
                                color="primary"
                                background-color="primary"
                            >
                            </v-rating>
                            <input type="hidden" v-model="form.communication" name="communication">
                            <div class="v-messages theme--light error--text" role="alert" v-if="errors[0]">
                                <div class="v-messages__wrapper">
                                    <div class="v-messages__message" style="">{{ errors[0] }}</div>
                                </div>
                            </div>
                        </ValidationProvider>
                    </div>
                </div>
            </div>

            <ValidationProvider
                rules="required|max:300"
                v-slot="{ errors }"
                name="requirements"
            >
                <v-textarea
                    v-model="form.comment"
                    label="palikite komentarą"
                    placeholder="Įrašykite…"
                    class="v-input--default v-input--labeled"
                    color="custom"
                    :error-messages="errors"
                    persistent-hint
                ></v-textarea>
            </ValidationProvider>

            <v-btn
                color="primary"
                width="100%"
                @click="validate"
                class="v-btn--submit"
                :loading="loading"
                :disabled="loading"
            >
                Pateikti įvertinimą
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

  export default {
    name: "RateForm",
    extends: FormEssentials,
    components: {
      ValidationProvider,
      ValidationObserver
    },
    data() {
      return {
        form: {
          speed: null,
          price: null,
          quality: null,
          communication: null,
          comment: null,
        },
        loading: false,
      }
    },
    props: {
      route: {
        type: String
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

      submit() {
        axios.post(this.route, this.getPreparedData(this.form))
        .then(response => {

          this.showGlobalMessage(response.data.message, 'success');
          this.redirectTo(response.data.redirect);

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
    }
  }
</script>
