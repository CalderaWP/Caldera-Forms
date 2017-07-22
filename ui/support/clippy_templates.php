<?php

?>

<script type="text/html" id="tmpl--caldera-help-clippy">
	<div class="caldera-forms-clippy-zone" style="background-image: url( '<?php echo esc_url( CFCORE_URL . 'assets/images/caldera-globe-logo-sm.png' ); ?>' );">
		<div class="caldera-forms-clippy-zone-inner-wrap">
			<div class="caldera-forms-clippy">
				<h2><?php esc_html_e( 'Documentation', 'caldera-forms' ); ?></h2>
				<div>
					<ul v-for="result in important">
						<li>
							<a v-bind:href="result.link" target="_blank">
								<span v-html="result.title"></span>
							</a>
						</li>
					</ul>
				</div>
				<a href="https://calderaforms.com/documentation/caldera-forms-documentation?utm-source=wp-admin&utm_campaign=clippy&utm_term=docs" target="_blank" class="bt-btn btn btn-green">
					<?php esc_html_e( 'All Documentation', 'caldera-forms' ); ?>
				</a>
			</div>
		</div>
	</div>
</script>


<script type="text/html" id="tmpl--caldera-support-clippy">
	<div class="caldera-forms-clippy-zone" style="background-image: url( '<?php echo esc_url( CFCORE_URL . 'assets/images/caldera-globe-logo-sm.png' ); ?>' );">
		<div class="caldera-forms-clippy-zone-inner-wrap">
			<div class="caldera-forms-clippy">
				<h2>
					<?php esc_html_e( 'Need Support?', 'caldera-forms' ); ?>
				</h2>
				<button v-on:click="greet">Greet</button>
				<label for="caldera-forms-docs-support-question">
					<?php esc_html_e( 'Do You?', 'caldera-forms' ); ?>
				</label>
				<select id="caldera-forms-docs-support-question"  v-model="type"">
					<option v-bind:value="'addOn'">
						<?php esc_html_e( 'Own An Add-on', 'caldera-forms' ); ?>
					</option>
					<option v-bind:value="'havePriority'">
						<?php esc_html_e( 'Have A Priority Support Subscription', 'caldera-forms' ); ?>
					</option>
					<option v-bind:value="'reportBug'">
						<?php esc_html_e( 'Need To Report A Bug', 'caldera-forms' ); ?>
					</option>
					<option v-bind:value="'wantPriority'">
						<?php esc_html_e( 'Would Like To Purchase Priority Support', 'caldera-forms' ); ?>
					</option>
				</select>

				<div v-if="'addOn' === type">
					You can open a support ticket <a href="https://calderaforms.com/support">here</a>.
				</div>
				<div v-if="'havePriority' === type">
					You can open a support ticket <a href="https://calderaforms.com/caldera-forms-priority-support/">here</a>.
				</div>
				<div v-if="'wantPriority' === type">
					You can purchase priority support <a href="https://calderaforms.com/downloads/caldera-forms-priority-support/">here</a>.
				</div>
				<div v-if="'other' === type">
					We do not currently offer free support. Please consider supporting us by <a href="https://calderaforms.com/caldera-forms-add-ons/">purchasing an add-on</a> or a <a href="https://calderaforms.com/downloads/caldera-forms-priority-support/">priority support subscription</a>.
				</div>
				<div v-if="'reportBug' === type">
				<div v-if="'reportBug' === type">
					Please open an in issue on <a href="https://github.com/calderawp/caldera-forms/issues" target="_blank">Github</a> with all of the requested information.
				</div>
			</div>
		</div>
	</div>
</script>


<script type="text/html" id="tmpl--caldera-extend-clippy">
	<div class="caldera-forms-clippy-zone" style="background-image: url( '<?php echo esc_url( CFCORE_URL . 'assets/images/caldera-globe-logo-sm.png' ); ?>' );">
		<div class="caldera-forms-clippy-zone-inner-wrap">

			<div class="caldera-forms-clippy" >
				<h2>{{title}}</h2>
				<h4 v-if="product.name != 'Caldera Forms Pro'">{{product.name}}</h4>
				<p v-html="product.excerpt"></p>
				<a v-bind:href="product.link" target="_blank" class="bt-btn btn btn-orange">
					<?php esc_html_e( 'Learn More', 'caldera-forms' ); ?>
				</a>
			</div>

		</div>
	</div>

</script>
