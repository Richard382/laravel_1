<template>
<v-card
      class="mx-auto"
      max-width="1200"
    >
    <div class="inquiries__list">
        <div v-if="isbroker == 1" class="create-inq-prov-link">
            <a v-bind:href="homepage_url">Kurti naują užklausą NT specialistams</a></div>
        <div v-if="isbroker == 0" class="create-inq-cust-link">
            <div style="width:50%;float: left;margin-top: -22px;">
                <a href="/profilis/registruotis-nt-specialistu">Esate NT specialistas?</a>
            </div>
            <div style="width:50%;float: right;">
                <a v-bind:href="homepage_url">Kurti naują užklausą NT specialistams</a>
            </div>
            </div>
        <div class="clearfix"></div>
        <template v-for="(item, index) in items">
            <inquiry :item="item"  :key="item.id"></inquiry>
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
        
        <v-dialog  v-model="dialog" width="600px">
            <v-card>
                <v-btn
                    icon
                    class="v-btn--close-dialog"
                    @click="dialog = false"
                >
                    <v-icon>mdi-close</v-icon>
                </v-btn>
               
                <v-card-text>

                    <div class="v-dialog__text">
                        <p style="font-size: 18px;padding-top: 20px;">Ar tikrai norite negrįžtamai ištrinti šią užklausą?</p>
                    </div>

                    <br>
                <div class="text-center">
                        <v-btn
                            color="default"
                            class="inquiry_confirm_button"
                            @click="dialog = false"
                        >
                            Atšaukti
                        </v-btn>
                            <v-btn
                            color="red"
                            class="inquiry_confirm_button"
                            @click="delteInquiry()"
                        >
                             Ištrinti
                        </v-btn>
                    </div>
                    </v-card-text>
            </v-card>
        </v-dialog>
    </div>
    
</v-card>
    
</template>

<script>
  import objectToFormData from 'object-to-formdata';
  import OfferForm from "./OfferForm";
  import Inquiry from "./base/Inquiry";

  export default {
    name: "MyInquiriesList",
    data() {
      return {
        page: this.current_page,
        items: this.current_items,
        currPage:1,
        dialog:false,
        InqId:0,
      }
    },
    components: {
      Inquiry,
      OfferForm
    },
    props: {
      pages_number: {
        type: Number
      },
      isbroker: {
        type: Number
      },
      homepage_url: {
          type: String
      },
      total_visible: {
        type: Number
      },
      current_page: {
        type: Number
      },
      current_items: {
        type: Array
      },
    },
    mounted(){
        let self = this;
        setInterval(function(){
            self.nextPage(self.currPage,false);
        } ,10000);
    },
    methods: {
        openDeleteModal(id){
          this.dialog = true;
          this.InqId = id;
        },
        delteInquiry() {
            let self = this;
            axios.post('/uzklausos/' + self.InqId +'/delete')
            .then(response => {
              if(response.data.success){
                self.dialog = false;
                self.nextPage(self.currPage);
              }
            })
        },
      checkIfNextNotAccepted(index)
      {
        if (typeof this.items[index + 1] !== undefined && this.items[index + 1])
        {
          let item = this.items[index + 1];

          if (typeof item.offer_accepted !== undefined) {
            return ! item.offer_accepted ? true : false;
          }
        }

        return false;
      },

      handleClick(item)
      {
        if (this.$root.role() === 'broker' && ! item.offer_accepted)
        {
          this.$root.$emit('openModal', item);
        }
      },

      nextPage(value,gotop = true)
      {
        this.currPage = value;
        const data = objectToFormData({
          page: value
        }, {
          indices: true,
          nullsAsUndefineds: true
        });

        axios.get('?page=' + value)
        .then(response => {
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
