<template>
    <v-container fluid id="contentlist_container">
        <h2 class="text-centers">Naujausios <b>UŽKLAUSOS</b></h2>
        <hr />
      <v-row dense
          class="ml-6 mr-6"
      >
        <v-col
          v-for="(item, i) in inquiriesData"
          :key="i"
          cols="12"
          class="pa-2"
        >
          <v-card
            class="mx-auto"
            max-width="1200"
          >
            <v-row no-gutters class="">
                <v-col
                cols="12"
                sm="2"
                class="">
                    <p class="text-center mt-2">
                        Užklausa Nr. {{item.id}}
                    </p>
                    <a href="/prisijungti">
                    <v-btn 
                        v-if="item.service_id == 1"
                        class="specialist_btn white--text"
                        color="#FF5C5C"
                    >
                        IEŠKAU
                    </v-btn>
                    <v-btn 
                        v-if="item.service_id == 2"
                        class="specialist_btn white--text"
                        color="#0000CD"
                    >
                        SIŪLAU
                    </v-btn>
                    <v-btn 
                        v-if="item.service_id == 3"
                        class="specialist_btn white--text"
                        color="#787E89"
                    >
                        KITA
                    </v-btn>
                    <v-btn 
                        v-if="item.service_id == 4"
                        class="specialist_btn white--text"
                        color="#787E89"
                    >
                        IEŠKAU išsinuomoti
                    </v-btn>
                    </a>
                </v-col>
                <v-col
                cols="12"
                sm="2"
                class="pa-4 card-letter-box"
                >
                    <div>Tipas</div>
                    <div>
                        <h4 class="service_title">{{item.property_type[0]['name']}}</h4>
                    </div>    
                    <div>Objektas</div>
                    <div>
                        <h4 class="service_title">{{item.properties[0]['name']}}</h4>
                    </div>
                </v-col>
                <v-col
                cols="12"
                sm="5"
                class="pa-4 card-letter-box"
                >
                    <div>Paslauga</div>
                    <div>
                        <h4 class="service_title">
                            {{ item.service['name'] }}
                        </h4>
                    </div>
                    <div>Vietovė</div>
                    <div>
                        <h4 class="service_title">{{ item.sum_up }}</h4>
                    </div>
                    <div class="text-right hidden d-none">
                        <v-btn color="green" class="white--text">ŽIŪRĖTI</v-btn>
                    </div>
                </v-col>
                <v-col
                    cols="12"
                    sm="3"
                    class=""
                >
                    <v-list-item two-line>
                        <v-list-item-content>
                            <v-list-item-title :class="item.service_id != 2 ? '':'d-none'">
                                <v-row>
                                    <v-col sm="3">
                                        <h3 class="mt-2">nuo</h3>
                                    </v-col>
                                    <v-col sm="9">
                                        <h3 class="h2 kaina-num">€ {{item.price_from}}</h3>
                                    </v-col>
                                </v-row>
                            </v-list-item-title>
                            <v-list-item-title :class="item.service_id != 2 ? '':'d-none'">
                                <v-row>
                                    <v-col sm="3">
                                        <h3 class="mt-2">iki</h3>
                                    </v-col>
                                    <v-col sm="9">
                                        <h3 class="h2 kaina-num">€ {{item.price_to}}</h3>
                                    </v-col>
                                </v-row>
                            </v-list-item-title>
                            <v-list-item-title v-if="item.service_id == 2">
                                <v-row class="mx-auto">
                                    <v-col cols="12" sm="12" class="text-center">
                                        <h3 class="h2 kaina-num">€ {{item.price_from}}</h3>
                                    </v-col>
                                </v-row>
                            </v-list-item-title>
                            <hr />
                            <span class="text-center price_label">
                                kaina
                            </span>
                        </v-list-item-content>
                    </v-list-item>
                </v-col>
            </v-row>
          </v-card>
        </v-col>
      </v-row>
    <v-row class="mt-6">
        <v-col class="text-center">
            <v-btn 
            color="#363A7B"  
            elevation="17"
            x-large 
            class="white--text size-2rem"
            :href="$root.authenticated() ? uzklausosPath : brokerPath"
            >
            VISI SKELBIMAI
            </v-btn>
        </v-col>
    </v-row>


    <v-card
        class="mx-auto mt-6"
        max-width="1200"
    >
    <v-row 
        class="youtube_area"
    >
        <v-col
            cols="12"
            sm="6"
            class=""
        >
        <div class="ml-8 mr-4 mb-8">
            <h2 class="size-2rem size-2rem mx-auto my-12">
                <span class="youtube_link_title">Ieškote, perkate, parduodate?</span><br/>
                <b class="youtube_link_title_bold">Registruokitės</b>
                <a href="https://cont.lt/registracija/vartotojas" class="youtube_link_title_bold_green">kaip užsakovas!</a>
            </h2>
        </div>
        <div class="video-responsive ml-8 mr-4 mb-8">
			<iframe width="100%" class="mx-auto d-block" src="https://www.youtube.com/embed/V2TNpThEc4o" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		</div> 
        </v-col>
        <v-col
            cols="12"
            sm="6"
            class=""
        >
        <div class="ml-4 mr-8 mb-8">
            <h2 class="size-2rem size-2rem mx-auto my-12">
                <span class="youtube_link_title">Teikiate NT paslaugas?</span><br/>
                <b class="youtube_link_title_bold">Registruokitės kaip</b> 
                <a href="https://cont.lt/registracija/brokeris" class="youtube_link_title_bold_green">NT specialistas!</a>
            </h2>
        </div>
        <div class="video-responsive ml-4 mr-8 mb-8">
			<iframe width="100%" class="mx-auto d-block" src="https://www.youtube.com/embed/V2TNpThEc4o" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		</div> 
        </v-col>
    </v-row>
    </v-card>

    </v-container>
</template>
<script>

   export default {
       components : {
           
       },
      data() {
         return {
            inquiriesData:this.inquiries,
            brokerPath:"https://cont.lt/registracija/brokeris",
            uzklausosPath:"https://cont.lt/uzklausos"
         }
      },
      props: {
         inquiries: {
            type: [Array]
         },
      },
      mounted(){
            this.getNewInquiries()
      },
      methods:{
          getNewInquiries(){
              let self = this;
            axios.get('/home/getnewinquiries')
                .then(response => {
                    self.inquiriesData = response.data.data
                })
          }
      }
   }
</script>
</script>