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
			<?php
				$iframe_width = intval(get_option('tp_iframe_width'));
				$iframe_height = intval(get_option('tp_iframe_height'));
				$show_background = intval(get_option('tp_show_background'));
				$include_padding = intval(get_option('tp_include_padding'));
				$show_username = intval(get_option('tp_show_username'));
				$show_source = intval(get_option('tp_show_source'));
			?>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="tp_iframe_width">iFrame width</label></th>
					<td><input type="text" id="tp_iframe_width" name="tp_iframe_width" value="<?php echo $iframe_width; ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="tp_iframe_height">iFrame height</label></th>
					<td><input type="text" id="tp_iframe_height" name="tp_iframe_height" value="<?php echo $iframe_height ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row">&nbsp;</th>
					<td>
						<label for="tp_show_background">
							<input type="checkbox" id="tp_show_background" name="tp_show_background" value="1"<?php echo ($show_background == 1) ? ' checked="checked"' : ''; ?> />
							Show background
						</label><br />
						<label for="tp_include_padding">
							<input type="checkbox" id="tp_include_padding" name="tp_include_padding" value="1"<?php echo ($include_padding == 1) ? ' checked="checked"' : ''; ?> />
							Show padding around the tweet box
						</label><br />
						<label for="tp_show_username">
							<input type="checkbox" id="tp_show_username" name="tp_show_username" value="1"<?php echo ($show_username == 1) ? ' checked="checked"' : ''; ?> />
							Show the twitterer’s username and avatar
						</label><br />
						<label for="tp_show_source">
							<input type="checkbox" id="tp_show_source" name="tp_show_source" value="1"<?php echo ($show_source == 1) ? ' checked="checked"' : ''; ?> />
							Show the source of the tweet: ie &ldquo;via Web&rdquo;
						</label>
					</td>
				</tr>
			</table>
			
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="tp_iframe_width,tp_iframe_height,tp_show_background,tp_include_padding,tp_show_username,tp_show_source" />
			
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div><?php
}

// [tweet id=%id"]
function write_tweet($atts) {
	$url = 'http://tweetpaste.thingamaweb.com/api/gethtml/';
	$params = '';
	
	$iframe_width = intval(get_option('tp_iframe_width'));
	$iframe_height = intval(get_option('tp_iframe_height'));
	$show_background = intval(get_option('tp_show_background'));
	$include_padding = intval(get_option('tp_include_padding'));
	$show_username = intval(get_option('tp_show_username'));
	$show_source = intval(get_option('tp_show_source'));
	
	if($atts['url']) {
		$tweet_url = $atts['url'];
		if(substr($tweet_url, -1) == '/') {
			$url = substr($tweet_url, 0, -1);
		}
		
		$split = explode('/', $tweet_url);
		$atts['id'] = $split[count($split) - 1];
		unset($atts['url']);
	}
	
	if(!isset($atts['iframe_width'])) { $atts['iframe_width'] = $iframe_width;	}
	if(!isset($atts['iframe_height'])) { $atts['iframe_height'] = $iframe_height; }
	if(!isset($atts['show_background'])) { $atts['show_background'] = $show_background; }
	if(!isset($atts['include_padding'])) { $atts['include_padding'] = $include_padding; }
	if(!isset($atts['show_username'])) { $atts['show_username'] = $show_username; }
	if(!isset($atts['show_source'])) { $atts['show_source'] = $show_source; }
	
	if(isset($atts['iframe_width'])) {
		if($atts['iframe_width'] == 0) {
			unset($atts['iframe_width']);
		}
	}
	
	if(isset($atts['iframe_height'])) {
		if($atts['iframe_height'] == 0) {
			unset($atts['iframe_height']);
		}
	}
	
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
		
		if(substr($script, 0, strlen('ERROR:')) == 'ERROR:') {
			return 'Error showing tweet with parameters ' . $params . ': ' . substr($script, strlen('ERROR:') + 1);
		}
		
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
	register_setting('tweetpaste-embed', 'tp_iframe_width', 'intval');
	register_setting('tweetpaste-embed', 'tp_iframe_height', 'intval');
	register_setting('tweetpaste-embed', 'tp_show_background');
	register_setting('tweetpaste-embed', 'tp_include_padding');
	register_setting('tweetpaste-embed', 'tp_show_username');
	register_setting('tweetpaste-embed', 'tp_show_source');
	
	add_option('tp_iframe_width', '500');
	add_option('tp_iframe_height', '200');
	add_option('tp_show_background', '1');
	add_option('tp_include_padding', '1');
	add_option('tp_show_username', '1');
	add_option('tp_show_source', '1');
}

add_shortcode('tweet', 'write_tweet');
register_activation_hook(__FILE__, 'tp_install');

if (is_admin()) {
	add_action('admin_menu', 'register_admin_page');
	add_action('admin_init', 'register_settings');
}
?>