<?php
/**
 * @package Notes-PostWidgets
 * @author David M&aring;rtensson
 * @version 1.0.8
 */
/*
Plugin Name: Notes PostWidgets
Plugin URI: http://notesblog.com/notes/post-widgets/
Description: Notes PostWidgets is a plugin which adds a custom post type that you can use to create text widgets with.
Author: David M&aring;rtensson
Version: 1.0.8
Author URI: http://www.feedmeastraycat.net/
*/



/**
 * Set plugin dir
 */
define('NotesPostWidgets_PLUGIN_DIR', plugin_basename(dirname(__FILE__)));

/**
 * Set text domain
 */
load_plugin_textdomain('Notes-PostWidgets', false, NotesPostWidgets_PLUGIN_DIR.'/languages/');

/**
 * Set Notes Post version
 */
define('NotesPostWidgets_VERSION', '1.0.8');

/**
 * Set plugin URL
 */
define('NotesPostWidgets_PLUGIN_URL', WP_CONTENT_URL."/plugins/".NotesPostWidgets_PLUGIN_DIR);



/**
 * Activate plugin.
 */
function NotesPostWidgets_activate() {
	
	// Check installed version
	$installed_version = get_option('NotesPostWidgets_version', '0.0.0');
	
	// No previous installed version
	if ($installed_version == "0.0.0") {
		NotesPostWidgets_install();
	}
	
	// Require upgrade
	elseIf (version_compare($installed_version, NotesPostWidgets_VERSION, '<')) {
		NotesPostWidgets_upgrade($installed_version);
	}

}
register_activation_hook(__FILE__, 'NotesPostWidgets_activate');


/**
 * Deactivate plugin
 */
function NotesPostWidgets_deactivate() {
	
	// Uninstall
	NotesPostWidgets_uninstall();
	
}
register_deactivation_hook(__FILE__, 'NotesPostWidgets_deactivate');


/**
 * Init plugin
 */
function NotesPostWidgets_init() {

	// Check if update is required
	$installed_version = get_option('NotesPostWidgets_version', '0.0.0');
	// Require upgrade
	if (version_compare($installed_version, NotesPostWidgets_VERSION, '<')) {
		NotesPostWidgets_upgrade($installed_version);
	}
	
	// Does any admin notifications need to be shown
	// - Show 1.0.0 upgrade notice
	if (get_option('NotesPostWidget_warning_PostTypeCap', 0) == 1) {
		add_action('admin_notices', 'NotesPostWidgets_admin_notice');
	}
	
	// Do admin page actions
	if (is_admin() && isset($_GET['plugin']) && $_GET['plugin'] == "notes-postwidgets") {
		// - 1.0.0 post type cap upgrade
		if (isset($_GET['action']) && $_GET['action'] == "upgrade-warning-posttypecap") {
			NotesPostWidgets_upgrade_PostTypeCap();
		}
	}
	
	// Adds custom post type
	$labels = array(
		'name' => __('Post Widgets', 'Notes-PostWidgets'),
		'singular_name' => __('Post Widget', 'Notes-PostWidgets'),
		'add_new' => __('Add New', 'Notes-PostWidgets'),
		'add_new_item' => __('Add New Post Widget', 'Notes-PostWidgets'),
		'edit_item' => __('Edit Post Widget', 'Notes-PostWidgets'),
		'new_item' => __('New Post Widget', 'Notes-PostWidgets'),
		'view_item' => __('View Post Widget', 'Notes-PostWidgets'),
		'search_items' => __('Search Post Widgets', 'Notes-PostWidgets'),
		'not_found' =>  __('No Post Widgets found', 'Notes-PostWidgets'),
		'not_found_in_trash' => __('No Post Widgets found in Trash', 'Notes-PostWidgets'), 
		'parent_item_colon' => ''
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => true,
		'show_ui' => true, 
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title', 'editor')
	); 
	register_post_type('notespostwidgets', $args);
	  
	// If admin page, load jquery
	if (is_admin()) {
		wp_enqueue_script('prototype'); 
	}
	
}
add_action('init', 'NotesPostWidgets_init');


/**
 * Add filter to insure the text Book, or book, is displayed when user updates a book
 */ 
function NotesPostWidgets_custom_post_updated_messages( $messages ) {
	// Variables who might not always be in use, ruins the page in debug mode
	$post_ID = (isset($post_ID) ? $post_ID:null);
	
	// Return messages
	$messages['NotesPostWidgets'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Post Widget updated. <a href="%s">View Post Widget</a>', 'Notes-PostWidgets'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.', 'Notes-PostWidgets'),
		3 => __('Custom field deleted.', 'Notes-PostWidgets'),
		4 => __('Post Widget updated.', 'Notes-PostWidgets'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Post Widget restored to revision from %s', 'Notes-PostWidgets'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Post Widget published. <a href="%s">View Post Widget</a>', 'Notes-PostWidgets'), esc_url( get_permalink($post_ID) ) ),
		7 => __('Post Widget saved.', 'Notes-PostWidgets'),
		8 => sprintf( __('Post Widget submitted. <a target="_blank" href="%s">Preview Post Widget</a>', 'Notes-PostWidgets'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('Post Widget scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Post Widget</a>', 'Notes-PostWidgets'),
		// translators: Publish box date format, see http://php.net/date
		date_i18n( __( 'M j, Y @ G:i' ), strtotime( @$post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('Post Widget draft updated. <a target="_blank" href="%s">Preview Post Widget</a>', 'Notes-PostWidgets'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	);
	return $messages;
}
add_filter('post_updated_messages', 'NotesPostWidgets_custom_post_updated_messages');


/**
 * Runs install when no installation has existed, or previously uninstalled.
 * Can also be run if one or more of the required tables does not exist.
 */
function NotesPostWidgets_install() {
	
	// Uppdate/Add version
	if (!update_option('NotesPostWidgets_version', NotesPostWidgets_VERSION)) {
		add_option('NotesPostWidgets_version', NotesPostWidgets_VERSION);
	}
	
}


/**
 * Upgrade
 *
 * @param string $current_version
 */
function NotesPostWidgets_upgrade($current_version) {

	// Any version previous then 1.0.2
	if (version_compare($current_version, '1.0.2', '<')) {
		// Set option to make code aware of 1.0.0 post_type problem
		update_option('NotesPostWidget_warning_PostTypeCap', 1);
	}
	
	// Uppdate/Add version
	if (!update_option('NotesPostWidgets_version', NotesPostWidgets_VERSION)) {
		add_option('NotesPostWidgets_version', NotesPostWidgets_VERSION);
	}
}


/**
 * Upgrade from the post type cap problem
 */
function NotesPostWidgets_upgrade_PostTypeCap() {
	global $wpdb;
	
	// Has anything to update?
	$rows_to_update = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM ".$wpdb->prefix."posts WHERE post_type='NotesPostWidget';" ) );
	if ($rows_to_update > 0) {
		
		// Update post type in database
		$update = $wpdb->update($wpdb->prefix."posts", array('post_type' => 'notespostwidgets'), array('post_type' => 'NotesPostWidgets'));
		
	}
	
	// Remove option
	if ($rows_to_update == 0 || (isset($update) && $update)) {
		delete_option('NotesPostWidget_warning_PostTypeCap');
		delete_option('NotesPostWidget_warning_PostTypeCap_error');
	}
	else {
		update_option('NotesPostWidget_warning_PostTypeCap_error', 1);
	}
	
	header("Location: ".admin_url('plugins.php'));
	exit;
}


/**
 * Uninstall
 */
function NotesPostWidgets_uninstall() {

	// Uppdate/Clear version
	if (!update_option('NotesPostWidgets_version', '0.0.0')) {
		add_option('NotesPostWidgets_version', '0.0.0');
	}
	
}



/**
 * Add javascript for Admin head
 */
function NotesPostWidgets_admin_head() {
	?>
	<script type="text/javascript" language="javascript" src="<?php echo NotesPostWidgets_PLUGIN_URL; ?>/notes-postwidgets-admin.js?ver=<?php echo NotesPostWidgets_VERSION; ?>"></script>
	<?php
}
add_action('admin_head', 'NotesPostWidgets_admin_head');


/**
 * Ajax search handle
 */
function NotesPostWidgets_ajax_search() {
	
	// Get keyword
	$keyword = urldecode($_POST['keyword']);
	
	// Get custom posts 
	$args = array(
		'post_type' => 'NotesPostWidgets',
		'post_status' => 'publish',
		's' => $keyword,
		'posts_per_page' => -1,
		'orderby' => 'title'
	);
	$query = new WP_Query($args);
	$post_widgets = array();
	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$post_widgets[] = array(
				'id' => (int)get_the_ID(),
				'title' => strip_tags(get_the_title())
			);
		}
	}
	
	// Return json
	header("Content-type: application/json");
	print("/*-secure-\n".json_encode($post_widgets)."\n*/");
	die();
	
}
add_action('wp_ajax_notes_post_widgets_search', 'NotesPostWidgets_ajax_search');



/**
 * Register the Widget
 */
function NotesPostWidgets_register() {
	register_widget('Notes_PostWidgets');
}
add_action('widgets_init', 'NotesPostWidgets_register');


/**
 * Notes PostWidgets class
 * @see WP_Widget
 */
class Notes_PostWidgets extends WP_Widget {
	
	/**
	 * Init widget
	 */
	function Notes_PostWidgets() {
		// Widget settings.
		$widget_ops = array('classname' => 'Notes_PostWidgets', 'description' => __('Adds a Notes PostWidget.', 'Notes-PostWidgets'));
	
		// Widget control settings.
		$control_ops = array('id_base' => 'notes-postwidgets');
	
		// Create the widget.
		$this->WP_Widget('notes-postwidgets', 'Notes PostWidget', $widget_ops, $control_ops);
		
	}
	
	/**
	 * Output widget
	 */
	function widget($args, $instance) {
		extract( $args );
		
		// Get container div class
		$container_div_class = strip_tags($instance['container_div']);
		
		// Get post to output id
		$post_id = (int)$instance['post_id'];
		if (!$post_id) {
			return false;
		}
		
		// Get post
		$post = wp_get_single_post($post_id);

		// User-selected settings.
		$title = $post->post_title;

		// Before widget (defined by themes).
		echo $before_widget;
		
		echo "<div".($container_div_class ? " class=\"".$container_div_class."\"":"").">";

		// Title of widget (before and after defined by themes).
		if ($title) {
			echo $before_title . $title . $after_title;
		}
		
		// Echo widget content
		$content = $post->post_content;
		$content = apply_filters('the_content', $content);
		$content = str_replace(']]>', ']]&gt;', $content);
		echo $content;
		
		echo "</div>";

		// After widget (defined by themes).
		echo $after_widget;
	}
	
	/**
	 * Update widget
	 */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance['post_id'] = (int)$new_instance['post_id'];
		$instance['container_div'] = strip_tags($new_instance['container_div']);

		return $instance;
	}
	
	/**
	 * Update widget form
	 */
	function form($instance) {
		
		$keyword_default = __('Keyword', 'Notes-PostWidgets');

		// Set up some default widget settings.
		$defaults = array('post_id' => 0, 'container_div' => '', 'search' => $keyword_default);
		$instance = wp_parse_args( (array) $instance, $defaults );

		// Get custom posts 
		$args = array(
			'post_type' => 'NotesPostWidgets',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => 'title'
		);
		$query = new WP_Query($args);
		$post_widgets = array();
		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$post_widgets[] = array(
					'id' => (int)get_the_ID(),
					'title' => get_the_title()
				);
			}
		}
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id('container_div'); ?>"><?php _e('Container css class', 'Notes-PostWidgets'); ?></label><br/>
			<input 
				id="<?php echo $this->get_field_id('container_div'); ?>" 
				name="<?php echo $this->get_field_name('container_div'); ?>" 
				value="<?php echo $instance['container_div']; ?>" 
				class="widefat"
			/><br/>
		</p>
		<div id="NotesPostWidgets-container-<?php echo $this->get_field_id('post_id'); ?>"><p>
			<label for="<?php echo $this->get_field_id('post_id'); ?>"><?php _e('Choose Post Widget', 'Notes-PostWidgets'); ?></label> <small>(<a href="javascript:NotesPostWidgets.toggle('NotesPostWidgets-container-<?php echo $this->get_field_id('post_id'); ?>', 'NotesPostWidgets-container-<?php echo $this->get_field_id('search'); ?>');"><?php _e('View search', 'Notes-PostWidgets'); ?></a>)</small><br/>
			<select id="<?php echo $this->get_field_id('post_id'); ?>" name="<?php echo $this->get_field_name('post_id'); ?>" class="widefat" style="margin-bottom: 5px;">
				<option value="0" style="font-style: italic;"><?php _e('Choose Post Widget', 'Notes-PostWidgets'); ?></option>
				<?php
				foreach ($post_widgets AS $post) {
					?>
					<option <?php echo ($instance['post_id'] == $post['id'] ? "selected=\"selected\"":""); ?> value="<?php echo $post['id']?>"><?php echo htmlspecialchars($post['title']); ?></option>
					<?php
				}
				?>
			</select><br/>
		</p></div>
		<div id="NotesPostWidgets-container-<?php echo $this->get_field_id('search'); ?>" style="display: none;">
			<label for="<?php echo $this->get_field_id('search'); ?>"><?php _e('Search', 'Notes-PostWidgets'); ?></label>  <small>(<a href="javascript:NotesPostWidgets.toggle('NotesPostWidgets-container-<?php echo $this->get_field_id('search'); ?>', 'NotesPostWidgets-container-<?php echo $this->get_field_id('post_id'); ?>');"><?php _e('View choose', 'Notes-PostWidgets'); ?></a>)</small><br/>
			<div style="float: right;">
				<div id="NotesPostWidgets-search-button-<?php echo $this->get_field_id('search'); ?>">
					<input 
						type="button" 
						name="NotePostWidgets-searchbutton-<?php echo $this->get_field_id('search'); ?>"
						class="button-secondary"
						value="<?php _e('Search', 'Notes-PostWidgets'); ?>"
						onclick="NotesPostWidgets.search('<?php echo $this->get_field_id('search'); ?>', '<?php echo $this->get_field_id('post_id'); ?>');"
					/>
				</div>
			</div>
			<input 
				id="<?php echo $this->get_field_id('search'); ?>" 
				name="<?php echo $this->get_field_name('search'); ?>" 
				value="<?php echo $instance['search']; ?>" 
				onfocus="if(this.value=='<?php echo $keyword_default?>'){this.value=''}"
				onblur="if(this.value==''){this.value='<?php echo $keyword_default?>'}"
				class="widefat" 
				style="width: 155px;"
			/><br/>
			<div style="display: none; padding-top: 10px;" id="NotesPostWidgets-search-loading-<?php echo $this->get_field_id('search'); ?>">
				<img src="images/wpspin_light.gif" border="0" alt="" />
			</div>
			<div style="display: none; padding-top: 10px;" id="NotesPostWidgets-search-result-<?php echo $this->get_field_id('search'); ?>"></div>
			<p>&nbsp;</p>
		</div>
		<?php
	}

}



/**
 * Display any admin notices
 */
function NotesPostWidgets_admin_notice(){
	// Show 1.0.0 upgrade notice
	if (get_option('NotesPostWidget_warning_PostTypeCap', 0) == 1) {
		$url = admin_url('plugins.php?action=upgrade-warning-posttypecap&plugin=notes-postwidgets');
		$msg = __('You have upgraded to Notes PostWidgets version 1.0.2 which require a database upgrade. Please backup your data and <a href="%url">click here</a> to perform the upgrade.', 'Notes-PostWidgets');
		$msg = str_replace("%url", $url, $msg);
		if (get_option('NotesPostWidget_warning_PostTypeCap_error', 0) == 1) {
			$msg .= " <strong>".__('An error occurred during last upgrade process.', 'Notes-PostWidgets')."</strong>";
		}
	}
	// Show msg
	echo '<div class="error"><p>'.$msg.'</p></div>';
}




