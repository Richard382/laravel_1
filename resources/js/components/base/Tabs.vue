<template>
    <v-tabs
        v-model="tab"
        fixed-tabs
        background-color="transparent"
        color="basil"
        class="switch-type-tab v-tabs--register mb-10"
    >
        <v-tabs-slider color="primary"></v-tabs-slider>
        <template
            v-for="tab in tabs"
        >
            <v-tab
                :href="tab.link"
                :disabled="tab.disabled"
                link
            >
                {{ tab.text }}
            </v-tab>
        </template>
    </v-tabs>
</template>

<script>
  export default {
    name: "Tabs",
    data() {
      return {
        tab: ''
      }
    },
    props: {
      tabs: {
        type: Array,
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
