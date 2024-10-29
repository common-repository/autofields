<?php
if(isset($_REQUEST['submit']) and $_REQUEST['submit']) {
	$options = array('insert_excerpt', 'insert_image_custom_field', 'check_excerpt', 'check_image_custom_field');
	foreach($options as $opt) {
		if(isset($_POST[$opt])) update_option('autofields_' . $opt, 1);
		else update_option('autofields_' . $opt, 0);
	}
	print '<div id="message" class="updated fade"><p>Options updated.</p></div>';
}
?>
<div class="wrap">
<h2>Autofields</h2>

<form action="" method="post">

<h3><?php _e('Auto Insertion') ?></h3>

<label for="insert_excerpt"><?php _e('Auto Insert Excerpt') ?></label>
<input type="checkbox" name="insert_excerpt" value="1" id="insert_excerpt" <?php if(get_option('autofields_insert_excerpt')) print " checked='checked'"; ?> /><br />

<label for="insert_image_custom_field"><?php _e('Auto Insert Image Custom Field') ?></label>
<input type="checkbox" name="insert_image_custom_field" value="1" id="insert_image_custom_field" <?php if(get_option('autofields_insert_image_custom_field')) print " checked='checked'"; ?> /><br />

<h3><?php _e('Custom Validation') ?></h3>

<label for="check_excerpt"><?php _e('Check for Excerpt before publishing') ?></label>
<input type="checkbox" name="check_excerpt" value="1" id="check_excerpt" <?php if(get_option('autofields_check_excerpt')) print " checked='checked'"; ?> /><br />

<label for="check_image_custom_field"><?php _e('Check for Image Custom Field before publishing') ?></label>
<input type="checkbox" name="check_image_custom_field" value="1" id="check_image_custom_field" <?php if(get_option('autofields_check_image_custom_field')) print " checked='checked'"; ?> /><br />

<p class="submit">
<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) $user_ID ?>" />
<span id="autosave"></span>
<input type="submit" name="submit" value="<?php _e('Save Options') ?>" style="font-weight: bold;" />
</p>

</form>

</div>
