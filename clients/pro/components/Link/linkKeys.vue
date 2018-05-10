<template>
    <a v-bind:href="strings.apiKeysViewLink" :alt="strings.apiKeysViewText" target="_blank">{{ strings.apiKeysViewText }}</a>
</template>
<script>
    import { mapGetters } from 'vuex'

    export default{

        computed :{
            ...mapGetters([
                'strings'
            ])
        },

    }
</script>
