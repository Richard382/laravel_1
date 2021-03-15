<template>
    <v-tabs
        v-model="tab"
        fixed-tabs
        background-color="transparent"
        color="basil"
        class="v-tabs--register mb-10"
    >
        <v-tabs-slider color="primary"></v-tabs-slider>
        <v-tab
            v-if="userRegisterLink"
            :href="userRegisterLink"
            link
        >
            Registracija ieškantiems
        </v-tab>
        <v-tab
            v-if="brokerRegisterLink"
            :href="brokerRegisterLink"
            link
        >
            Registracija teikėjams
        </v-tab>
    </v-tabs>
</template>

<script>
  export default {
    name: "RegisterTabs",
    data() {
      return {
        tab: ''
      }
    },
    props: {
      userRegisterLink: {
        type: String,
      },
      brokerRegisterLink: {
        type: String,
      },
      currentLink: {
        type: String,
        default() {
          return false
        }
      }
    },
    created() {
      this.tab = this.currentLink
    }
  }
</script>

<style scoped>

</style>
