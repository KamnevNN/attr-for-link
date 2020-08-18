<?php
/*
Plugin Name: Attr for link
Plugin URI: 
Description: Add rel="nofollow" and target="_blank"
Author: Kamnev Nikolai
Version: 1.0
Author URI:
License: GPLv2 or later
*/

require_once 'class.attr-for-link-admin.php';
require_once 'class.attr-for-link-fontend.php';

try {
	$AFL_Admin = new AFL_Admin();
	if (!is_admin()) $AFL_Front = new AFL_Front();
} catch (Exception $e) {
	echo $e->getMessage();
}
