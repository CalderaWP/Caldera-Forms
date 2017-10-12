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
							<a v-bind:href="link( result, 'docs' )" target="_blank">
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


<script type="text/html" id="tmpl--caldera-extend-clippy">
	<div class="caldera-forms-clippy-zone" style="background-image: url( '<?php echo esc_url( CFCORE_URL . 'assets/images/caldera-globe-logo-sm.png' ); ?>' );">
		<div class="caldera-forms-clippy-zone-inner-wrap">

			<div class="caldera-forms-clippy" >
				<h2>{{title}}</h2>
				<h4 v-if="product.name != 'Caldera Forms Pro'">{{product.name}}</h4>
				<p v-html="product.excerpt"></p>
				<a v-bind:href="link( product, 'extend' )" target="_blank" class="bt-btn btn btn-orange">
					<?php esc_html_e( 'Learn More', 'caldera-forms' ); ?>
				</a>
			</div>

		</div>
	</div>

</script>
