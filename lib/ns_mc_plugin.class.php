<?php

/**
 *
 */

class NS_MC_Plugin {
	
	private $options;
	
	private static $instance;
	private static $mcapi;
	private static $name = 'NS_MC_Plugin';
	private static $prefix = 'ns_mc';
	private static $public_option = 'no';
	private static $text_domain = 'mailchimp-widget';
	
	private function __construct () {
		
		register_activation_hook(__FILE__, array(&$this, 'set_up_options'));
		
		/**
		 * Set up the administration page.
		 */
		
		add_action('admin_menu', array(&$this, 'set_up_admin_page'));
		
		/**
		 * Fetch the options, and, if they haven't been set up yet, display a notice to the user.
		 */
		 
		$this->get_options();
		
		if ('' == $this->options) {
			
			add_action('admin_notices', array(&$this, 'admin_notices'));
			
		}

		/**
		 * Add our widget when widgets get intialized.
		 */
		
		add_action('widgets_init', create_function('', 'return register_widget("NS_Widget_MailChimp");'));
		
		/**
		 *
		 */
		
		add_action('init', array(&$this, 'load_text_domain'));
		
	}
	
	public static function get_instance () {

		if (empty(self::$instance)) {
			
			self::$instance = new self::$name;
			
		}
		
		return self::$instance;

	}
	
	/**
	 *
	 */
	
	public function admin_notices () {
		
		echo '<div class="error fade">' . $this->get_admin_notices() . '</div>';
		
	}

	public function admin_page () {	
		
		$api_key = (is_array($this->options)) ? $this->options['api-key'] : '';
		
		if (isset($_POST[self::$prefix . '_nonce'])) {
			
			$nonce = $_POST[self::$prefix . '_nonce'];
			
			$nonce_key = self::$prefix . '_update_options';
			
			if (! wp_verify_nonce($nonce, $nonce_key)) {
				
				echo '<div class="wrap">

					<div id="icon-options-general" class="icon32"><br /></div>

					<h2>MailChimp Widget Settings</h2><p>' . __('What you\'re trying to do looks a little shady.', 'mailchimp-widget') . '</p></div>';
				
				return false;
				
			} else {
				
				$new_api_key = $_POST[self::$prefix . '-api-key'];
				
				$new_options['api-key'] = $new_api_key;
				
				$this->update_options($new_options);
				
				$api_key = $this->options['api-key'];
				
			}
			
		}
		
		$admin_page = '<div class="wrap"><div id="icon-options-general" class="icon32"><br /></div><h2>' . __('MailChimp Widget Settings', 'mailchimp-widget') . '</h2><p>' . __('Enter a valid MailChimp API key here to get started. Once you\'ve done that, you can use the MailChimp Widget from the Widgets menu. You will need to have at least MailChimp list set up before the using the widget.', 'mailchimp-widget') . '</p><form action="' . $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '" method="post">' . wp_nonce_field(self::$prefix . '_update_options', self::$prefix . '_nonce', true, false) . '<table class="form-table"><tr valign="top"><th scope="row"><label for="' . self::$prefix . '-api-key">MailChimp Api Key</label></th><td><input class="regular-text" id="' . self::$prefix . '-api-key" name="' . self::$prefix . '-api-key" type="password" value="' . $api_key . '" /></td></tr></table><p class="submit"><input type="submit" name="Submit" class="button-primary" value="' . __('Save Changes', 'mailchimp-widget') . '" /></p></form></div>';
		
		echo $admin_page;
		
	}
	
	public function get_admin_notices () {
		
		$notice = '<p>';
		
		$notice .= __('You\'ll need to set up the MailChimp signup widget plugin options before using it. ', 'mailchimp-widget') . __('You can make your changes', 'mailchimp-widget') . ' <a href="/wp-admin/options-general.php?page=mailchimp-widget/lib/ns_mc_plugin.class.php">' . __('here', 'mailchimp-widget') . '.</a>';
		
		$notice .= '</p>';
		
		return $notice;
		
	}
	
	public function get_mcapi () {
		
		$api_key = $this->get_api_key();
		
		if (false == $api_key) {
			
			return false;
			
		} else {
			
			if (empty(self::$mcapi)) {
			
				self::$mcapi = new MCAPI($api_key);
				
			}
			
			return self::$mcapi;
			
		}
		
	}
	
	public function get_options () {
		
		$this->options = get_option(self::$prefix . '_options');
		
		return $this->options;
		
	}
	
	public function load_text_domain () {
		
		load_plugin_textdomain(self::$text_domain, false, dirname(plugin_basename(__FILE__)) . '/languages/');
		
	}
	
	public function remove_options () {
		
		delete_option(self::$prefix . '_options');
		
	}
	
	public function set_up_admin_page () {
		
		add_submenu_page('options-general.php', 'MailChimp Widget Options', 'MailChimp Widget', 'activate_plugins', __FILE__, array(&$this, 'admin_page'));
		
	}

	public function set_up_options () {
		
		add_option(self::$prefix . '_options', '', '', self::$public_option);
		
	}
	
	private function get_api_key () {
		
		if (is_array($this->options) && ! empty($this->options['api-key'])) {
		
			return $this->options['api-key'];
			
		} else {
			
			return false;
			
		}
		
	}
	
	private function update_options ($options_values) {
		
		$old_options_values = get_option(self::$prefix . '_options');
		
		$new_options_values = wp_parse_args($options_values, $old_options_values);
		
		update_option(self::$prefix .'_options', $new_options_values);
		
		$this->get_options();
		
	}
	
}

?>