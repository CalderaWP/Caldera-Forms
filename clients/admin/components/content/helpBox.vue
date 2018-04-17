<template>
    <div class="caldera-forms-clippy-zone">
        <div class="caldera-forms-clippy-zone-inner-wrap">
            <div class="caldera-forms-clippy">
                <h2>Documentation</h2>
                <div>
                    <ul v-for="result in important">
                        <li>
                            <a v-bind:href="link( result, 'docs' )" target="_blank">
                                <span v-html="result.title"></span>
                            </a>
                        </li>
                    </ul>
                </div>
                <a href="https://calderaforms.com/documentation/caldera-forms-documentation?utm-source=wp-admin&utm_campaign=clippy&utm_term=docs" target="_blank" class="bt-btn btn btn-green">
                    All Documentation
                </a>
            </div>
        </div>
    </div>
</template>
<script>
    import {clippyLinkCB} from  '../utils/clippyLinkCB';
    export default {
        props: [ 'important' ],
        methods: {
            link:clippyLinkCB,

        }
    }
</script>