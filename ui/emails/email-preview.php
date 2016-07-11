<?php
/**
 * View for email preview
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */

if( ! defined( 'ABSPATH' ) || ! isset( $message, $headers ) ){
	exit;
}
status_header( 200 );
header( "Content-Type: text/html" );
header( "Pragma: public" );
header( "Expires: 0" );
header( "Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0" );

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php esc_html_e( 'Caldera Forms Email Preview', 'caldera-forms' ); ?></title>
	<style>
		body {
			background-color: #DFDFDF;
		}
		#wrap {
			margin-top: 20%;
			position: relative;

		}
		#inner {
			width: 80%;
			padding: 24px;
			background-color: #fff;
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
		}
		#headers {
			border-bottom: 1px solid #DFDFDF;
		}

		pre {
			display: inline;
		}
	</style>
</head>
<body>

<div id="wrap">
	<div id="inner">
		<div id="headers" class="item">
			<?php echo $headers; ?>
		</div>
		<div id="message">
			<?php echo $message; ?>
		</div>
	</div>
</div>

</body>
</html>
