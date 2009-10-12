<?php
/**
 * @package tweetpaste-embed
 * @author moxypark
 * @version 1.0.0
 */
/*
Plugin Name: TweetPaste Embed
Plugin URI: http://moxypark.co.uk/tweetpaste/
Description: Embed status updates from Twitter into blog posts and pages
Author: moxypark
Version: 1.0.0
Author URI: http://moxypark.co.uk/
*/

// Create the wp_embeddedtweets table if it doesn't already exist.
function tp_install() {
	global $wpdb;
	global $tp_db_version;
	
	$table_name = $wpdb->prefix . 'embeddedtweets';
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			args VARCHAR(255) NOT NULL,
			script text NOT NULL,
			UNIQUE KEY id (id)
		);";
		
    	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		add_option("tp_db_version", $tp_db_version);
	}
}

function register_admin_page() {
	add_submenu_page(
		'options-general.php',
		'Settings',
		'Embedded tweets',
		'administrator',
		'tweetpaste-embed',
		'show_admin_page'
	);
}

function show_admin_page() { ?>
	<div class="wrap">
		<h2>TweetPaste</h2>
		<p>The default settings to pass to the TweetPaste API</p>
		
		<form method="post" action="options.php">
			<?php wp_nonce_field('update-options'); ?>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row">iFrame width</th>
					<td><input type="text" name="tp_iframe_width" value="<?php echo get_option('tp_iframe_width'); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row">iFrame height</th>
					<td><input type="text" name="tp_iframe_height" value="<?php echo get_option('tp_iframe_height'); ?>" /></td>
				</tr>
			</table>
			
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="tp_iframe_width,tp_iframe_height,option_etc" />
			
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div><?php
}

// [tweet id=%id"]
function write_tweet($atts) {
	$url = 'http://3/';
	$params = '';
	
	foreach($atts as $key=>$value) {
		$params .= $key . '=' . urlencode($value) . '&';
	}
	
	if(substr($params, -1) == '&') {
		$params = substr($params, 0, -1);
	}
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'embeddedtweets';
	$tweets = $wpdb->query("SELECT script FROM $table_name WHERE args = '$params'");
	$tweets = $wpdb->last_result;
	
	if(count($tweets) > 0) {
		$tweet = $tweets[0];
		return $tweet->script;
	} else {
		$url = "$url?$params";
		$handle = fopen($url, 'r');
		$script = stream_get_contents($handle);
		fclose($handle);
		
		$wpdb->query(
			$wpdb->prepare(
				"INSERT INTO $table_name (args, script)	VALUES (%s, %s)", 
				$params,
				$script
			)
		);
	}
	
	return $script;
}

function register_settings() {
	register_setting('tweetpaste-embed', 'tp_iframe_width' );
	register_setting('tweetpaste-embed', 'tp_iframe_height' );
}

add_shortcode('tweet', 'write_tweet');
register_activation_hook(__FILE__, 'tp_install');

if (is_admin()) {
	add_action('admin_menu', 'register_admin_page');
	add_action('admin_init', 'register_settings');
}
?>