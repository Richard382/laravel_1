<template>
    <v-dialog
        v-model="dialog"
        width="600px"
    >
        <v-card>
            <v-btn
                icon
                class="v-btn--close-dialog"
                @click="dialog = false"
            >
                <v-icon>mdi-close</v-icon>
            </v-btn>
            <v-card-title>
            </v-card-title>
            <v-card-text>
                <div class="text-center">
                    <v-progress-circular
                        :size="50"
                        color="primary"
                        indeterminate
                        v-if="loading"
                    ></v-progress-circular>
                </div>

                <template v-if="ready && !loading">
                    <v-alert
                        outlined
                        :type="global_message_type"
                    >
                        {{ global_message }}
                    </v-alert>
                </template>
                <div class="v-dialog__text" v-if="helper_text && !loading">
                    <p>Paslaugos teikėjas turi 30min. susiekti su
                        jumis.</p>

                    <p>Taip neįvykus paslaugos teikėjas bus įvertintas
                        viena žvaigždute.</p>
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
  import FormEssentials from "./base/FormEssentials";

  export default {
    extends: FormEssentials,
    name: "MakeConnection",
    data() {
      return {
        loading: true,
        ready: false,
        helper_text: false,
        offer: null,
      }
    },
    props: {
      inquiry: {
        type: Number,
      }
    },
    methods: {
    },
    mounted() {
      let self = this;

      this.$root.$on('openMakeConnectionForm', (company) => {
        this.loading = true;
        this.dialog = true;

        axios.post('/nt-paslaugu-teikejai/' + company.id + '/' + this.inquiry + '/susisiekti')
        .then(response => {
          this.showGlobalMessage('Jūsų kontaktai išsiųsti paslaugos teikėjui', 'success')

          this.loading = false;
          this.ready = true;
          this.helper_text = true;

          setTimeout(function() {
            self.redirectTo(response.data.redirect);
          }, 5000);

        }).catch(error => {
          this.loading = false;
          this.ready = true;

          if (error.response.data.message) {
            this.showGlobalMessage(error.response.data.message);
          }
        });
      })
    }
  }
</script>
