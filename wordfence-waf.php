<?php
// Before removing this file, please verify the PHP ini setting `auto_prepend_file` does not point to this.

// This file was the current value of auto_prepend_file during the Wordfence WAF installation (Sun, 28 May 2017 12:09:13 +0000)
if (file_exists('/Applications/MAMP/htdocs/wordfence-waf.php')) {
	include_once '/Applications/MAMP/htdocs/wordfence-waf.php';
}
if (file_exists('/Applications/MAMP/htdocs/wp-content/plugins/wordfence/waf/bootstrap.php')) {
	define("WFWAF_LOG_PATH", '/Applications/MAMP/htdocs/wp-content/wflogs/');
	include_once '/Applications/MAMP/htdocs/wp-content/plugins/wordfence/waf/bootstrap.php';
}
?>