<?php if (!defined('IN_GS')) {die('you cannot load this page directly.');}

/**
 * General functions used by the GetSimple Link Manager Plugin.
 */


/*******************************************************
 * @function lm_get_links
 * @return array with links currently in database
 */
function lm_get_links() {
	$name = 'name';
	$desc = 'description';
	$links = array();
	$data = @getXML(LM_DATA);
	if (!empty($data)) {
		foreach ($data->children() as $item) {
			$id = intval($item->id);
			$arr = array('url'=>strval($item->url), $name=>cl($item->$name), $desc=>cl($item->$desc));
			if (function_exists('return_i18n_languages')) {
				foreach (return_i18n_languages() as $lang) {
					if ($lang != return_i18n_default_language()) {
						$n       = $name . '_' . $lang;
						$d       = $desc . '_' . $lang;
						$arr[$n] = cl($item->$n);
						$arr[$d] = cl($item->$d);
					}
				}
			}
			$links[$id] = $arr;
		}
	}
	return $links;
}


/*******************************************************
 * @function lm_edit_link
 * @param $id unique link identifier
 * @action create a new link or edit an existing one
 */
function lm_edit_link($id=null) {
	if (isset($id)) {
		$links = lm_get_links();
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
	$arr = array('url'=>$_POST['link-url']);
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
 * @param $id unique link identifier
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
	foreach ($_POST as $link) {
		if ($link->name != 'order') {
			$link->name = str_replace('_', ' ', $link->name);
			$links[] = $link;
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
 * @return links in html format
 */
function lm_to_html() {
	global $language;
	$html = '';
	$links = lm_get_links();
	$name = 'name';
	$desc = 'description';
	if (function_exists('return_i18n_default_language') && !empty($language) && $language !=  return_i18n_default_language()) {
		$name .= '_'.$language;
		$desc .= '_'.$language;
	}
	if (!empty($links)) {
		foreach ($links as $id=>$link)
			$html .= '<li><a href="' . $link['url'] . '" target="_blank" rel="nofollow">' . $link[$name] . '</a> &nbsp;&mdash;&nbsp; ' . $link[$desc] . '</li>';
		$html = '<ul>' . $html . '</ul>';
	}
	return $html;
}


/*******************************************************
 * @function lm_to_xml
 * @param $links array with link data
 * @action write link data to xml file
 * @return true when successful, false otherwise
 */
function lm_to_xml($links) {
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
 * @param $msg a string containing the message
 * @param $update if true, show as $msg as update, else as error
 * @param $undo if true, provide undo link
 * @action display status messages on back-end pages
 */
function lm_display_message($msg, $update=true, $undo=false) {
	if (isset($msg)) {
		if ($undo)
			$msg .= " <a href=\"load.php?id=".LM_PLUGIN."&undo\">" . i18n_r('UNDO') . '</a>';
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
