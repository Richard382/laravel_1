<script>
   import Nl2br from 'vue-nl2br';
   import objectToFormData from 'object-to-formdata';
   import Redirect from "../../utils/Redirect";
   import Cloner from "../../helpers/Cloner";

   export default {
      name: "ModalPopUp",
      components: {
         Nl2br
      },
      data() {
         return {
            dialog: false,
            global_message_type: null,
            global_message: null,
            cleanFormData: null,
            loading: false,
            clearFormData: true,
         }
      },
      created() {
         this.cleanFormData = Cloner(this.form);
      },
      methods: {
         clearForm() {
            if (this.clearFormData) {
               this.form = Object.assign({}, this.cleanFormData);
            }

            this.loading = false
            this.$refs.validator.reset();
         },

         redirectTo(url, object = false) {
            Redirect(url, object)
         },

         getPreparedData(data) {
            return objectToFormData(data, {
               indices: true,
               nullsAsUndefineds: true
            });
         },

         showGlobalMessage(message, type = 'error') {
            this.global_message_type = type;
            this.global_message = message;
         },

         hideGlobalMessage() {
            this.global_message = null;
         }
      },
   }
</script>

<style scoped>

</style>
