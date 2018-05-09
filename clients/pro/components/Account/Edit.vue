<template>
	<div id="cf-pro-account-edit">
		<div class="caldera-config-group">
			<label for="cf-pro-api-public">
				Public Key
			</label>
			<div class="caldera-config-field">
				<input
						type="text"
						v-model="publicKey"
						id="cf-pro-api-public"
						@change="publicKeyChange"
				/>
			</div>
		</div>

		<div class="caldera-config-group">
			<label for="cf-pro-api-secret">
				Secret Key
			</label>
			<div class="caldera-config-field">
				<input
					type="text"
					v-model="secretKey"
					id="cf-pro-api-secret"
					@change="secretKeyChange"
				/>
			</div>
		</div>

		<p v-if="! connected">
			You can find your API keys in your <a href="https://app.calderaformspro.com/app#/account" target="_blank">
			Caldera Forms Pro Account
		</a>
		</p>
	</div>
</template>
<script>
	import { mapGetters,mapMutations } from 'vuex'
	export default{

		computed: {
			publicKey:{
			    get(){
                    return this.$store.getters.publicKey;
				},
				set(newValue){
                    this.$store.commit('publicKey',newValue);
				}
			},
            secretKey:{
                get(){
                    return this.$store.getters.secretKey;
                },
                set(newValue){
                    this.$store.commit('secretKey',newValue);
                }
            },
            connected:{
                get(){
                    return this.$store.getters.connected;
                },
                set(newValue){
                    this.$store.commit('connected',newValue);
                }
            },

		},
		methods: {
			...mapMutations({
                secretKey: 'secretKey',
            }),
			publicKeyChange(ev){
                this.$store.commit('publicKey',ev.target.value);
			},
			secretKeyChange(ev){
				this.$store.commit('secretKey',ev.target.value);
			}
		}
	}
</script>
<style>
	@import "./style.css";
</style>