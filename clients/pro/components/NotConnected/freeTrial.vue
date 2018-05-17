<template>
    <div>
        <p class="paragraph">{{ strings.freeTrial.firstParagraph }}</p>
        <div>
            <a class="cf-button-linked" href="https://calderaforms.com/caldera-forms-documentation-2/" target="_blank">{{ strings.freeTrial.buttonLeft }}</a>
            <a class="cf-button-linked" href="https://calderaforms.com/pro/#buy" target="_blank">{{ strings.freeTrial.buttonRight }}</a>
        </div>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex'

  export default {
    name: 'freeTrial',
    computed : {
      ...mapGetters([
        'strings'
      ])
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
        text-decoration: none;
    }
</style>