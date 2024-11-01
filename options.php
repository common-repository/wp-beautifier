<?php

/**
 * This file contains backend related code of the WP-Beautifier plugin.
 *
 * Copyright 2011 Till Krüss  (www.tillkruess.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package WP-Beautifier
 * @copyright 2011 Till Krüss
 *
 */

/**
 * Define WP-Beautifier's plugin basename variable.
 * @global string WP's plugin path to the the main file of this plugin
 * @since 1.3.1
 */
$beautifier_basename = plugin_basename($beautifier_file);

/**
 * Register admin actions/filter, register uninstall callback and
 * try loading the localization.
 */
add_action('admin_init', 'beautifier_admin_init');
add_action('admin_menu', 'beautifier_menu', 0);
add_action('contextual_help', 'beautifier_helpmenu', 10, 3);
add_filter('plugin_action_links_'.$beautifier_basename, 'beautifier_optionslink', 10, 1);
add_filter('plugin_row_meta', 'beautifier_metalinks', 10, 2);
load_plugin_textdomain('wp-beautifier', FALSE, 'wp-beautifier/l10n');
register_uninstall_hook(__FILE__, 'beautifier_uninstall');

/**
 * This is the callback function which is called by Wordpress
 * when this plugin is uninstalled/deleted. It deletes all stored
 * options which start with "wp_beautifier_".
 *
 * @since 1.0
 */
function beautifier_uninstall() {

	$options = wp_load_alloptions();

	foreach ($options as $option => $value) {
		if (preg_match('~^wp_beautifier_~', $option)) {
			delete_option($option);
		}
	}

}

/**
 * This is the callback function for the registerd action
 * 'admin_init' which is called by WordPress itself. It
 * registers and queues the 'options.css' file under the
 * global handle 'wp-beautifier.css' and sets the plugin
 * install time, if not set yet.
 *
 * @since 1.4
 */
function beautifier_admin_init() {

	wp_register_style('wp-beautifier.css', plugin_dir_url(__FILE__).'options.css');
	wp_enqueue_style('wp-beautifier.css');

	if (get_option('wp_beautifier_install_date') === false) {
		add_option('wp_beautifier_install_date', time());
	}

}

/**
 * This is the callback function for the registerd action
 * 'admin_menu' which is called by WordPress itself. It
 * registers the option page for this plugin in the Wordpress
 * Backend.
 *
 * @since 1.0
 * @global string $beautifier_screen_id WP-Beautifier's unique screen handle
 */
function beautifier_menu() {

	global $beautifier_screen_id;

	$beautifier_screen_id = add_options_page(__('Beautification Settings', 'wp-beautifier'), __('Beautification', 'wp-beautifier'), 'manage_options', 'wp-beautifier', 'beautifier_options');

}

/**
 * This is the callback function for the WordPress filter
 * plugin_action_links_{$plugin_file} which is called by WP
 * itself. It adds a settings link to the plugin actions in
 * the plugin listing.
 *
 * @since 1.3
 *
 * @param $actions WP's default plugin actions like deactivate, edit and delete
 */
function beautifier_optionslink($links) {

	$links[] = '<a href="options-general.php?page=wp-beautifier" title="'.__('Edit plugin settings', 'wp-beautifier').'">'.__('Settings').'</a>';

	return $links;

}

/**
 * This is the callback function for WordPress's plugin_row_meta
 * filter. It adds useful links to this plugin's meta row in the
 * WordPress plugin listing.
 *
 * @since 1.3.1
 *
 * @param array $links WP's default meta links like "Visit plugin site"
 * @param string $file The plugin basename of the current file in the loop
 */
function beautifier_metalinks($links, $file) {

	global $beautifier_basename;

	if ($file == $beautifier_basename) {
		$links[] = '<a href="http://wordpress.org/extend/plugins/wp-beautifier/faq/" target="_blank">'.__('FAQ', 'wp-beautifier').'</a>';
		$links[] = '<a href="http://wordpress.org/tags/wp-beautifier" target="_blank">'.__('Support Forum', 'wp-beautifier').'</a>';
	}

	return $links;

}

/**
 * This is the callback function for the registerd action
 * 'contextual_help' which is called by WordPress itself.
 * It return the help text for this plugin.
 *
 * @since 1.2
 * @global string $beautifier_screen_id WP-Beautifier's unique screen handle
 *
 * @param string $contextual_help Arbitrary help text
 * @param string $screen_id The handle for the screen to add help to (this is usually the hook name returned by the add_*_page() functions)
 * @param stdClass $screen An object containing the safe screen name and id [@see convert_to_screen()]
 * @return string The WP-Beautifier help text
 */
function beautifier_helpmenu($contextual_help, $screen_id, $screen) {

	global $beautifier_screen_id;

	if ($screen_id == $beautifier_screen_id) {

		$contextual_help = '<p>'.__('<strong>Code indentation</strong> &#8211; The code indentation option unifies all line endings into unix-style <code>&#92;n</code> and removes all whitespace from the end and the beginning of each line and from the document in its entirety. It also removes empty lines, unnecessary whitespace in continuous text (including tag declaration) and horizontal whitespace between tags. Afterwards all lines are indented correctly with tabs <code>&#92;t</code> for easier reading.', 'wp-beautifier').'</p>';
		$contextual_help .= '<p>'.__('<strong>Single quotes conversion</strong> &#8211; This option converts single quoted tag attributes values like <code>&lt;link rel=&#39;&#8230;&#39;&gt;</code> into double quoted attributes values <code>&lt;link rel=&#34;&#8230;&#34;&gt;</code>, if the attribute is not listed under <em>Ignored attributes</em>.', 'wp-beautifier').'</p>';
		$contextual_help .= '<p>'.__('<strong>Tag declarations cleaning</strong> &#8211; This option removes horizontal whitespace before closing brackets and adds a space before self closing brackets, if there is none.', 'wp-beautifier').'</p>';
		$contextual_help .= '<p>'.__('<strong>Inline JavaScript compression</strong> &#8211; All <code>script</code> tags containing JavaScript code will be compressed (or minified) with JSMin. This means the removal of comments, unnecessary whitespace/line breaks and more. More information can be found on the <a href="http://www.crockford.com/javascript/jsmin.html" target="_blank">JSMin website</a>.', 'wp-beautifier').'</p>';
		$contextual_help .= '<p>'.__('<strong>Ignored tags</strong> &#8211; The contents of these tags remain completly untouched. Tag attributes will be converted. To modify or remove tag contents, use the <em>custom regular expressions</em>.', 'wp-beautifier').'</p>';
		$contextual_help .= '<p>'.__('<strong>Ignored attributes</strong> &#8211; These tag attributes will be ignored by the single quotes conversion.', 'wp-beautifier').'</p>';
		$contextual_help .= '<p>'.__('<strong>Custom regular expressions</strong> &#8211; Each line is treated as a <a href="http://en.wikipedia.org/wiki/Regular_expression" title="Wikipedia article about regular expressions" target="_blank">regular expression pattern</a>. All matching strings are removed from the ouput, by passing each pattern through PHP\'s <code><a href="http://php.net/preg_replace" target="_blank">preg_replace</a></code> function. Empty lines and lines beginning with a <code>#</code> are ignored. Additional information about regular expressions can be found in the <a href="http://php.net/manual/en/reference.pcre.pattern.syntax.php" target="_blank">PHP documentation</a> and at <a href="http://regular-expressions.info/" target="_blank">regular-expressions.info</a>. <strong>Be very careful! Invalid patterns can abort the execution of the WordPress frontend and your site becomes <em>temporarily</em> unusable (you\'ll only see a white/blank page)!</strong>', 'wp-beautifier').'</p>';
		$contextual_help .= '<p><strong>'.__('Helpful links', 'wp-beautifier').':</strong></p>
			<ul>
				<li><a href="http://tillkruess.com/projects/wp-beautifier/" target="_blank">'.__('Plugin Homepage', 'wp-beautifier').'</a> / <a href="http://wordpress.org/extend/plugins/wp-beautifier/" target="_blank">'.__('Plugin on WP.org', 'wp-beautifier').'</a></li>
				<li><a href="http://wordpress.org/extend/plugins/wp-beautifier/faq/" target="_blank">'.__('FAQ', 'wp-beautifier').'</a> / <a href="http://wordpress.org/tags/wp-beautifier" target="_blank">'.__('Support Forum', 'wp-beautifier').'</a> / <a href="http://wordpress.org/tags/wp-beautifier" target="_blank">'.__('Report a bug', 'wp-beautifier').'</a> / <a href="http://wordpress.org/tags/wp-beautifier" target="_blank">'.__('Request a feature', 'wp-beautifier').'</a></li>
				<li><a href="http://tillkruess.com/donations/" target="_blank">'.__('Donate', 'wp-beautifier').'</a></li>
			</ul>
		';

	}

	return $contextual_help;

}

/**
 * This is the callback function registered by add_options_page()
 * in beautifier_menu() which is called by WordPress itself. It
 * echos the XHTML for the options page of this plugin and
 * handles changes to those.
 *
 * @since 1.0
 * @global array $beautifier_default_options WP-Beautifier's default configuration parameters
 * @see beautifier_menu() Registers this plugin's options page
 */
function beautifier_options() {

	global $beautifier_default_options;

	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.'));
	}

	if (!empty($_POST) && isset($_POST['reset'])) {

		foreach ($beautifier_default_options as $option => $value) {
			update_option($option, $value);
		}

		echo '<div class="error"><p><strong>'.__('Default settings restored.', 'wp-beautifier').'</strong></p></div>';

	} elseif (!empty($_POST)) {

		update_option('wp_beautifier_indent_flag', isset($_POST['wp_beautifier_indent_flag']) ? 1 : 0);
		update_option('wp_beautifier_comments_flag', isset($_POST['wp_beautifier_comments_flag']) ? 1 : 0);
		update_option('wp_beautifier_quotes_flag', isset($_POST['wp_beautifier_quotes_flag']) ? 1 : 0);
		update_option('wp_beautifier_tagdeclarations_flag', isset($_POST['wp_beautifier_tagdeclarations_flag']) ? 1 : 0);
		update_option('wp_beautifier_inlinejs_flag', isset($_POST['wp_beautifier_inlinejs_flag']) ? 1 : 0);
		update_option('wp_beautifier_regexps_flag', isset($_POST['wp_beautifier_regexps_flag']) ? 1 : 0);
		update_option('wp_beautifier_feed_flag', isset($_POST['wp_beautifier_feed_flag']) ? 1 : 0);
		update_option('wp_beautifier_feed_regexps_flag', isset($_POST['wp_beautifier_feed_regexps_flag']) ? 1 : 0);

		if (isset($_POST['wp_beautifier_ignored_tags'])) {
			$tags = preg_split('~[\s,]+~', $_POST['wp_beautifier_ignored_tags'], -1, PREG_SPLIT_NO_EMPTY);
			update_option('wp_beautifier_ignored_tags', implode(', ', $tags));
		}

		if (isset($_POST['wp_beautifier_ignored_attributes'])) {
			$attributes = preg_split('~[\s,]+~', $_POST['wp_beautifier_ignored_attributes'], -1, PREG_SPLIT_NO_EMPTY);
			update_option('wp_beautifier_ignored_attributes', implode(', ', $attributes));
		}

		if (isset($_POST['wp_beautifier_regexps'])) {
			$regexps = stripslashes(trim($_POST['wp_beautifier_regexps']));
			$regexps = preg_replace('~\r\n~ms', "\n", $regexps);
			$regexps = preg_replace('~\r~ms', "\n", $regexps);
			update_option('wp_beautifier_regexps', $regexps);
		}

		echo '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>';

	}

?>
<div class="wrap" id="wp-beautifier">
	<?php screen_icon() ?>
	<h2><?php _e('Beautification Settings', 'wp-beautifier') ?></h2>
	<?php if ((get_option('wp_beautifier_install_date') + (86400 * 10)) < time()) : ?>
		<div class="updated">
			<p><?php _e('Thanks for using this plugin. If you like it, please recommend it to others by <a href="http://wordpress.org/extend/plugins/wp-beautifier/" target="_blank">rating it</a>. Thank you.', 'wp-beautifier') ?></p>
		</div>
	<?php endif; ?>
	<form method="post" action="">
		<?php settings_fields('wp-beautifier') ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('(X)HTML output cleaning', 'wp-beautifier') ?></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span><?php _e('(X)HTML output cleaning', 'wp-beautifier') ?></span></legend>
						<label for="wp_beautifier_indent_flag">
							<input name="wp_beautifier_indent_flag" type="checkbox" id="wp_beautifier_indent_flag" value="1" <?php checked('1', get_option('wp_beautifier_indent_flag')) ?> />
							<?php _e('Indent (X)HTML output and remove unnecessary whitespace, like empty lines and trailing whitespace.', 'wp-beautifier') ?>
						</label>
						<ul>
							<li>
								<label for="wp_beautifier_quotes_flag">
									<input name="wp_beautifier_quotes_flag" type="checkbox" id="wp_beautifier_quotes_flag" value="1" <?php checked('1', get_option('wp_beautifier_quotes_flag')) ?> />
									<?php _e('Convert single quoted <code>&#039;</code> tag attributes values into double quoted <code>&quot;</code> values.', 'wp-beautifier') ?>
								</label>
							</li>
							<li>
								<label for="wp_beautifier_tagdeclarations_flag">
									<input name="wp_beautifier_tagdeclarations_flag" type="checkbox" id="wp_beautifier_tagdeclarations_flag" value="1" <?php checked('1', get_option('wp_beautifier_tagdeclarations_flag')) ?> />
									<?php _e('Clean (X)HTML tag declarations very precisely.', 'wp-beautifier') ?>
								</label>
							</li>
						</ul>
						<label for="wp_beautifier_comments_flag">
							<input name="wp_beautifier_comments_flag" type="checkbox" id="wp_beautifier_comments_flag" value="1" <?php checked('1', get_option('wp_beautifier_comments_flag')) ?> />
							<?php _e('Remove comments <code>&lt;!-- &#8230; --&gt;</code> from output.', 'wp-beautifier') ?>
						</label>
						<br />
						<label for="wp_beautifier_inlinejs_flag">
							<input name="wp_beautifier_inlinejs_flag" type="checkbox" id="wp_beautifier_inlinejs_flag" value="1" <?php checked('1', get_option('wp_beautifier_inlinejs_flag')) ?> />
							<?php _e('Compress inline JavaScript code.', 'wp-beautifier') ?>
						</label>
						<br />
						<label for="wp_beautifier_regexps_flag">
							<input name="wp_beautifier_regexps_flag" type="checkbox" id="wp_beautifier_regexps_flag" value="1" <?php checked('1', get_option('wp_beautifier_regexps_flag')) ?> />
							<?php _e('Apply the <em>custom regular expressions</em> to (X)HTML output.', 'wp-beautifier') ?>
						</label>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('XML output cleaning', 'wp-beautifier') ?></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span><?php _e('XML output cleaning', 'wp-beautifier') ?></span></legend>
						<label for="wp_beautifier_feed_flag">
							<input name="wp_beautifier_feed_flag" type="checkbox" id="wp_beautifier_feed_flag" value="1" <?php checked('1', get_option('wp_beautifier_feed_flag')) ?> />
							<?php _e('Indent and clean RSS and Atom-Feed output.', 'wp-beautifier') ?>
						</label>
						<br />
						<label for="wp_beautifier_feed_regexps_flag">
							<input name="wp_beautifier_feed_regexps_flag" type="checkbox" id="wp_beautifier_feed_regexps_flag" value="1" <?php checked('1', get_option('wp_beautifier_feed_regexps_flag')) ?> />
							<?php _e('Apply the <em>custom regular expressions</em> to Feed output.', 'wp-beautifier') ?>
						</label>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="wp_beautifier_ignored_tags"><?php _e('Ignored tags', 'wp-beautifier') ?></label></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span><?php _e('Ignored tags', 'wp-beautifier') ?></span></legend>
						<input name="wp_beautifier_ignored_tags" type="text" id="wp_beautifier_ignored_tags" value="<?php form_option('wp_beautifier_ignored_tags') ?>" class="regular-text" />
						<span class="description"><?php _e("Comma separated list of all tags, which contents will be preserved from the (X)HTML output cleaning. It's highly recommended to preserve the <code>pre</code>, <code>script</code> and <code>textarea</code> tag.", 'wp-beautifier') ?></span>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="wp_beautifier_ignored_attributes"><?php _e('Ignored attributes', 'wp-beautifier') ?></label></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span><?php _e('Ignored attributes', 'wp-beautifier') ?></span></legend>
						<input name="wp_beautifier_ignored_attributes" type="text" id="wp_beautifier_ignored_attributes" value="<?php form_option('wp_beautifier_ignored_attributes') ?>" class="regular-text" />
						<span class="description"><?php _e("Comma separated list of all tag attributes, which will be ignored by the single quotes conversion.", 'wp-beautifier') ?></span>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Custom regular expressions', 'wp-beautifier') ?></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span><?php _e('Custom regular expressions', 'wp-beautifier') ?></span></legend>
						<span class="description"><label for="wp_beautifier_regexps"><?php _e('Each line is treated as a <a href="http://en.wikipedia.org/wiki/Regular_expression" title="Wikipedia article about regular expressions" target="_blank">regular expression pattern</a> and applied to the output by passing it through PHP\'s <code><a href="http://php.net/preg_replace" target="_blank">preg_replace</a></code> function. Matching strings will be remove from the output. Lines beginning with a <code>#</code> are ignored. <strong>Be very careful! Invalid patterns can abort the execution of the WordPress frontend and your site becomes <em>temporarily</em> unusable!</strong>', 'wp-beautifier') ?></label></span>
						<textarea name="wp_beautifier_regexps" rows="10" cols="50" id="wp_beautifier_regexps" class="large-text code"><?php form_option('wp_beautifier_regexps') ?></textarea>
					</fieldset>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
			<input type="submit" name="reset" class="button-secondary" value="<?php esc_attr_e('Reset Options', 'wp-beautifier') ?>" onclick="return confirm('<?php _e('Really restore factory settings?', 'wp-beautifier') ?>');" />
		</p>
	</form>
</div>
<?php

}
