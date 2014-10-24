<?php if (!defined('IN_GS')) {die('you cannot load this page directly.');}

/**
 * General functions used by the GetSimple I18N Link Manager plugin.
 */


/*******************************************************
 * @function lm_save_category
 * @action collect $_POST data (single category) and write to file
 */
function lm_save_category() {
	@copy(LM_CDATA, LM_CBACKUP);
	$id = isset($_POST['category-id']) ? intval($_POST['category-id']) : null;
	$cid = isset($_POST['category-cid']) ? intval($_POST['category-cid']) : time();
	$arr = array('cid'=>$cid, 'name'=>safe_slash_html($_POST['category-name']));
	if (function_exists('return_i18n_languages')) {
		foreach(return_i18n_languages() as $lang) {
			if ($lang != return_i18n_default_language()) {
				$arr['name_'.$lang] = safe_slash_html($_POST['category-name_'.$lang]);
			}
		}
	}
	$categories = lm_get_categories();
	if (isset($id))
		$categories[$id] = $arr;
	else
		$categories[] = $arr;
	if (lm_c_to_xml($categories))
		lm_display_message(i18n_r(LM_PLUGIN.'/SUCCESS_SAVE'), true, false, true);
	else
		lm_display_message(i18n_r(LM_PLUGIN.'/ERROR_SAVE'), false);
}

/*******************************************************
 * @function lm_save_category_order
 * @action collect $_POST data (category order) and write to file
 */
function lm_save_category_order() {
	@copy(LM_CDATA, LM_CBACKUP);
	$categories = array();
	foreach ($_POST as $key=>$val) {
		if ($key != 'corder') {
			$categories[] = unserialize($val);
		}
	}
	if (lm_c_to_xml($categories))
		lm_display_message(i18n_r(LM_PLUGIN,'/SUCCESS_SAVE'), true, false, true);
	else
		lm_display_message(i18n_r(LM_PLUGIN.'/ERROR_SAVE'), false);
}

/*******************************************************
 * @function lm_edit_category
 * @param $cid int unique category identifier
 * @action create a new category or edit an existing one
 */
function lm_edit_category($id=null) {
	if (isset($id)) {
		$categories = lm_get_categories();
		if (array_key_exists($id, $categories)) {
			$category = $categories[$id];
			$cid = $category['cid'];
		}
	}
	include(LM_TEMPLATE_PATH . 'edit_category.php');
}

/*******************************************************
 * @function lm_delete_category
 * @param $cid int unique category identifier
 * @action remove category with given $cid
 */
function lm_delete_category($id) {
	@copy(LM_CDATA, LM_CBACKUP);
	$categories = lm_get_categories();
	if (array_key_exists($id, $categories)) {
		unset($categories[$id]);
		if (lm_c_to_xml($categories))
			lm_display_message(i18n_r(LM_PLUGIN.'/SUCCESS_DELETE'), true, false, true);
		else
			lm_display_message(i18n_r(LM_PLUGIN.'/ERROR_DELETE'), false);
	}
}

/*******************************************************
 * @function lm_get_categories
 * @return array with categories currently in database
 */
function lm_get_categories() {
	$categories = array();
	$data = @getXML(LM_CDATA);
	if (!empty($data)) {
		$languages = array();
		if (function_exists('return_i18n_languages')) {
			$languages = return_i18n_languages();
		}
		foreach ($data->children() as $item) {
			$arr = array('cid'=>intval($item->cid), 'name'=>cl($item->name));
			if (!empty($languages)) {
				foreach ($languages as $lang) {
					if (return_i18n_default_language() != $lang) {
						$name = "name_$lang";
						$arr[$name] = cl($item->$name);
					}
				}
			}
			$categories[] = $arr;
		}
	}
	return $categories;
}

function lm_c_undo() {
	if (copy(LM_CBACKUP, LM_CDATA))
		lm_display_message(i18n_r(LM_PLUGIN.'/SUCCESS_RESTORE'));
	else
		lm_display_message(i18n_r(LM_PLUGIN.'/ERROR_RESTORE'), false);
}

/*******************************************************
 * @function lm_c_to_xml
 * @param $categories array with category data
 * @action write category data to xml file
 * @return bool true when successful, false otherwise
 */
function lm_c_to_xml($categories) {
	debugLog($categories);
	$languages = array();
	if (function_exists('return_i18n_languages')) {
		$languages = return_i18n_languages();
	}
	$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
	foreach ($categories as $id=>$cat) {
		$item = $xml->addChild('item');
		$elem = $item->addChild('cid');
		$elem->addCData($cat['cid']);
		if (empty($languages)) {
			$elem = $item->addChild('name');
			$elem->addCData($cat['name']);
		} else {
			foreach($languages as $lang) {
				$name = (return_i18n_default_language() != $lang) ? $name = "name_$lang" : $name = 'name';
				$elem = $item->addChild($name);
				$elem->addCData($cat[$name]);
			}
		}
	}
	return @XMLsave($xml, LM_CDATA);
}


/*******************************************************
 * @function lm_get_links
 * @param $count int count of links, or null for all of them
 * @return array with links currently in database
 */
function lm_get_links($count=null) {
	$name = 'name';
	$desc = 'description';
	$links = array();
	$data = @getXML(LM_DATA);
	if (!empty($data)) {
		if (function_exists('return_i18n_languages'))
			$languages = return_i18n_languages();
		$categories = lm_get_categories();
		$i = 0;
		foreach ($data->children() as $item) {
			$id = intval($item->id);
			$cid = intval($item->cid);
			$arr = array('cid'=>$cid, 'url'=>strval($item->url), $name=>cl($item->$name), $desc=>cl($item->$desc));
			if (function_exists('return_i18n_languages')) {
				foreach ($languages as $lang) {
					if ($lang != return_i18n_default_language()) {
						$n       = $name.'_'.$lang;
						$d       = $desc.'_'.$lang;
						$arr[$n] = cl($item->$n);
						$arr[$d] = cl($item->$d);
					}
				}
			}
			$links[$id] = $arr;
			$i++;
			if ($count !== null && $i == $count) break;
		}
	}
	return $links;
}


/*******************************************************
 * @function lm_edit_link
 * @param $id int unique link identifier
 * @action create a new link or edit an existing one
 */
function lm_edit_link($id=null) {
	if (isset($id)) {
		$links = lm_get_links();
		$categories = lm_get_categories();
		debugLog($categories);
		if (function_exists('return_i18n_languages'))
			$languages = return_i18n_languages();
		if (array_key_exists($id, $links))
			$link = $links[$id];
	}
	include(LM_TEMPLATE_PATH . 'edit_link.php');
}


/*******************************************************
 * @function lm_save_link
 * @action collect $_POST data (single link) and write to file
 */
function lm_save_link() {
	@copy(LM_DATA, LM_BACKUP);
	$id = isset($_POST['link-id']) ? intval($_POST['link-id']) : null;
	$arr = array('cid'=>intval($_POST['link-cid']), 'url'=>$_POST['link-url']);
	$arr['name'] = safe_slash_html($_POST['link-name']);
	$arr['description'] = safe_slash_html($_POST['link-description']);
	if (function_exists('return_i18n_languages')) {
		foreach(return_i18n_languages() as $lang) {
			if ($lang != return_i18n_default_language()) {
				$arr['name_'.$lang] = safe_slash_html($_POST['link-name_'.$lang]);
				$arr['description_'.$lang] = safe_slash_html($_POST['link-description_'.$lang]);
			}
		}
	}
	$links = lm_get_links();
	if (isset($id))
		$links[$id] = $arr;
	else
		$links[] = $arr;
	if (lm_to_xml($links))
		lm_display_message(i18n_r(LM_PLUGIN.'/SUCCESS_SAVE'), true, true);
	else
		lm_display_message(i18n_r(LM_PLUGIN.'/ERROR_SAVE'), false);
}


/*******************************************************
 * @function lm_delete_link
 * @param $id int unique link identifier
 * @action remove link with given $id
 */
function lm_delete_link($id) {
	@copy(LM_DATA, LM_BACKUP);
	$links = lm_get_links();
	if (array_key_exists($id, $links)) {
		unset($links[$id]);
		if (lm_to_xml($links))
			lm_display_message(i18n_r(LM_PLUGIN.'/SUCCESS_DELETE'), true, true);
		else
			lm_display_message(i18n_r(LM_PLUGIN.'/ERROR_DELETE'), false);
	}
}


/*******************************************************
 * @function lm_save_order
 * @action collect $_POST data (link order) and write to file
 */
function lm_save_order() {
	@copy(LM_DATA, LM_BACKUP);
	$links = array();
	foreach ($_POST as $key=>$val) {
		if ($key != 'order') {
			$links[] = unserialize($val);
		}
	}
	if (lm_to_xml($links))
		lm_display_message(i18n_r(LM_PLUGIN,'/SUCCESS_SAVE'), true, true);
	else
		lm_display_message(i18n_r(LM_PLUGIN.'/ERROR_SAVE'), false);
}


function lm_undo() {
	if (copy(LM_BACKUP, LM_DATA))
		lm_display_message(i18n_r(LM_PLUGIN.'/SUCCESS_RESTORE'));
	else
		lm_display_message(i18n_r(LM_PLUGIN.'/ERROR_RESTORE'), false);
}

/*******************************************************
 * @function lm_to_html
 * @action convert link data to html list
 * @return string links in html format
 */
function lm_to_html() {
	global $language, $cats;
	$cats = lm_get_categories();

	function catIdx($cid) {
		global $cats;
		foreach($cats as $key=>&$val) {
			if ($val['cid'] == $cid) return $key;
		}
		return 0;
	}

	function linkCmp(&$a, &$b) {
		global $cats;
		if ($a['cid'] == $b['cid'])
			return 0;
		return catIdx($a['cid']) > catIdx($b['cid']) ? 1 : -1;
	}

	$html = '';
	$links = lm_get_links();
	usort($links, 'linkCmp');
	$cid = null;
	if (function_exists('return_i18n_default_language') && !empty($language) && $language !=  return_i18n_default_language()) {
		$n = "name_$language";
		$d = "description_$language";
	} else {
		$n = "name";
		$d = "description";
	}
	if (!empty($links)) {
		foreach ($links as $id=>$link) {
			$c = catIdx($link['cid']);
			if ($cid !== $c) {
				$cid = $c;
				$html .= "</ul><b>" . (empty($cats[$cid][$n]) ? $cats[$cid]['name'] : $cats[$cid][$n]) . ":</b><ul>";
			}
			$html .= "<li><a href=\"${link['url']}\" target=\"_blank\" rel=\"nofollow\">" . (empty($link[$n]) ? $link['name'] : $link[$n]) ."</a>";
			if (!empty($link[$d]) || !empty($link['description'])) {
				$html .= ' &nbsp;&mdash;&nbsp; ' . (empty($link[$d]) ? $link['description'] : $link[$d]);
			}
			$html .= '</li>';
		}
		$html = '<ul>' . $html . '</ul>';
	}
	return $html;
}


/*******************************************************
 * @function lm_to_xml
 * @param $links array with link data
 * @action write link data to xml file
 * @return bool true when successful, false otherwise
 */
function lm_to_xml($links) {
	debugLog($links);
	$name = 'name';
	$desc = 'description';
	$languages = array();
	if (function_exists('return_i18n_languages')) {
		$languages = return_i18n_languages();
	}
	$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
	foreach ($links as $id=>$link) {
		$item = $xml->addChild('item');
		$elem = $item->addChild('id');
		$elem->addCData($id);
		$elem = $item->addChild('cid');
		$elem->addCData($link['cid']);
		$elem = $item->addChild('url');
		$elem->addCData($link['url']);
		if (empty($languages)) {
			$elem = $item->addChild($name);
			$elem->addCData($link[$name]);
			$elem = $item->addChild($desc);
			$elem->addCData($link[$desc]);
		} else {
			foreach($languages as $lang) {
				if ($lang != return_i18n_default_language()) {
					$n = $name.'_'.$lang;
					$d = $desc.'_'.$lang;
				} else {
					$n = $name;
					$d = $desc;
				}
				$elem = $item->addChild($n);
				$elem->addCData($link[$n]);
				$elem = $item->addChild($d);
				$elem->addCData($link[$d]);
			}
		}
	}
	return @XMLsave($xml, LM_DATA);
}



/*******************************************************
 * @function lm_display_message
 * @param $msg string containing the message
 * @param $update bool if true, show as $msg as update, else as error
 * @param $undo bool if true, provide undo link
 * @param $cundo bool if true, provide undo link for category
 * @action display status messages on back-end pages
 */
function lm_display_message($msg, $update=true, $undo=false, $cundo=false) {
	if (isset($msg)) {
		if ($undo)
			$msg .= " <a href=\"load.php?id=".LM_PLUGIN."&undo\">" . i18n_r(LM_PLUGIN.'/UNDO') . '</a>';
		if ($cundo)
			$msg .= " <a href=\"load.php?id=".LM_PLUGIN."&cundo\">" . i18n_r(LM_PLUGIN,'/UNDO') . '</a>';
		?>
		<script type="text/javascript">
			$(function() {
				$('div.bodycontent').before('<div class="<?php echo $update ? 'updated' : 'error'; ?>" style="display:block;">'+
				<?php echo json_encode($msg); ?>+'</div>');
				$(".updated, .error").fadeOut(500).fadeIn(500);
			});
		</script>
	<?php
	}
}


/*******************************************************
 * @function lm_header_include
 * @action insert necessary script/style sections into site header
 */
function lm_header_include() {
	?>
	<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.8/jquery.validate.min.js"></script>
	<script type="text/javascript" src="../plugins/link_manager/template/js/jquery-ui.sort.min.js"></script>
	<style>
		.invalid {
			color: #D94136;
			font-size: 11px;
			font-weight: normal;
		}
	</style>
<?php
}


?>
