<?php
/**
 * @author James Lafferty
 * @since 0.1
 */

class NS_Widget_MailChimp extends WP_Widget {
	
	private $default_loader_graphic = '/wp-content/plugins/mailchimp_widget/images/ajax-loader.gif';
	private $default_signup_text = 'Join now!';
	private $default_title = 'Sign up for our mailing list.';
	
	private $ns_mc_plugin;
	
	/**
	 * @author James Lafferty
	 * @since 0.1
	 */
	
	public function NS_Widget_MailChimp () {
		
		$widget_options = array('classname' => 'widget_ns_mailchimp', 'description' => __( "Displays a sign-up form for a MailChimp mailing list.", 'mailchimp-widget'));
		
		$this->WP_Widget('ns_widget_mailchimp', __('MailChimp List Signup', 'mailchimp-widget'), $widget_options);
		
		$this->ns_mc_plugin = NS_MC_Plugin::get_instance();
		
		$this->default_loader_graphic = get_bloginfo('wpurl') . $this->default_loader_graphic;
		
		add_action('init', array(&$this, 'add_scripts'));
		
		add_action('parse_request', array(&$this, 'process_submission'));
		
	}
	
	/**
	 * @author James Lafferty
	 * @since 0.1
	 */
	
	public function add_scripts () {
		
		wp_enqueue_script('ns-mc-widget', get_bloginfo('wpurl') . '/wp-content/plugins/mailchimp_widget/js/mailchimp-widget-min.js', array('jquery'), false);
		
	}
	
	/**
	 * @author James Lafferty
	 * @since 0.1
	 */
	
	public function form ($instance) {
		
		$mcapi = $this->ns_mc_plugin->get_mcapi();
		
		if (false != $mcapi) {
			
			$this->lists = $mcapi->lists();
			
			$defaults = array(

				'title' => __($this->default_title, 'mailchimp-widget'),
				'signup_text' => __($this->default_signup_text, 'mailchimp-widget')

			);

			$vars = wp_parse_args($instance, $defaults);

			extract($vars);

			$form = '<p><label>' . __('Title :', 'mailchimp-widget') . '<input class="widefat" id=""' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></label></p>';
			
			$form .= '<p><label>' . __('Select a Mailing List:', 'mailchimp-widget') . '';
			
			$form .= '<select class="widefat" id="' . $this->get_field_id('current_mailing_list') . '" name="' . $this->get_field_name('current_mailing_list') . '">';
			
			foreach ($this->lists['data'] as $key => $value) {
				
				$selected = ($current_mailing_list == $value['id']) ? ' selected="selected" ' : '';
				
				$form .= '<option ' . $selected . 'value="' . $value['id'] . '">' . __($value['name'], 'mailchimp-widget') . '</option>';
				
			}
			
			$form .= '</select></label></p><p><strong>N.B.</strong> ' . __('This is the list your users will be signing up for in your sidebar.', 'mailchimp-widget') . '</p>';
			
			$form .= '<p><label>' . __('Sign Up Button Text :', 'mailchimp-widget') . '<input class="widefat" id="' . $this->get_field_id('signup_text') .'" name="' . $this->get_field_name('signup_text') . '" value="' . $signup_text . '" /></label></p>';
			
			$form .= '<p><input type="checkbox" class="checkbox" /><label>Collect first name.</label><input type="checkbox" class="checkbox" /><label>Collect last name.</label></p><p>These fields won\'t (and shouldn\'t) be required. Only ask your users for stuff you really need to have.</p>';
			
		} else { //If an API key hasn't been entered, direct the user to set one up.
			
			$form = $this->ns_mc_plugin->get_admin_notices();
			
		}
		
		echo $form;
		
	}
	
	/**
	 * @author James Lafferty
	 * @since 0.1
	 */
	
	public function process_submission () {
		
		if (isset($_GET[$this->id_base . '_email'])) {
			
			header("Content-Type: application/json");
			
			//Assume the worst.
			$response = '';
			$result = array('success' => false, 'error' => __('There was a problem processing your submission.', 'mailchimp-widget'));
			
			if (! is_email($_GET[$this->id_base . '_email'])) { //Use WordPress's built-in is_email function to validate input.
				
				$response = json_encode($result); //If it's not a valid email address, just encode the defaults.
				
			} else {
				
				$mcapi = $this->ns_mc_plugin->get_mcapi();
				
				if (false == $this->ns_mc_plugin) {
					
					$response = json_encode($result);
					
				} else {
					
					$subscribed = $mcapi->listSubscribe($this->get_current_mailing_list_id($_GET['ns_mc_number']), $_GET[$this->id_base . '_email']);
				
					if (false == $subscribed) {
						
						
					} else {
					
						$result['success'] = true;
						$result['error'] = '';
						$result['success_message'] = __('Thank you for joining our mailing list. Please check your email for a confirmation link.', 'mailchimp-widget');
						$response = json_encode($result);
						
					}
					
				}
				
			}
			
			exit($response);
			
		} elseif (isset($_POST[$this->id_base . '_email'])) {
			
			$this->subscribe_errors = '<div class="error">' . __('There was a problem processing your submission.', 'mailchimp-widget') . '</div>';
			
			if (! is_email($_POST[$this->id_base . '_email'])) {
				
				return false;
				
			}
			
			$mcapi = $this->ns_mc_plugin->get_mcapi();
			
			if (false == $mcapi) {
				
				return false;
				
			}
			
			$subscribed = $mcapi->listSubscribe($this->get_current_mailing_list_id($_POST['ns_mc_number']), $_POST[$this->id_base . '_email']);
			
			if (false == $subscribed) {

				return false;
				
			} else {
				
				$this->subscribe_errors = '';
				
				setcookie($this->id_base . '-' . $this->number, $this->hash_mailing_list_id(), time() + 31556926);
				
				$this->successful_signup = true;
				
				$this->signup_success_message = '<p>' . __('Thank you for joining our mailing list. Please check your email for a confirmation link', 'mailchimp-widget') . '</p>';
				
				return true;
				
			}	
			
		}
		
	}
	
	/**
	 * @author James Lafferty
	 * @since 0.1
	 */
	
	public function update ($new_instance, $old_instance) {
		
		$instance = $old_instance;
		
		$instance['current_mailing_list'] = esc_attr($new_instance['current_mailing_list']);
		
		$instance['signup_text'] = esc_attr($new_instance['signup_text']);
		
		$instance['title'] = esc_attr($new_instance['title']);
		
		return $instance;
		
	}
	
	/**
	 * @author James Lafferty
	 * @since 0.1
	 */
	
	public function widget ($args, $instance) {
		
		extract($args);
		
		if ($this->hash_mailing_list_id($this->number) == $_COOKIE[$this->id_base . '-' . $this->number] || false == $this->ns_mc_plugin->get_mcapi()) {
			
			return 0;
			
		} else {
			
			$widget = $before_widget . $before_title . $instance['title'] . $after_title;
			
			if ($this->successful_signup) {
				
				$widget .= $this->signup_success_message;
				
			} else {
			
				$widget .= '<form action="' . $_SERVER['REQUEST_URI'] . '" id="' . $this->id_base . '_form-' . $this->number . '" method="post">' . $this->subscribe_errors . '<label>' . __('Email Address :', 'mailchimp-widget') . '</label><input type="hidden" name="ns_mc_number" value="' . $this->number . '" /><input type="text" name="' . $this->id_base . '_email" /><input type="submit" name="' . __($instance['signup_text'], 'mailchimp-widget') . '" value="' . __($instance['signup_text'], 'mailchimp-widget') . '" /></form><script type="text/javascript"> jQuery(\'#' . $this->id_base . '_form-' . $this->number . '\').ns_mc_widget({"url" : "' . $_SERVER['PHP_SELF'] . '", "cookie_id" : "'. $this->id_base . '-' . $this->number . '", "cookie_value" : "' . $this->hash_mailing_list_id() . '", "loader_graphic" : "' . $this->default_loader_graphic . '"}); </script>';
				
			}

			$widget .= $after_widget;

			echo $widget;
			
		}
		
	}
	
	/**
	 * @author James Lafferty
	 * @since 0.1
	 */
	
	private function hash_mailing_list_id () {
		
		$options = get_option($this->option_name);
		
		$hash = md5($options[$this->number]['current_mailing_list']);
		
		return $hash;
		
	}
	
	/**
	 * @author James Lafferty
	 * @since 0.1
	 */
	
	private function get_current_mailing_list_id ($number = null) {
		
		$options = get_option($this->option_name);
		
		return $options[$number]['current_mailing_list'];
		
	}
	
}

?>