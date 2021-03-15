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

                <template v-if="ready">
                    <v-alert
                        outlined
                        :type="global_message_type"
                    >
                        {{ global_message }}
                    </v-alert>
                </template>
                <div class="v-dialog__text" v-if="helper_text">
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
  import Nl2br from 'vue-nl2br';

  export default {
    name: "AcceptOffer",
    components: {
      Nl2br,
    },
    data() {
      return {
        loading: true,
        ready: false,
        helper_text: false,
        offer: null,
        dialog: false,
        global_message_type: null,
        global_message: null
      }
    },
    props: {
      inquiryid: {
        type: String,
      }
    },
    methods: {
      showGlobalMessage(message, type = 'error') {
        this.global_message_type = type;
        this.global_message = message;
      },
    },
    mounted() {
      const self = this;

      this.$root.$on('openAcceptOfferModal', (offer) => {
        this.loading = true;
        this.dialog = true;

        axios.post('/offers/' + offer.id + '/' + self.inquiryid + '/accept')
        .then(response => {

          this.showGlobalMessage(response.data.message, 'success')

          this.loading = false;
          this.ready = true;
          this.helper_text = true;

          setTimeout(function(){
            location.reload();
          }, 2000);

        }).catch(error => {
          this.loading = false;
          this.ready = true;

          if (error.response.data.message) {
            this.showGlobalMessage(error.response.data.message);
          }

          setTimeout(function(){
            location.reload();
          }, 2000);
        });
      })
    }
  }
</script>
