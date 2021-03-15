<template>
    <v-menu
        v-model="menu"
        :close-on-content-click="false"
        :nudge-width="250"
        offset-x
    >
        <template v-slot:activator="{ on }">
            <v-btn
                icon
                :ripple="false"
                v-on="on"
                @click="readAll"
            >
                <v-badge
                    v-model="show_count"
                    class="v-badge--bell"
                    color="red"
                >
                    <template v-slot:badge>{{ count }}</template>
                    <v-img src="/images/bell.png" />
                </v-badge>
            </v-btn>
        </template>

        <v-card>
            <v-list v-if="available" class="notification-wrapper">
                <template
                    v-for="(item, index) in items"
                >
                    <v-divider
                        v-if="index !== 0"
                    ></v-divider>
                    <div class="notification-list__item">
                    <v-list-item
                        :href="item.data.link"
                        :key="index"
                    >
                        <v-list-item-content>
                            <v-list-item-title>
                                <strong>{{ item.data.sub_message }}</strong> {{ item.data.message }}
                            </v-list-item-title>
                            <v-list-item-subtitle v-text="item.created_at"></v-list-item-subtitle>
                        </v-list-item-content>
                        
                    </v-list-item>
                        <span class="notification_close" @click="read(item)">&times;</span>
                    </div>
                </template>
            </v-list>

            <v-card-text>
                <v-alert
                    outlined
                    class="v-alert--nt-info"
                    v-if=" ! available"
                >
                    Nėra naujienų!
                </v-alert>
            </v-card-text>

            <v-spacer></v-spacer>

            <v-card-actions>
                <v-btn
                    color="primary"
                    width="100%"
                    @click="menu = false"
                >
                    Uždaryti
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-menu>
</template>

<script>
  export default {
    name: "Notifications",
    data() {
      return {
        items: this.notifications,
//        count: this.notifications.length,
        count: this.isallview,
        menu: false,
        message: false,
        available: this.notifications.length > 0,
//        show_count: this.notifications.length > 0,
        show_count: this.isallview > 0,
        hints: true,
      }
    },
    props: {
      notifications: {
        type: Array,
      },
      isallview: {
          type: String
      }
    },
    mounted(){
        let self = this;
        setInterval(function(){
            console.log("notification getting");
            self.getNotifications();
        } ,10000);
        
    },
    methods: {
      lowerCount(by = 1)
      {
//        this.count = this.count - Number(by);
//
//        if (this.count === 0) {
          this.show_count = false;
//        }
      },

      readAll()
      {
         let self = this;
         console.log(this.isallview)
         this.items.forEach(function (item) {
            self.view(item)
         })
      },
      getNotifications(){
        let self = this;
        axios.get('/notifications/get/new').then((res) => {
            if(res.data && res.data.success ) {
                self.items = res.data.notifications;
                self.available = res.data.notifications.length > 0;
                self.count = res.data.notviewed;
                self.show_count = res.data.notviewed > 0;
            }
        });  
      },
      read(item)
      {
        item.read_at = true;
//        this.lowerCount();
        
        this.items = this.items.filter((x) => x.id != item.id);
        this.available = this.items.length > 0;
        axios.post('/notifications/' + item.id);
        let ByCLass = document.getElementsByClassName('v-menu__content')
        if(ByCLass.length){
            ByCLass[0].style.left = 'unset';
            ByCLass[0].style.right = '10px';
        }
      },
      view(item)
      {
        item.read_at = true;
        this.lowerCount();

        axios.post('/notifications/view/' + item.id);
      }
    }
  }
</script>
