<?php

/**
 * I18N Link Manager edit template
 */

if (isset($category))
	echo '<h3>' . i18n_r(LM_PLUGIN.'/EDIT_CATEGORY') . '</h3>';
else
	echo '<h3>' . i18n_r(LM_PLUGIN.'/NEW_CATEGORY') . '</h3>';

?>
<div class="edit-nav clearfix">
	<a href="load.php?id=<?php echo LM_PLUGIN; ?>"><?php i18n(LM_PLUGIN.'/LINKS'); ?></a>
</div>

<form class="largeform" id="edit" action="load.php?id=<?php echo LM_PLUGIN; ?>" method="post" accept-charset="utf-8">
	<?php
	if (isset($category)) {
		echo "<p><input name=\"category-id\" type=\"hidden\" value=\"$id\" /></p>";
		echo "<p><input name=\"category-cid\" type=\"hidden\" value=\"${category['cid']}\" /></p>";
	}
	?>
	<p>
		<label for="category-name"><?php i18n(LM_PLUGIN.'/NAME'); ?>:</label>
		<input class="text required" name="category-name" id="category-name" type="text" value="<?php if (isset($category)) echo $category['name']; ?>" />
		<?php
		if (function_exists('return_i18n_languages')) {
			foreach(return_i18n_languages() as $lang) {
				if ($lang != return_i18n_default_language()) {
					?>
					<label for="<?php echo 'category-name_'.$lang; ?>"><?php echo i18n_r(LM_PLUGIN.'/NAME')." ($lang)"; ?>:</label>
					<input class="text required" name="<?php echo 'category-name_'.$lang; ?>" id="<?php echo 'category-name_'.$lang; ?>" type="text" value="<?php if (isset($category)) echo $category['name_'.$lang]; ?>" />
					<?php
				}
			}
		} ?>
	</p>
	<p>
		<input class="submit" type="submit" name="category" value="<?php i18n(LM_PLUGIN.'/SAVE_CATEGORY'); ?>" />
		&nbsp;&nbsp;<?php i18n(LM_PLUGIN.'/OR'); ?>&nbsp;&nbsp;
		<a href="load.php?id=<?php echo LM_PLUGIN; ?>&cancel" class="cancel"><?php i18n(LM_PLUGIN.'/CANCEL'); ?></a>
		<?php
		if (isset($category)) {
			?>
			/
			<a href="load.php?id=<?php echo LM_PLUGIN; ?>&cdelete=<?php echo $cid; ?>" class="cancel">
				<?php i18n(LM_PLUGIN.'/DELETE'); ?>
			</a>
		<?php
		}
		?>
	</p>
</form>

<script>
	$(document).ready(function(){
		$("#edit").validate({
			errorClass: "invalid"
		})
		$("#category-name").focus();
	});
</script>
