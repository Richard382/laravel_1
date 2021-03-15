<template>
   <div>
      
       <v-tabs
         v-model="tab"
         background-color="transparent"
         color="basil"
         grow
         style="display:none"
      >
         <v-tabs-slider color="primary"></v-tabs-slider>
         
         <v-tab
            v-text="'Paskutinės užklausos'"
            key="uzklausos"
         />
      </v-tabs>
       <div>
           <h2 class="text-center page-title">Paskutinės užklausos</h2>
       </div>
       <div class="inquiries__list__meanings">
            <div class="d-flex">
               <div class="flex searching">Ieško</div>
               <div class="flex suggest">Siūlo</div>
               <div class="flex other">Kita</div>
            </div>
         </div>
        <v-tabs-items
           v-model="tab"
           class="mt-5"
        >
            <v-tab-item
               key="uzklausos"
            >
            <div class="inquiries">
                <template v-if="inckey">
                <inquiry-list
                   :current_items="inquiriesData"
                />
               </template>
            </div>
            </v-tab-item>
        </v-tabs-items>
         <br>
        <a v-bind:href="islogin?(isbroker?'uzklausos':'mano-uzklausos'):'/registracija/vartotojas'">
        <v-btn
            color="primary"
            style="text-transform:initial"
            width="100%"
            class="mt-0" >
            Daugiau užklausų
        </v-btn>
        </a>
       <div class="videos section ">
            <!--<v-container>-->
            <div class="videos_section_content">
                <h2 class="text-center page-title">Kaip tai veikia</h2>
                <div class="video-responsive">
                    <iframe width="100%" class="mx-auto d-block" src="https://www.youtube.com/embed/OzdQgm4JMCQ" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
                <br> <br>
                <div class="video-responsive">
                    <iframe width="100%" class="mx-auto d-block" src="https://www.youtube.com/embed/V2TNpThEc4o" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>
            <!--</v-container>-->
        </div>
       <v-tabs
         v-model="tab"
         background-color="transparent"
         color="basil"
         grow
         style="display:none"
      >
         <v-tabs-slider color="primary"></v-tabs-slider>
         <v-tab
            v-text="'Rekomenduojami Nt Specialistai'"
            key="brokeriai"
         />
      </v-tabs>
         <div>
           <h2 class="text-center page-title">Rekomenduojami Nt Specialistai</h2>
       </div>
       <v-tabs-items
         v-model="tab"
         class="mt-5"
      >
         <v-tab-item
            key="brokeriai"
         >
            <broker-list
               :prop_items="companies"
               nofilter
               class="inquiries"
               :get-route="brokerGetRoute"
            />
         </v-tab-item>
      </v-tabs-items>
   </div>
</template>

<script>
   import BrokerList from "./BrokerList";
   import InquiryList from "./InquiryList";

   export default {
      name: "Recommended",
      components: {BrokerList, InquiryList},
      data() {
         return {
            tab: null,
            inquiriesData:this.inquiries,
            inckey:true
         }
      },
      props: {
         inquiries: {
            type: [Array]
         },
         companies: {
            type: [Array]
         },
         brokerGetRoute: {
            type: String,
         },
         islogin: {
            type: Number
         },
         isbroker: {
            type: Number
         },
      },
      mounted(){
            let self = this;
            setInterval(function(){
                self.getNewInquiries();
            } ,10000);
      },
      methods:{
          getNewInquiries(){
            let self = this;
            axios.get('/home/getnewinquiries')
                .then(response => {
//                    self.inckey = false;
//                    self.inquiriesData = response.data.data;
//                    setTimeout(function(){
//                         self.inckey = true;
//                    },100)
//                    console.log('new innnnn')
//                    self.$set(self, 'inquiriesData', response.data.data);
                    response.data.data.forEach((ele,i) => {
                        self.$set(self.inquiriesData, i, ele)
                    })
                  
                })
          }
      }
//      watch: {
//        inquiriesData: function (val) {
//          this.inquiriesData = val 
//        },
//      }
   }
</script>

<style scoped>

</style>
