<template>
   <div>
      <ValidationProvider
         ref="imageUpload"
         rules="image|maxDimensions:2000,2000"
         v-slot="{ errors, validate }"
         :name="name"
      >
         <div class="file-upload d-flex align-center">
            <div class="file-upload__image" style="cursor: pointer;">
               <v-img
                  :src="croppedImg"
                  width="70"
                  height="70"
                  position="center center"
                  @click="$refs.file.click()"
               >
               </v-img>
               <input type="file" ref="file" accept="image/png, image/jpeg" @change="setImage($event)"
                      style="display: none;">
            </div>
            <div class="file-upload__info">
               <div class="file-upload__label" v-if="label">{{ label }}</div>
               <div class="file-upload__sub" v-if="sub_text">{{ sub_text }}</div>
               <div class="file-upload__error">{{ errors[0] }}</div>
            </div>
         </div>
      </ValidationProvider>
      <v-dialog
         v-model="cropImage"
         width="500"
      >
         <v-card>
            <v-card-title
               class="headline primary white--text pt-3"
               primary-title
            >
               Redaguoti nuotrauką
            </v-card-title>

            <v-card-text class="pt-5">
               <clipper-fixed
                  ref="cropper"
                 :src="img"
               />
            </v-card-text>

            <v-divider></v-divider>

            <v-card-actions>
               <v-spacer></v-spacer>
               <v-btn
                  color="primary"
                  text
                  @click="crop"
               >
                  Išsaugoti
               </v-btn>
            </v-card-actions>
         </v-card>
      </v-dialog>
   </div>
</template>

<script>
   import {ValidationProvider} from 'vee-validate';
   import {clipperFixed, clipperUpload} from 'vuejs-clipper';

   export default {
      name: "ImageUploadField",
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
         preset: {
            type: [Boolean, String],
            default() {
               return false;
            }
         }
      },
      data() {
         return {
            cropImage: false,
            croppedImg: '/images/image-placeholder.png',
            img: '/images/image-placeholder.png',
            placeholder: '/images/image-placeholder.png',
            progress: false
         }
      },
      computed: {
         file: {
            get() {
               return this.file
            },
            set(value) {
               this.$emit('update:file', value)
            }
         }
      },
      methods: {
         async setImage(event) {
            let image = await this.$refs.imageUpload.validate(event);

            if (image.valid) {
               const file = event.target.files[0];
               this.file = file;
               const URL = window.URL || window.webkitURL;
               this.img = URL.createObjectURL(file);

               this.cropImage = true;
            }
         },

         clear() {
            this.croppedImg = '/images/image-placeholder.png'
            this.img = '/images/image-placeholder.png'
            this.placeholder = '/images/image-placeholder.png'
         },

         crop() {
            this.croppedImg = this.$refs.cropper.clip({wPixel: 100, maxWPixel: 100}).toDataURL()
            this.file = this.croppedImg
            this.cropImage = false
         }
      },
      created() {
         if (this.preset) {
            this.croppedImg = this.preset
         }
      }
   }
</script>
