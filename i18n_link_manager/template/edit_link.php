<?php

/**
 * Link Manager edit template
 */

if (isset($link))
	echo '<h3>' . i18n_r(LM_PLUGIN.'/EDIT_LINK') . '</h3>';
else
	echo '<h3>' . i18n_r(LM_PLUGIN.'/NEW_LINK') . '</h3>';

?>

<form class="largeform" id="edit" action="load.php?id=<?php echo LM_PLUGIN; ?>" method="post" accept-charset="utf-8">
	<?php
	if (isset($link))
		echo "<p><input name=\"link-id\" type=\"hidden\" value=\"$id\" /></p>";
	?>
	<p>
		<label for="link-url"><?php i18n(LM_PLUGIN.'/URL'); ?>:</label>
		<input class="text url required" name="link-url" id="link-url" type="text" value="<?php echo isset($link) ? $link['url'] : 'http://'; ?>" />
	</p>
	<p>
		<label for="link-name"><?php i18n(LM_PLUGIN.'r/NAME'); ?>:</label>
		<input class="text required" name="link-name" id="link-name" type="text" value="<?php if (isset($link)) echo $link['name']; ?>" />
		<?php
		if (function_exists('return_i18n_languages')) {
			foreach(return_i18n_languages() as $lang) {
				if ($lang != return_i18n_default_language()) {
					?>
					<label for="<?php echo 'link-name_'.$lang; ?>"><?php echo i18n_r(LM_PLUGIN.'/NAME')." ($lang)"; ?>:</label>
					<input class="text required" name="<?php echo 'link-name_'.$lang; ?>" id="<?php echo 'link-name_'.$lang; ?>" type="text" value="<?php if (isset($link)) echo $link['name_'.$lang]; ?>" />
					<?php
				}
			}
		} ?>
	</p>
	<p>
		<label for="link-description"><?php i18n(LM_PLUGIN.'/DESCRIPTION'); ?>:</label>
		<input class="text" name="link-description" id="link-description" type="text" value="<?php if (isset($link)) echo $link['description']; ?>" />
		<?php
		if (function_exists('return_i18n_languages')) {
			foreach(return_i18n_languages() as $lang) {
				if ($lang != return_i18n_default_language()) {
					?>
					<label for="<?php echo 'link-description_'.$lang; ?>"><?php echo i18n_r(LM_PLUGIN.'/DESCRIPTION')." ($lang)"; ?>:</label>
					<input class="text" name="<?php echo 'link-description_'.$lang; ?>" id="<?php echo 'link-description_'.$lang; ?>" type="text" value="<?php if (isset($link)) echo $link['description_'.$lang]; ?>" />
				<?php
				}
			}
		} ?>
	</p>
	<p>
		<input class="submit" type="submit" name="link" value="<?php i18n(LM_PLUGIN.'/SAVE_LINK'); ?>" />
		&nbsp;&nbsp;<?php i18n(LM_PLUGIN.'/OR'); ?>&nbsp;&nbsp;
		<a href="load.php?id=<?php echo LM_PLUGIN; ?>&cancel" class="cancel"><?php i18n(LM_PLUGIN.'/CANCEL'); ?></a>
		<?php
		if (isset($link)) {
			?>
			/
			<a href="load.php?id=<?php echo LM_PLUGIN; ?>&delete=<?php echo $id; ?>" class="cancel">
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
		$("#link-name").focus();
	});
</script>
