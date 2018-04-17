<template>
    <div class="caldera-forms-clippy-zone">
        <div class="caldera-forms-clippy-zone-inner-wrap">
            <div class="caldera-forms-clippy" >
                <h2>{{title}}</h2>
                <h4 v-if="product.name != 'Caldera Forms Pro'">{{product.name}}</h4>
                <p v-html="product.excerpt"></p>
                <a v-bind:href="link( product, 'extend' )" target="_blank" class="bt-btn btn btn-orange">
                    Learn More
                </a>
            </div>
        </div>
    </div>
</template>
<script>
    import {clippyLinkCB} from '../utils/clippyLinkCB';
    export default {
        props: [ 'product','title' ],
        methods: {
            link:clippyLinkCB,
        }
    }
</script>