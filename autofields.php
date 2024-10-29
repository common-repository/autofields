<?php
/*
Plugin Name: Autofields
Plugin URI: http://www.bin-co.com/tools/wordpress/plugins/autofields/
Description: AutoFields will auto fill the Excerpt and add an Image custom field based on the data you entered into the contents editor.
Version: 1.01.1
Author: Binny V A
Author URI: http://www.binnyva.com/
*/

/**
 * Add an option page for Autofields.
 */
add_action('admin_menu', 'autofields_option_page');
function autofields_option_page() {
	add_options_page('Autofields', 'Autofields', 8, basename(__FILE__), 'autofields_options');
}
function autofields_options() {
	if ( function_exists('current_user_can') && !current_user_can('manage_options') ) die(__('Cheatin&#8217; uh?'));
	if (! user_can_access_admin_page()) wp_die( __('You do not have sufficient permissions to access this page.') );

	include(ABSPATH. 'wp-content/plugins/autofields/options.php');
}


/**
 * Add the auto fill action in the post editing/adding page
 */
add_action( 'edit_form_advanced',	'autofields_insert_script' );
function autofields_insert_script() {
?>
<script type="text/javascript">
function autofields_init() {
	//Set up the event handlers.
	jQuery("#newtag").focus(autofields_getContents); // For WP 2.7
	jQuery("#new-tag-post_tag").focus(autofields_getContents); // WP 2.8
	jQuery("#excerpt").focus(autofields_getContents);
	
	jQuery("#contents").blur(autofields_getContents);
	
	// If the user hits publish, check for excerpt and Image.
	jQuery("#publish").click(autofields_check);
}

function autofields_getContents() {
	var contents;
	if(window.tinyMCE && document.getElementById("content").style.display=="none") { // If visual mode is activated.
		contents = tinyMCE.get("content").getContent();
	} else {
		contents = document.getElementById("content").value;
	}

	if(!contents) return;
	
	<?php if(get_option('autofields_insert_excerpt')) { ?>
	if(!document.getElementById("excerpt").value) {
		// Get the first paragraph
		var start_index = contents.search(/<p[^>]*>/i); //Get the first <p> tag.
		if(start_index != -1) {
			var end_index	= contents.slice(start_index).search(/<\/p>/i); // Find the ending tag.
		} else {
			start_index = 0;
			var end_index = 200;
		}


		var para = contents.slice(start_index, start_index + end_index).replace(/<p[^>]*>/i, ""); //Get the text between that - and Remove the first <p> tag.
		if(para) {
			document.getElementById("excerpt").value = para;
		}

	}
	<?php } ?>
	
	<?php if(get_option('autofields_insert_image_custom_field')) { ?>
	found_image_custom_field = false;
	jQuery("input[name*=meta]").each(function() {
		if(this.name.match(/meta\[\d+\]\[key\]/)) {
			if(this.value == "Image") found_image_custom_field = true;
		}
	});
	
	if(found_image_custom_field) return;

	var image = contents.match(/<img[^>]+src=['"]?([^'" >]+)['"]?/i);
	if(image) {
		document.getElementById("metakeyinput").value = "Image";
		document.getElementById("metavalue").value = image[1];
		document.getElementById("newmeta-submit").click(); // Add the custom field. :TODO: Option for the user to decide where the inserted image should be auto added
	}
	<?php } ?>
}

function autofields_check(e) {
	var to_enter = [];
	<?php if(get_option('autofields_check_excerpt')) { ?>
	if(!document.getElementById("excerpt").value) to_enter.push("<?php _e('the excerpt field'); ?>");
	<?php } ?>
	
	<?php if(get_option('autofields_check_image_custom_field')) { ?>
	var found_image_custom_field = false;
	jQuery("input[name*=meta]").each(function() {
		if(this.name.match(/meta\[\d+\]\[key\]/)) {
			if(this.value == "Image") found_image_custom_field = true;
		}
	});
	if(!found_image_custom_field) to_enter.push("<?php _e("the 'Image' custom field"); ?>");
	<?php } ?>
	
	if(to_enter.length) {
		if(!confirm("<?php _e('You have not entered anything in '); ?>" + to_enter.join("<?php _e(' and ') ?>") + "<?php _e('. Are you sure you want to continue?'); ?>")) {
			e.preventDefault();
			e.stopPropagation();
			return false;
		}
	}
}

jQuery(document).ready(autofields_init);
</script>
<?php
}

/**
 * Stuff to do when the plugin is activated - set default options, stuff like that.
 */
add_action('activate_autofields/autofields.php','autofields_activate');
function autofields_activate() {
	add_option('autofields_insert_excerpt', 1);
	add_option('autofields_insert_image_custom_field', 1);
	add_option('autofields_check_excerpt', 1);
	add_option('autofields_check_image_custom_field', 1);
}
