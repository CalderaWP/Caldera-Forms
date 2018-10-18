<?php

$deprecated = Caldera_Forms_Admin_PHP::is_version_deprecated(PHP_VERSION);
if (!$deprecated) {
    echo '<div id="caldera-forms-admin-client"></div>';
}
