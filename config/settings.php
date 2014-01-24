<?php
/**
 * Default settings
 */
// constants
define(APP_VERSION, 'v1.00.20131128'); // application version. See README.txt
define(APP_NAME, 'Kateglo ~ Kamus, tesaurus, dan glosarium bahasa Indonesia'); // application name
define(APP_SHORT, 'Kateglo'); // application name
define(LF, "\n"); // line break
define(KTG_TIMEOUT, 15); // timeout, used for curl

// cleanup get
foreach ($_GET as $key => $val) $_GET[$key] = trim($val);
?>