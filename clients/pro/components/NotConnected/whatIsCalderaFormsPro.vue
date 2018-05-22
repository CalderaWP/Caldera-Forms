<template>
    <div>
        <p>{{ strings.whatIsCFPro.firstParagraph }}</p>
        <h3>{{ strings.whatIsCFPro.hTitle }}</h3>
        <ul class="list">
            <li>{{ strings.whatIsCFPro.firstLi }}</li>
            <li>{{ strings.whatIsCFPro.secondLi }}</li>
            <li>{{ strings.whatIsCFPro.thirdLi }}</li>
            <li>{{ strings.whatIsCFPro.fourthLi }}</li>
        </ul>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex';

  export default {
    name: 'whatIsCalderaFormsPro',
    computed : {
      ...mapGetters([
        'strings'
      ])
    }
  }
</script>

<style scoped>
    .list {
        list-style-type: disc;
        padding-left: 2rem;
    }
</style>