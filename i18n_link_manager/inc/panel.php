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
	<h3 class="floated"><?php i18n('i18n_link_manager/PLUGIN_NAME'); ?></h3>
	<div class="edit-nav clearfix">
		<a href="load.php?id=link_manager&edit"><?php i18n('i18n_link_manager/NEW_LINK'); ?></a>
	</div>
	<?php
	$links = lm_get_links();
	if (empty($links))
		echo '<p>' . i18n_r('i18n_link_manager/NO_LINKS') . '</p>';
	else {
		if (count($links) > 1) echo '<p>' . i18n_r('i18n_link_manager/CHANGE_ORDER') . '</p>'
		?>
		<form method="post">
			<table id="links" class="highlight">
				<tr>
					<th><?php i18n('i18n_link_manager/NAME'); ?></th>
					<th><?php i18n('i18n_link_manager/DESCRIPTION'); ?></th>
					<th style="text-align: right;"><?php i18n('i18n_link_manager/URL'); ?></th>
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
							<a href="load.php?id=link_manager&edit=<?php echo $id; ?>" title="<?php i18n('i18n_link_manager/EDIT_LINK'); ?>: <?php echo $name; ?>">
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
							<a href="<?php echo $url; ?>" target="_blank" title="<?php i18n('i18n_link_manager/VIEW_LINK'); ?>: <?php echo $name; ?>">
								#
							</a>
						</td>
						<td class="delete">
							<a href="load.php?id=link_manager&delete=<?php echo $id; ?>" class="delconfirm" title="<?php i18n('i18n_link_manager/DELETE_LINK'); ?>: <?php echo $name; ?>?">
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
				<input type="submit" class="submit" name="order" value="<?php i18n('i18n_link_manager/SAVE_ORDER'); ?>">
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
