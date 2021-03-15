<template>
   <v-text-field
      ref="field"
      v-model="internalValue"
      type="string"
      :label="label"
      :placeholder="placeholder"
      :class="customClass ? customClass : 'v-input--default v-input--labeled'"
      color="custom"
      :error-messages="errors"
      :solo="solo"
      @input="formatValue"
   ></v-text-field>
</template>

<script>
   import Format from "../../helpers/Format";

   export default {
      name: "NumberField",
      data() {
        return {
           internalValue: this.value
        }
      },
      props: {
         value: {
            required: true,
         },
         label: {
            default() {
               return ""
            }
         },
         placeholder: {
            required: true,
         },
         errors: {
            required: true,
         },
         customClass: {
            default() {
               return ""
            }
         },
         solo: {
            default() {
               return false
            }
         }
      },
      created() {
         this.formatValue(this.internalValue)
      },
      methods: {
         formatValue(value) {
            if (! value) {
               return;
            }

            let tempVal

            if (typeof value === "string" && value.includes(' ')) {
               tempVal = value.split(' ').join('')
            } else {
               tempVal = value
            }

            if (isNaN(tempVal)) {

               this.internalValue = ""
               this.$refs.field.reset()
               this.$emit('input', "")

               return
            }

            // if (tempVal !== parseInt(tempVal, 10)) {
            //    this.internalValue = 0;
            //    return
            // }

            this.internalValue = Format(tempVal).Number()

            this.$emit('input', parseInt(tempVal))
         }
      }
   }
</script>

<style scoped>

</style>
