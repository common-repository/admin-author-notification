<?php /*
Plugin Name: Admin and author notification
Plugin URI: http://www.duskwebdesign.co.uk
Description: Displays a Javascript alert to blog post authors stating that an Admin will need to approve their post. Also sends an email to Admin informing them there's a new post to authorise.
Author: Kev Leitch
Version: 0.1
Author URI: http://www.duskwebdesign.co.uk
License: GPLv2

/*  Copyright 2011  Kev Leitch  (email : kev@duskwebdesign.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
########################### 1: Installation (Uninstall in seperate file) ###############################
*/

register_activation_hook(__FILE__, 'aan_kl_install');

function aan_kl_install() {
	$options = array(
		'text_string' => get_option('admin_email'),
		'text_string2' => 'New blog entry!',
		'text_string3' => 'A blog author has submitted a new entry. You need to authorise this entry before it will go live on the site.',
		'text_string4' => 'Thank you for your entry. An Administrator will now need to approve it before it can appear live on the site.'
	);
	update_option('aan_kl_options', $options);
}

/*
########################### 2: Settings page - interface ###############################
*/

function aan_kl_create_settings_page () {
	add_options_page( 'Admin and Author Notification Settings', 'Admin and Author Notification Settings', 'manage_options', __FILE__, 'aan_kl_setup_settings_page' );
}

function aan_kl_setup_settings_page () {
	?>
    	<div class="wrap">
        	<?php screen_icon(); ?>
        	<h2>Admin and Author Notification Settings</h2>
            <form method="post" action="options.php">
            	<?php settings_fields('aan_kl_options'); ?>
				<?php do_settings_sections('aan_kl'); ?>
            	<br />
            	<input name="Submit" type="submit" value="Save Changes" class="button-primary" />
            </form>
        </div>
    <?php
}

/*
########################### 3: More settings page - getting values ###############################
*/

add_action('admin_init', 'aan_kl_admin_init');
function aan_kl_admin_init(){
	register_setting(
		'aan_kl_options',
		'aan_kl_options',
		''
	);
	add_settings_section(
		'aan_kl_main',
		'',
		'aan_kl_section_text',
		'aan_kl'
	);
	add_settings_field(
		'aan_kl_text_string',
		'Enter email address here',
		'aan_kl_setting_input',
		'aan_kl',
		'aan_kl_main'
	);
	add_settings_field(
		'aan_kl_subj_line',
		'Enter email Subject Line here',
		'aan_kl_setting_input_subjline',
		'aan_kl',
		'aan_kl_main'
	);
	add_settings_field(
		'aan_kl_email_body',
		'Enter email Body text here',
		'aan_kl_setting_input_bodytext',
		'aan_kl',
		'aan_kl_main'
	);
	add_settings_field(
		'aan_kl_js_alert_txt',
		'Enter Javascript alert text here',
		'aan_kl_setting_js_alert_bodytext',
		'aan_kl',
		'aan_kl_main'
	);
}

function aan_kl_section_text() {
	echo '<p><em>Enter the email and javascript alert() details below.</em></p>';
}

function aan_kl_setting_input() {
	$options = get_option( 'aan_kl_options' );
	$text_string = $options['text_string'];
	echo "<input id='text_string' size='50' name='aan_kl_options[text_string]' type='text' value='$text_string' />";
}
function aan_kl_setting_input_subjline() {
	$options = get_option( 'aan_kl_options' );
	$text_string2 = $options['text_string2'];
	echo "<input id='text_string2' size='50' name='aan_kl_options[text_string2]' type='text' value='$text_string2' />";
}
function aan_kl_setting_input_bodytext() {
	$options = get_option( 'aan_kl_options' );
	$text_string3 = $options['text_string3'];
	echo "<input id='text_string3' size='100' maxlength='100' name='aan_kl_options[text_string3]' type='text' value='$text_string3' />";
}
function aan_kl_setting_js_alert_bodytext() {
	$options = get_option( 'aan_kl_options' );
	$text_string4 = $options['text_string4'];
	echo "<input id='text_string4' size='100' maxlength='100' name='aan_kl_options[text_string4]' type='text' value='$text_string4' />";
}

/*
########################### 4: Do the work ###############################
*/

function aan_kl_get_the_alert() {
	$aan_kl_optional = get_option('aan_kl_options');
	$aan_kl_alert_message = $aan_kl_optional['text_string4'];
	$aan_kl_admin_email = $aan_kl_optional['text_string'];
	$aan_kl_email_subject = $aan_kl_optional['text_string2'];
	$aan_kl_email_body = $aan_kl_optional['text_string3'];
	wp_mail( $aan_kl_admin_email, '$aan_kl_email_subject', '$aan_kl_email_body' );
	$aan_kl_redirect_location = admin_url();
	echo "<script type='text/javascript'>";
	echo "alert('" . $aan_kl_alert_message . "');";
	echo "location = '" . $aan_kl_redirect_location . "';";
	echo "</script>	";
	exit;
}
	add_action('admin_menu', 'aan_kl_create_settings_page');
	add_action('draft_to_pending', 'aan_kl_get_the_alert'); ?>