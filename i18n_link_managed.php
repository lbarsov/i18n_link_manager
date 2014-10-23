<?php

/*
Plugin Name: Link Manager (updated)
Description: Manage a collection of links (I18N enabled) based on code of Rogier Koppejan
Version: 2.0
Author: Lev Barsov, Rog<?php

/*
Plugin Name: I18N Link Manager
Description: Manage a collection of links (I18N enabled) based on code of Rogier Koppejan
Version: 2.1
Author: Lev Barsov, Rogier Koppejan
Author URI: http://rxgr.nl/getsimple/
*/


# get correct id for plugin
$thisfile = basename(__FILE__, '.php');

# definitions
define('LM_PLUGIN', $thisfile);
define('LM_NAME', 'I18N Link Manager');
define('LM_VERSION', '2.1');
define('LM_DATA', GSDATAOTHERPATH . 'links.xml');
define('LM_BACKUP', GSBACKUPSPATH . 'other/links.xml');
define('LM_INC_PATH', GSPLUGINPATH . LM_PLUGIN.'/inc/');
define('LM_TEMPLATE_PATH', GSPLUGINPATH . LM_PLUGIN.'/template/');


# register plugin
register_plugin(
	LM_PLUGIN,
	LM_NAME,
	LM_VERSION,
	'Lev Barsov, Rogier Koppejan',
	'https://github.com/lbarsov/',
	'Manage a collection of links (I18N enabled) based on code of Rogier Koppejan',
	'plugins',
	'lm_main'
);


# hooks
add_action('common', 'lm_load');
add_action('index-pretemplate', 'lm_init');
add_action('plugins-sidebar', 'createSideMenu', array(LM_PLUGIN, LM_NAME));
add_action('header', 'lm_header_include');
add_filter('content', 'lm_filter');

# language

# includes
require_once(LM_INC_PATH . 'functions.php');
require_once(LM_INC_PATH . 'panel.php');


function lm_init() {
	if (function_exists('i18n_init')) {
		i18n_init();
	}
}

function lm_load() {
	global $language, $LANG;
	if (function_exists('i18n_load_texts')) {
		i18n_load_texts(LM_PLUGIN);
	} else {
		i18n_merge(LM_PLUGIN, $language) || i18n_merge(LM_PLUGIN, substr($LANG, 0, 2)) || i18n_merge(LM_PLUGIN, 'en');
	}
}

/*******************************************************
 * @function lm_main
 * @action back-end main function; select action to take
 */
function lm_main() {
	if (isset($_POST['link'])) {
		lm_save_link();
		lm_admin_panel();
	} elseif (isset($_POST['order'])) {
		lm_save_order();
		lm_admin_panel();
	} elseif (isset($_GET['delete'])) {
		lm_delete_link($_GET['delete']);
		lm_admin_panel();
	} elseif (isset($_GET['edit'])) {
		lm_edit_link($_GET['edit']);
	} elseif (isset($_GET['undo'])) {
		lm_undo();
		lm_admin_panel();
	} else {
		lm_admin_panel();
	}
}


/*******************************************************
 * @function lm_filter
 * @action front-end function; insert links into content
 */
function lm_filter($content) {
	if (preg_match('/\(%\s*links\s*%\)/', $content)) {
		$html = lm_to_html();
		$content = preg_replace('/\(%\s*links\s*%\)/', $html, $content);
	}
	return $content;
}


/*******************************************************
 * @function lm_list_links
 * @action front-end function; add links to template
 */
function lm_list_links() {
	$html = lm_to_html();
	if (!empty($html))
		echo $html;
}


?>
ier Koppejan
Author URI: http://rxgr.nl/getsimple/
*/


# get correct id for plugin
$thisfile = basename(__FILE__, '.php');

# register plugin
register_plugin(
	$thisfile,
	'Link Manager (updated)',
	'2.0',
	'Lev Barsov, Rogier Koppejan',
	'https://github.com/lbarsov/',
	'Manage a collection of links (I18N enabled) based on code of Rogier Koppejan',
	'plugins',
	'lm_main'
);


# hooks
add_action('common', 'lm_load');
add_action('index-pretemplate', 'lm_init');
add_action('plugins-sidebar', 'createSideMenu', array($thisfile, 'Link Manager'));
add_action('header', 'lm_header_include');
add_filter('content', 'lm_filter');

# language

# definitions
define('LM_DATA', GSDATAOTHERPATH . 'links.xml');
define('LM_BACKUP', GSBACKUPSPATH . 'other/links.xml');
define('LM_INC_PATH', GSPLUGINPATH . 'i18n_link_manager/inc/');
define('LM_TEMPLATE_PATH', GSPLUGINPATH . 'i18n_link_manager/template/');

# includes
require_once(LM_INC_PATH . 'functions.php');
require_once(LM_INC_PATH . 'panel.php');


function lm_init() {
	if (function_exists('i18n_init')) {
		i18n_init();
	}
}

function lm_load() {
	global $language, $LANG;
	if (function_exists('i18n_load_texts')) {
		i18n_load_texts('i18n_link_manager');
	} else {
		i18n_merge('i18n_link_manager', $language) || i18n_merge('i18n_link_manager', substr($LANG, 0, 2)) || i18n_merge('i18n_link_manager', 'en');
	}
}

/*******************************************************
 * @function lm_main
 * @action back-end main function; select action to take
 */
function lm_main() {
	if (isset($_POST['link'])) {
		lm_save_link();
		lm_admin_panel();
	} elseif (isset($_POST['order'])) {
		lm_save_order();
		lm_admin_panel();
	} elseif (isset($_GET['delete'])) {
		lm_delete_link($_GET['delete']);
		lm_admin_panel();
	} elseif (isset($_GET['edit'])) {
		lm_edit_link($_GET['edit']);
	} elseif (isset($_GET['undo'])) {
		lm_undo();
		lm_admin_panel();
	} else {
		lm_admin_panel();
	}
}


/*******************************************************
 * @function lm_filter
 * @action front-end function; insert links into content
 */
function lm_filter($content) {
	if (preg_match('/\(%\s*links\s*%\)/', $content)) {
		$html = lm_to_html();
		$content = preg_replace('/\(%\s*links\s*%\)/', $html, $content);
	}
	return $content;
}


/*******************************************************
 * @function lm_list_links
 * @action front-end function; add links to template
 */
function lm_list_links() {
	$html = lm_to_html();
	if (!empty($html))
		echo $html;
}


?>
