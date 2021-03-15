<template>
   <ValidationProvider
      ref="imageUpload"
      rules="image|maxFiles:6"
      v-slot="{ errors, validate }"
      :name="name"
   >
      <div class="multiple-file-upload">
         <div class="multiple-file-upload__label" v-if="label">{{ label }}:</div>
         <div class="multiple-file-upload__sub" v-if="sub_text">{{ sub_text }}</div>
         <div class="multiple-file-upload__error">{{ errors[0] }}</div>

         <input
            type="file"
            ref="file"
            accept="image/png, image/jpeg"
            @change="setImage($event)"
            style="display: none;"
            multiple
            :max="max_files"
         />

         <div class="multiple-file-upload__files">
            <v-img
               :src="img"
               width="70"
               height="70"
               position="center center"
               @click="$refs.file.click()"
                style="cursor: pointer;"
            />

            <v-img
               v-for="(file, index) in internalFiles"
               :src="file.img ? file.img : img"
               :key="index"
               width="70"
               height="70"
               position="center center"
            />
         </div>
      </div>
   </ValidationProvider>
</template>

<script>
   import {ValidationProvider} from 'vee-validate';

   export default {
      name: "MultipleFileUploadField",
      components: {
         ValidationProvider
      },
      props: {
         name: {
            type: String,
         },
         label: {
            type: String,
         },
         sub_text: {
            type: String
         },
         max_files: {
            type: Number,
            default() {
               return 6
            }
         },
         preset: {
            type: [Boolean, Array],
            default() {
               return false;
            }
         }
      },
      data() {
         return {
            img: '/images/image-placeholder.png',
            placeholder: '/images/image-placeholder.png',
            progress: false,
            internalFiles: [],
         }
      },
      computed: {
         files: {
            get() {
               return this.files
            },
            set(value) {
               this.$emit('update:file', value)
            }
         }
      },
      methods: {
         async setImage(event) {
            this.internalFiles = [];
            this.files = false;

            let image = await this.$refs.imageUpload.validate(event);
            const URL = window.URL || window.webkitURL;

            if (image.valid) {
               Array.from(event.target.files).forEach(file => {
                  this.internalFiles.push({
                     img: URL.createObjectURL(file)
                  })
               });

               this.files = event.target.files
            }
         },

         clear() {
            this.internalFiles = []
         }
      },
      created() {
         if (this.preset) {
            this.preset.forEach(image => {
               this.internalFiles.push({
                  img: image
               })
            })
         }
      }
   }
</script>
