<template>
    <div>
        <docs
            :important="contentBoxData.importantDocs"
        >

        </docs>
        <extend
                :product="contentBoxData.product"
                :title="contentExtendTitle"
        ></extend>
    </div>
</template>
<script>

    import { mapGetters } from 'vuex'
    import helpBox from '../components/content/helpBox.vue';
    import productBox from '../components/content/productBox.vue';
    export default {
        components: {
            docs : helpBox,
            extend: productBox
        },
        beforeMount(){
             this.$store.dispatch('getContentBoxData');
        },
        computed: {
            ...mapGetters([
                'contentBoxData',
                'contentExtendTitle'
            ])
        },
        methods: {
            showDocs: function(){
                return showDocs;
            },
            showExtend: function(){
                return showExtend;
            }

        }
    }
</script>