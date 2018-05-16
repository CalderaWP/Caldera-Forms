<template>
    <div>
        <p class="paragraph">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean nec dapibus felis, vitae malesuada libero. Integer rutrum sodales nulla, eget fermentum ligula faucibus in. Nulla efficitur ac urna sit amet fringilla.</p>
        <div>
            <a class="cf-button-linked" v-on:click="clickedFreeTrial">View Documentation</a>
            <a class="cf-button-linked" v-on:click="clickedDocumentation">Start Free Trial</a>
        </div>
    </div>
</template>

<script>
  import { mapGetters,mapMutations } from 'vuex'

  export default {
    name: 'freeTrial',
    method: {
      clickedFreeTrial(){
        this.$store.dispatch('freeTrialLink');
      },
      clickedDocumentation(){
        this.$store.dispatch('documentationLink');
      }
    }
  }
</script>

<style scoped>
    .paragraph {
        max-width: 40rem;
    }
    .cf-button-linked {
        padding: 1rem 1.4rem;
        background-color: #ff7e30;
        color: white;
        cursor: pointer;
        display: inline-block;
    }
</style>