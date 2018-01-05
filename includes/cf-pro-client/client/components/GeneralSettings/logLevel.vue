<template>
    <div>
        <div class="caldera-config-group">
            <label for="cf-pro-form-log-level-chooser">
                Set Log level
            </label>
            <div class="caldera-config-field">
                <select
                        id="cf-pro-form-log-level-chooser"
                        v-model="logLevelValue"
                >
                    <option v-for="level in logLevels" v-bind:value="level.number">
                        {{ level.name }}
                    </option>

                </select>
            </div>
        </div>
    </div>

</template>
<script>
    import { mapGetters } from 'vuex';

    export default{
        computed : {
            ...mapGetters([
                'logLevels',
                'logLevel'
            ]),
            logLevelValue: {
                get() {
                    return this.logLevel;
                },
                set(value) {
                    this.$store.commit('logLevel', value);
                }
            },
        },
    }

</script>