<?php 
echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<?php ob_start(); ?>
		<select <?php echo $field_placeholder; ?> id="<?php echo esc_attr( $field_id ); ?>" data-field="<?php echo esc_attr( $field_base_id ); ?>" class="<?php echo esc_attr( $field_class ); ?>" name="<?php echo esc_attr( $field_name ); ?>" <?php echo $field_required; ?>>
			<option value=""> - Select Province/State - </option>
			<option value="AB">Alberta</option>
			<option value="BC">British Columbia</option>
			<option value="MB">Manitoba</option>
			<option value="NB">New Brunswick</option>
			<option value="NL">Newfoundland and Labrador</option>
			<option value="NS">Nova Scotia</option>
			<option value="NT">Northwest Territories</option>
			<option value="NU">Nunavut</option>
			<option value="ON">Ontario</option>
			<option value="PE">Prince Edward Island</option>
			<option value="QC">Quebec</option>
			<option value="SK">Saskatchewan</option>
			<option value="YT">Yukon</option>
			<option> ==================== </option>
			<option value="AL">Alabama</option>
			<option value="AK">Alaska</option>
			<option value="AZ">Arizona</option>
			<option value="AR">Arkansas</option>
			<option value="CA">California</option>
			<option value="CO">Colorado</option>
			<option value="CT">Connecticut</option>
			<option value="DE">Delaware</option>
			<option value="DC">District Of Columbia</option>
			<option value="FL">Florida</option>
			<option value="GA">Georgia</option>
			<option value="HI">Hawaii</option>
			<option value="ID">Idaho</option>
			<option value="IL">Illinois</option>
			<option value="IN">Indiana</option>
			<option value="IA">Iowa</option>
			<option value="KS">Kansas</option>
			<option value="KY">Kentucky</option>
			<option value="LA">Louisiana</option>
			<option value="ME">Maine</option>
			<option value="MD">Maryland</option>
			<option value="MA">Massachusetts</option>
			<option value="MI">Michigan</option>
			<option value="MN">Minnesota</option>
			<option value="MS">Mississippi</option>
			<option value="MO">Missouri</option>
			<option value="MT">Montana</option>
			<option value="NE">Nebraska</option>
			<option value="NV">Nevada</option>
			<option value="NH">New Hampshire</option>
			<option value="NJ">New Jersey</option>
			<option value="NM">New Mexico</option>
			<option value="NY">New York</option>
			<option value="NC">North Carolina</option>
			<option value="ND">North Dakota</option>
			<option value="OH">Ohio</option>
			<option value="OK">Oklahoma</option>
			<option value="OR">Oregon</option>
			<option value="PA">Pennsylvania</option>
			<option value="RI">Rhode Island</option>
			<option value="SC">South Carolina</option>
			<option value="SD">South Dakota</option>
			<option value="TN">Tennessee</option>
			<option value="TX">Texas</option>
			<option value="UT">Utah</option>
			<option value="VT">Vermont</option>
			<option value="VA">Virginia</option>
			<option value="WA">Washington</option>
			<option value="WV">West Virginia</option>
			<option value="WI">Wisconsin</option>
			<option value="WY">Wyoming</option>
		</select>
		<?php
			$states = ob_get_clean();
			if( !empty( $field_value ) ){
				$states = str_replace( 'value="' . $field_value . '"', 'value="' . $field_value . '" selected="selected"', $states );
			}
			echo $states;
		?>

		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
