<template>
    <div>
        <div class="caldera-config-group">
            <div aria-labelledby="minLogLevelTitle" aria-describedby="minLogLevelInfo">
                <label for="cf-pro-form-log-level-chooser" id="minLogLevelTitle">
                    {{ strings.minLogLevelTitle }}
                </label>
                <p id="minLogLevelInfo">{{ strings.minLogLevelInfo}}</p>
            </div>
            <div class="caldera-config-field">
                <select
                        id="cf-pro-form-log-level-chooser"
                        v-model="logLevelValue"
                >
                    <option v-for="level in logLevels" v-bind:value="level.number" v-key="level.number">
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
                'logLevel',
                'strings'
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