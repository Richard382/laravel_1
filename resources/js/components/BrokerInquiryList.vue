<template>
<v-container fluid id="contentlist_container">
      <v-row dense
          class="ml-6 mr-6"
      >
        <v-col
          cols="12"
          class="pa-2"
        >
          <v-card
            class="mx-auto"
            max-width="1200"
          >

   <div class="inquiries__list loader-manage">
       <div class="inquiry-search-loader" v-if="searchloader">
           <i class="mdi mdi-spin mdi-refresh mdi-36px"></i>
       </div>
      <div class="inquiries__list-filter" v-if=" !nofilter">
         <v-form class="form">

            <v-navigation-drawer
               v-model="item_filter"
               app
               overlay-color="transparent"
               temporary
               width="230"
               class="inquiries__list-drawer"
            >
               <div class="inquiries__list-drawer-title">
                  Patikslinkit paieška
               </div>
               <div class="inquiries__list-drawer-filters">
                  <v-select
                     v-model="form.region"
                     class="v-select--single v-input--labeled v-input--no-label"
                     no-data-text="Nėra duomenų"
                     placeholder="Miestas"
                     :items="regions"
                     item-text="name"
                     item-value="id"
                     clearable
                     :menu-props="{bottom: true, offsetY: true, maxHeight: 240}"
                  ></v-select>
                  <v-select
                     v-model="form.type"
                     key="form.type"
                     class="v-select--single v-input--labeled v-input--no-label"
                     no-data-text="Nėra duomenų"
                     placeholder="Tipas"
                     :items="types"
                     item-text="name"
                     item-value="id"
                     clearable
                     :menu-props="{bottom: true, offsetY: true, maxHeight: 240}"
                  ></v-select>
                  <v-select
                     v-model="form.service_type"
                     key="form.service_type"
                     class="v-select--single v-input--labeled v-input--no-label"
                     no-data-text="Nėra duomenų"
                     placeholder="Paslaugos Tipas"
                     :items="service_types"
                     item-text="name"
                     item-value="id"
                     clearable
                     :menu-props="{bottom: true, offsetY: true, maxHeight: 240}"
                  ></v-select>
               </div>
            </v-navigation-drawer>

            <v-text-field
               v-model="form.search"
               name="search"
               placeholder="Paklausimo paieška…"
               append-icon="mdi-magnify"
               autocomplete="off"
               class="v-input--default v-input--labeled v-input--no-label v-input--with-submit-inside"
               type="text"
               color="custom"
               @click:append="submit"
               v-on:keyup.enter="submit"
            >
               <template v-slot:append-outer>
                  <v-btn
                     color="primary"
                     width="42"
                     @click="item_filter = true"
                  >
                     <v-icon>mdi-filter</v-icon>
                  </v-btn>
               </template>
            </v-text-field>
         </v-form>
      </div>

      <template v-for="(item, index) in items">
         <div
            class="inquiries__list-label"
            v-if="item.offer_accepted && index === 0"
         >Jus pasirinko:
         </div>
         <div v-if="checkIfPrevAcceptedAndNowNot(index)" class="inquiries__list__meanings">
            <!-- <div class="d-flex">
               <div class="flex searching">Ieško</div>
               <div class="flex suggest">Siūlo</div>
               <div class="flex other">Kita</div>
            </div> -->
         </div>
         <inquiry
            :item="item"
            v-on:doOffer="handleClick"
            :current-company-route="currentCompanyRoute"
            :expand="expand"
         ></inquiry>
      </template>

      <v-alert
         outlined
         class="v-alert--nt-info"
         v-if=" ! items.length"
      >
         Paklausimų nėra
      </v-alert>

      <div class="inquiries__list-pagination" v-if="total_visible && items.length > 0">
         <v-pagination
            v-model="page"
            :length="pages_number"
            :total-visible="pages_number"
            @input="nextPage"
            circle
         ></v-pagination>
      </div>
      <offer-form/>
      <v-dialog  v-model="dialogarchive" width="600px">
            <v-card>
                <v-btn
                    icon
                    class="v-btn--close-dialog"
                    @click="dialogarchive = false"
                >
                    <v-icon>mdi-close</v-icon>
                </v-btn>
               
                <v-card-text>

                    <div class="v-dialog__text">
                        <p style="font-size: 18px;padding-top: 20px;">Ar Jūsų užklausa buvo sėkmingai įvykdyta?</p>
                    </div>

                    <br>
                <div class="text-center">
                        <v-btn
                            color="primary"
                            class="inquiry_confirm_button"
                            @click="archiveModalTrue()"
                        >
                        TAIP
                        </v-btn>
                        <v-btn
                            color="red"
                            class="inquiry_confirm_button"
                            @click="archiveModalfalse()"
                        >
                        NE
                        </v-btn>
                    </div>
                    </v-card-text>
            </v-card>
        </v-dialog>
      <v-dialog  v-model="archivecomplain" width="600px">
            <v-card>
                <v-btn
                    icon
                    class="v-btn--close-dialog"
                    @click="archivecomplain = false"
                >
                    <v-icon>mdi-close</v-icon>
                </v-btn>
               
                <v-card-text>
                    <br>
                    <div class="v-dialog__text">
                        <p style="font-size: 18px;padding-top: 20px;">NT specialistui skirtas įspėjimas. Norėdami plačiau papasakoti, kodėl likote nepatenkinti suteikta paslauga,parašykite žinutę mums:</p>
                    </div>
                    <div class="v-dialog__text form">
                        <div class=" v-input--labeled">
                            <textarea placeholder="Įrašykite..." class="v-input--default v-text-field__slot"
                                style="width: 100%;padding: 10px;height: 100px;"
                                v-model="archiveFormText"
                                @input="archiveErrorvalidate()"
                            ></textarea>
                            <div class="v-input error--text" id="archive_error_block" style="display:none;">
                                <div class="v-messages__message">Privalomas laukas</div>
                            </div>
                            <div v-if="mail_success">El. Laiškas sėkmingai išsiųstas</div>
                        </div>
                    </div>
                    <br>
                    <div class="text-center">
                        <v-btn
                            color="primary"
                            class="inquiry_confirm_button"
                            @click="archiveFormSubmit()"
                        >
                        SIŲSTI
                        </v-btn>
                        
                    </div>
                    </v-card-text>
            </v-card>
        </v-dialog>
   </div>

      </v-card>
      </v-col>
   </v-row>
</v-container>
</template>

<script>
   import Nl2br from 'vue-nl2br';
   import objectToFormData from 'object-to-formdata';
   import countdown from '@chenfengyuan/vue-countdown';
   import OfferForm from "./OfferForm";
   import Inquiry from "./base/Inquiry";
   
   export default {
      name: "BrokerInquiryList",
      data() {
         return {
            form: {
               search: null,
               region: null,
               type: null,
               service_type: null,
            },
            item_filter: false,
            page: this.current_page,
            items: this.current_items,
            currPage:1,
            searchloader:false,
            dialogarchive:false,
            archivecomplain:false,
            archiveInqId:0,
            archiveFormText:'',
            mail_success:false
         }
      },
      components: {
         Inquiry,
         Nl2br,
         countdown,
         OfferForm
      },
      props: {
         currentCompanyRoute: {
            type: [String, Boolean],
            default() {
               return false
            }
         },
         nofilter: {
            type: Boolean,
            default() {
               return false;
            }
         },
         regions: {
            type: Array,
            default() {
               return []
            }
         },
         service_types: {
            type: Array,
            default() {
               return []
            }
         },
         types: {
            type: Array,
            default() {
               return []
            }
         },
         pages_number: {
            type: Number
         },
         total_visible: {
            type: Number
         },
         expand: {
            type: String
         },
         current_page: {
            type: Number
         },
         current_items: {
            type: Array
         },
         getRoute: {
            type: String,
         }
      },
      mounted(){
            let self = this;
            setInterval(function(){
                self.nextPage(self.currPage,false);
            } ,10000);
    },
    methods: {
        archiveFormSubmit() {
            let self = this;
            if(!this.archiveFormText){
                archive_error_block.style.display = 'block';
            }else{
                archive_error_block.style.display = 'none';
                axios.post(`/uzklausos/archive/complain`)
                .then(response => {
                    self.mail_success = true;
                    setTimeout(function(){
                        self.archivecomplain = false;
                        self.mail_success = false;
                    },2000)
                    self.archiveModalTrue();
                });
            }
            console.log(this.archiveFormText)
        },
        archiveErrorvalidate() {
            archive_error_block.style.display = 'none';
        },
        openarchiveModal(id){
            this.dialogarchive = true;
            this.archiveInqId = id;
        },
        archiveModalTrue() {
             this.dialogarchive = false;
             axios.post(`/uzklausos/${this.archiveInqId}/archyvuoti`)
            .then(response => {
                setTimeout(function(){
                    location.reload();
                }, 2000);
            }).catch(error => {
                setTimeout(function(){
                    location.reload();
                }, 2000);
            });
        },
        archiveModalfalse(){
             this.dialogarchive = false;
             this.archivecomplain = true;
        },
        checkIfPrevAcceptedAndNowNot(index) {
            if (typeof this.items[index - 1] !== undefined && this.items[index - 1]) {
               let item = this.items[index - 1];
               if (typeof item.offer_accepted !== undefined) {
                  return item.offer_accepted && !this.items[index].offer_accepted ? true : false;
               }
            }
            if (this.items[index].offer_accepted) {
               return false
            }
            return true;
        },
        checkIfNextNotAccepted(index) {
            if (typeof this.items[index + 1] !== undefined && this.items[index + 1]) {
               let item = this.items[index + 1];

               if (typeof item.offer_accepted !== undefined) {
                  return !item.offer_accepted ? true : false;
               }
            }

            return false;
         },

         transform(props) {
            Object.entries(props).forEach(([key, value]) => {
               // Adds leading zero
               const digits = value < 10 ? `0${value}` : value;

               props[key] = `${digits}`;
            });

            return props;
         },

         handleClick(item) {
            if (this.$root.role() === 'broker' && !item.offer_accepted) {
               this.$root.$emit('openModal', item);
            }
         },

         submit() {
            this.searchloader = true;
            this.nextPage(1)
         },

         nextPage(value, gotop = true) {
             this.currPage = value;
            const data = objectToFormData({
               page: value,
            }, {
               indices: true,
               nullsAsUndefineds: true
            });

            axios.get(this.getRoute, {
               params: {
                  page: value,
                  search: this.form.search,
                  region: this.form.region,
                  type: this.form.type,
                  service_type: this.form.service_type
               }
            })
               .then(response => {
                   this.searchloader = false;
                  this.items = response.data.items;
                  
                    if(gotop){
                        this.$root.toTop();
                    }
               })

         }
      }
   }
</script>

<style scoped>

</style>
