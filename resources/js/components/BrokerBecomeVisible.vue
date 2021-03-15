<template>
    <ValidationObserver v-slot="{ valid }" ref="registration">
        <v-form
            class="form"
        >
            <ValidationProvider
                rules="required"
                v-slot="{ errors }"
                name="service"
            >
                <v-select
                    v-model="form.place"
                    :items="places"
                    item-text="name"
                    item-value="id"
                    label="Pasirinkite vietą lentelėje"
                    class="v-input--default v-input--labeled"
                    :error-messages="errors"
                    :menu-props="{bottom: true, offsetY: true, maxHeight: 240}"
                    return-object
                    @change="triggerPlaceChanged"
                >
                    <template v-slot:item="data">
                        <div class="d-flex align-center w-full px-1 py-2" style="width: 100%;">
                            <div class="flex font-weight-medium w-full" style="width: 100%">
                                {{ data.item.name }}
                            </div>
                            <div class="flex" style="white-space: nowrap">
                                <template v-if="data.item.disabled">
                                    <span class="grey--text font-weight-medium">užimta iki {{ data.item.taken_until }}</span>
                                </template>
                                <template v-else>
                                    <span class="primary--text font-weight-medium">nuo {{ data.item.price_per_month }}</span>
                                </template>
                            </div>
                        </div>
                    </template>
                </v-select>
            </ValidationProvider>

            <ValidationProvider
                rules="required"
                v-slot="{ errors }"
                name="service"
            >
                <v-select
                    v-model="form.duration"
                    :items="internalDurations"
                    item-text="name"
                    item-value="id"
                    label="Pasirinkite laikotarpį"
                    class="v-input--default v-input--labeled"
                    :error-messages="errors"
                    :menu-props="{bottom: true, offsetY: true, maxHeight: 240}"
                    :disabled="! hasPlace"
                    return-object
                >
                    <template v-slot:item="data">
                        <div class="d-flex align-center w-full px-1 py-2" style="width: 100%;">
                            <div class="flex font-weight-medium w-full" style="width: 100%">
                                {{ data.item.name }}
                            </div>
                            <div class="flex" style="white-space: nowrap">
                                <span class="primary--text font-weight-medium" v-html="data.item.price"></span>
                            </div>
                        </div>
                    </template>
                </v-select>
            </ValidationProvider>

            <div v-if="form.place && form.duration" class="mt-5" style="color: #363a7b">
                <div class="d-block font-weight-bold">Pasirinkta vieta lentelėje</div>
                <div class="caption">{{ form.place.name }}</div>
                <div class="d-block font-weight-bold mt-2">Pasirinktas laikotarpis</div>
                <div class="caption">{{ form.duration.name }}</div>
                <div class="d-block font-weight-bold mt-2">Pasirinkta vieta lentelėje</div>
                <div class="headline" v-html="form.duration.price"></div>
            </div>

            <v-btn
                color="primary"
                width="100%"
                @click="validate"
                class="v-btn--submit"
                :loading="loading"
                :disabled="!valid || loading"
            >
                Apmokėti
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
    extends: FormEssentials,
    components: {
      ValidationProvider,
      ValidationObserver,
    },
    data() {
      return {
        form: {
          place: null,
          duration: null,
        },
        hasPlace: false,
        loading: false,
        path_errors: [],
        internalDurations: [],
      }
    },
    props: {
        places: {
            type: Array,
        },
        initialDurations: {
            type: Array
        },
        post_link: {
            type: String
        },
        payment_link: {
            type: String
        }
    },
    methods: {
      async validate() {
        let form = await this.$refs.registration.validate();

        this.resetFormStates();

        if (form) {
          this.loading = true;
          this.submit();
        }
      },

      triggerPlaceChanged() {
        this.hasPlace = true;

        this.internalDurations.forEach(duration => {
          duration.price = (duration.months * this.form.place.price_per_month) + ' <span class="text-uppercase">' + window.auth.currency + '</span>'
        })
      },

      resetFormStates() {
        this.path_errors = [];
        this.hideGlobalMessage();
      },


      submit() {
        this.redirectTo(this.payment_link + '/' + this.form.place.id + '?months=' + this.form.duration.id)
        // axios.post(this.post_link, this.getPreparedData(this.form))
        //   .then(response => {
        //
        //     this.loading = false;
        //     this.showGlobalMessage(response.data.message, 'success');
        //     this.redirectTo(response.data.redirect);
        //
        //   }).catch(error => {
        //
        //   var path_errors = this.path_errors;
        //
        //   if (error.response.status === 422) {
        //     this.$refs.registration.setErrors(error.response.data.errors);
        //
        //     for (var property in error.response.data.errors) {
        //       this.form.paths.forEach(function (value, key) {
        //         var error_key = 'paths.' + key;
        //
        //         if (property.includes(error_key)) {
        //           path_errors.push(key);
        //         }
        //       });
        //     }
        //   }
        //
        //   if (error.response.data.message) {
        //     this.showGlobalMessage(error.response.data.message);
        //   }
        //
        //   this.loading = false;
        // });
      }
    },
    created() {
      this.internalDurations = this.initialDurations;
    }
  }
</script>
