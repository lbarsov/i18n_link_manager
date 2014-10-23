<?php if (!defined('IN_GS')) {die('you cannot load this page directly.');}

/**
 * Link Manager admin panel.
 */


/*******************************************************
 * @function lm_admin_panel
 * @action give an overview of all links in the database
 */
function lm_admin_panel() {
	?>
	<h3 class="floated"><?php i18n(LM_PLUGIN.'/PLUGIN_NAME'); ?></h3>
	<div class="edit-nav clearfix">
		<a href="load.php?id=<?php echo LM_PLUGIN; ?>&edit"><?php i18n(LM_PLUGIN.'/NEW_LINK'); ?></a>
	</div>
	<?php
	$links = lm_get_links();
	if (empty($links))
		echo '<p>'.i18n_r(LM_PLUGIN.'/NO_LINKS').'</p>';
	else {
		if (count($links) > 1) echo '<p>' . i18n_r(LM_PLUGIN.'/CHANGE_ORDER') . '</p>'
		?>
		<form method="post" action="load.php?id=<?php echo LM_PLUGIN; ?>">
			<table id="links" class="highlight">
				<tr>
					<th><?php i18n(LM_PLUGIN.'/NAME'); ?></th>
					<th><?php i18n(LM_PLUGIN.'/DESCRIPTION'); ?></th>
					<th style="text-align: right;"><?php i18n(LM_PLUGIN.'/URL'); ?></th>
					<th></th>
					<th></th>
				</tr>
				<tbody>
				<?php
				foreach ($links as $id=>$link) {
					$url = $link['url'];
					$name = $link['name'];
					$desc = $link['description'];
					?>
					<tr>
						<input type="hidden" name="<?php echo $name; ?>" value="<?php echo $url; ?>">
						<td>
							<a href="load.php?id=<?php echo LM_PLUGIN; ?>&edit=<?php echo $id; ?>" title="<?php i18n(LM_PLUGIN.'/EDIT_LINK'); ?>: <?php echo $name; ?>">
								<?php echo $name; ?>
							</a>
						</td>
						<td class="description">
							<span><?php echo $desc; ?></span>
						</td>
						<td style="text-align: right;">
							<span><?php echo strlen($url) > 50 ? substr($url, 0, 50) . '&hellip;' : $url; ?></span>
						</td>
						<td class="secondarylink">
							<a href="<?php echo $url; ?>" target="_blank" title="<?php i18n(LM_PLUGIN.'/VIEW_LINK'); ?>: <?php echo $name; ?>">
								#
							</a>
						</td>
						<td class="delete">
							<a href="load.php?id=<?php echo LM_PLUGIN; ?>&delete=<?php echo $id; ?>" class="delconfirm" title="<?php i18n(LM_PLUGIN.'/DELETE_LINK'); ?>: <?php echo $name; ?>?">
								X
							</a>
						</td>
					</tr>
				<?php
				}
				?>
				</tbody>
			</table>
			<?php if (count($links) > 1) { ?>
				<input type="submit" class="submit" name="order" value="<?php i18n(LM_PLUGIN.'/SAVE_ORDER'); ?>">
			<?php } ?>
		</form>

		<script language="javascript">
			$(document).ready(function(){
				$('#links tbody').sortable();
			});
		</script>
	<?php
	}
}


?>
