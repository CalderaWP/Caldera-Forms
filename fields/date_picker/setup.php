<div class="caldera-config-group">
	<label for="{{_id}}_default">
        <?php esc_html_e('Default', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<input id="{{_id}}_default" type="text" class="block-input field-config is-not-cfdatepicker magic-tag-enabled" data-dontprovide="cfdatepicker" id="{{id}}" data-date-format="{{format}}" name="{{_name}}[default]" value="{{default}}" />
	</div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_format">
        <?php esc_html_e('Format', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<input id="{{_id}}_format" type="text" class="cfdatepicker-set-format block-input field-config" id="{{id}}" name="{{_name}}[format]" value="{{format}}">
	</div>
</div>
<div class="caldera-config-group">
    <label>
        <?php esc_html_e('Autoclose', 'caldera-forms'); ?>
    </label>
    <div class="caldera-config-field">
        <label id="{{_id}}_autoclosed">
            <input id="{{_id}}_autoclosed" aria-describedby="{{_id}}_autoclosed-description" type="checkbox" class="field-config {{_id}}_autoclosed" name="{{_name}}[autoclose]" value="1" {{#if autoclose}}checked="checked"{{/if}} />
            <?php esc_html_e('Enable autoclose', 'caldera-forms'); ?>
        </label>
        <p class="description" id="{{_id}}_autoclosed-description">
            <?php esc_html_e('If enabled, the date picker will automatically close after selecting the final input', 'caldera-forms'); ?>
        </p>
    </div>
</div>
<div class="caldera-config-group">
    <label for="{{_id}}_startview">
        <?php esc_html_e('Start View', 'caldera-forms'); ?>
    </label>
    <div class="caldera-config-field">
        <select class="block-input field-config" name="{{_name}}[start_view]" id="{{_id}}_startview" aria-describedby="{{_id}}_startview-description">
            <option value="month" {{#is start_view value="month"}}selected="selected"{{/is}}>
                <?php esc_html_e('Month (Default)', 'caldera-forms'); ?>
            </option>
            <option value="year" {{#is start_view value="year"}}selected="selected"{{/is}}>
                <?php esc_html_e('Year', 'caldera-forms'); ?>
            </option>
            <option value="decade" {{#is start_view value="decade"}}selected="selected"{{/is}}>
                <?php esc_html_e('Decade', 'caldera-forms'); ?>
            </option>
        </select>
        <p class="description" id="{{_id}}_startview-description"><?php _e('The starting view of the date picker (month, year, decade)', 'caldera-forms'); ?></p>
    </div>
</div>
<div class="caldera-config-group">
    <label for="{{_id}}_startdate">
        <?php esc_html_e('Start Date', 'caldera-forms'); ?>
    </label>
    <div class="caldera-config-field">
        <input id="{{_id}}_startdate" aria-describedby="{{_id}}_startdate-description" type="text" class="cfdatepicker-set-format block-input field-config" name="{{_name}}[start_date]" value="{{start_date}}">
        <p class="description" id="{{_id}}_startdate-description">
            <?php esc_html_e('The starting date of the date picker like +1d, -2y, +4m. 0d for today.', 'caldera-forms'); ?>
        </p>
    </div>
</div>
<div class="caldera-config-group">
    <label for="{{_id}}_end-date">
        <?php esc_html_e('End Date', 'caldera-forms'); ?>
    </label>
    <div class="caldera-config-field">
        <input id="{{_id}}_end-date" aria-describedby="{{_id}}_end-date-description" type="text" class="cfdatepicker-set-format block-input field-config" name="{{_name}}[end_date]" value="{{end_date}}">
        <p class="description" id="{{_id}}_end-date-description">
                <?php esc_html_e('The ending date of the date picker like +1d, -2y, +4m. 0d for today.', 'caldera-forms'); ?>
        </p>
    </div>
</div>
<div class="caldera-config-group">
	<label for="{{_id}}_language">
        <?php esc_html_e('language', 'caldera-forms'); ?>
    </label>
	<div class="caldera-config-field">
		<select id="{{_id}}_language" aria-describedby="{{_id}}_language-description" class="cfdatepicker-set-language block-input field-config" id="{{id}}" name="{{_name}}[language]" style="width: 90px;">
			<option value="">en-US</option>
			<option value="ar" {{#is language value="ar"}}selected="selected"{{/is}}>ar</option>
			<option value="az" {{#is language value="az"}}selected="selected"{{/is}}>az</option>
			<option value="bg" {{#is language value="bg"}}selected="selected"{{/is}}>bg</option>
			<option value="bs" {{#is language value="bs"}}selected="selected"{{/is}}>bs</option>
			<option value="ca" {{#is language value="ca"}}selected="selected"{{/is}}>ca</option>
			<option value="cs" {{#is language value="cs"}}selected="selected"{{/is}}>cs</option>
			<option value="cy" {{#is language value="cy"}}selected="selected"{{/is}}>cy</option>
			<option value="da" {{#is language value="da"}}selected="selected"{{/is}}>da</option>
			<option value="de" {{#is language value="de"}}selected="selected"{{/is}}>de</option>
			<option value="el" {{#is language value="el"}}selected="selected"{{/is}}>el</option>
			<option value="en-GB" {{#is language value="en-GB"}}selected="selected"{{/is}}>en-GB</option>
			<option value="es" {{#is language value="es"}}selected="selected"{{/is}}>es</option>
			<option value="et" {{#is language value="et"}}selected="selected"{{/is}}>et</option>
			<option value="eu" {{#is language value="eu"}}selected="selected"{{/is}}>eu</option>
			<option value="fa" {{#is language value="fa"}}selected="selected"{{/is}}>fa</option>
			<option value="fi" {{#is language value="fi"}}selected="selected"{{/is}}>fi</option>
			<option value="fo" {{#is language value="fo"}}selected="selected"{{/is}}>fo</option>
			<option value="fr-CH" {{#is language value="fr-CH"}}selected="selected"{{/is}}>fr-CH</option>
			<option value="fr" {{#is language value="fr"}}selected="selected"{{/is}}>fr</option>
			<option value="gl" {{#is language value="gl"}}selected="selected"{{/is}}>gl</option>
			<option value="he" {{#is language value="he"}}selected="selected"{{/is}}>he</option>
			<option value="hr" {{#is language value="hr"}}selected="selected"{{/is}}>hr</option>
			<option value="hu" {{#is language value="hu"}}selected="selected"{{/is}}>hu</option>
			<option value="hy" {{#is language value="hy"}}selected="selected"{{/is}}>hy</option>
			<option value="id" {{#is language value="id"}}selected="selected"{{/is}}>id</option>
			<option value="is" {{#is language value="is"}}selected="selected"{{/is}}>is</option>
			<option value="it-CH" {{#is language value="it-CH"}}selected="selected"{{/is}}>it-CH</option>
			<option value="it" {{#is language value="it"}}selected="selected"{{/is}}>it</option>
			<option value="ja" {{#is language value="ja"}}selected="selected"{{/is}}>ja</option>
			<option value="ka" {{#is language value="ka"}}selected="selected"{{/is}}>ka</option>
			<option value="kh" {{#is language value="kh"}}selected="selected"{{/is}}>kh</option>
			<option value="kk" {{#is language value="kk"}}selected="selected"{{/is}}>kk</option>
			<option value="kr" {{#is language value="kr"}}selected="selected"{{/is}}>kr</option>
			<option value="lt" {{#is language value="lt"}}selected="selected"{{/is}}>lt</option>
			<option value="lv" {{#is language value="lv"}}selected="selected"{{/is}}>lv</option>
			<option value="mk" {{#is language value="mk"}}selected="selected"{{/is}}>mk</option>
			<option value="ms" {{#is language value="ms"}}selected="selected"{{/is}}>ms</option>
			<option value="nb" {{#is language value="nb"}}selected="selected"{{/is}}>nb</option>
			<option value="nl-BE" {{#is language value="nl-BE"}}selected="selected"{{/is}}>nl-BE</option>
			<option value="nl" {{#is language value="nl"}}selected="selected"{{/is}}>nl</option>
			<option value="no" {{#is language value="no"}}selected="selected"{{/is}}>no</option>
			<option value="pl" {{#is language value="pl"}}selected="selected"{{/is}}>pl</option>
			<option value="pt-BR" {{#is language value="pt-BR"}}selected="selected"{{/is}}>pt-BR</option>
			<option value="pt" {{#is language value="pt"}}selected="selected"{{/is}}>pt</option>
			<option value="ro" {{#is language value="ro"}}selected="selected"{{/is}}>ro</option>
			<option value="rs-latin" {{#is language value="rs-latin"}}selected="selected"{{/is}}>rs-latin</option>
			<option value="rs" {{#is language value="rs"}}selected="selected"{{/is}}>rs</option>
			<option value="ru" {{#is language value="ru"}}selected="selected"{{/is}}>ru</option>
			<option value="sk" {{#is language value="sk"}}selected="selected"{{/is}}>sk</option>
			<option value="sl" {{#is language value="sl"}}selected="selected"{{/is}}>sl</option>
			<option value="sq" {{#is language value="sq"}}selected="selected"{{/is}}>sq</option>
			<option value="sr-latin" {{#is language value="sr-latin"}}selected="selected"{{/is}}>sr-latin</option>
			<option value="sr" {{#is language value="sr"}}selected="selected"{{/is}}>sr</option>
			<option value="sv" {{#is language value="sv"}}selected="selected"{{/is}}>sv</option>
			<option value="sw" {{#is language value="sw"}}selected="selected"{{/is}}>sw</option>
			<option value="th" {{#is language value="th"}}selected="selected"{{/is}}>th</option>
			<option value="tr" {{#is language value="tr"}}selected="selected"{{/is}}>tr</option>
			<option value="uk" {{#is language value="uk"}}selected="selected"{{/is}}>uk</option>
			<option value="vi" {{#is language value="vi"}}selected="selected"{{/is}}>vi</option>
			<option value="zh-CN" {{#is language value="zh-CN"}}selected="selected"{{/is}}>zh-CN</option>
			<option value="zh-TW" {{#is language value="zh-TW"}}selected="selected"{{/is}}>zh-TW</option>

		</select>

		<p class="description" id="{{_id}}_language-description">
            <?php esc_html_e('Language to use. e.g. pt-BR', 'caldera-forms'); ?>
        </p>
	</div>
</div>
