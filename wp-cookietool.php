<?php

/*
Javascript work based on the work by Emilio Cobos: https://github.com/ecoal95/CookieTool
*/

define( 'WP_COOKIE_TOOL_PATH', plugin_dir_path(__FILE__) ); //Includes trailing slash!!!

class wp_cookie_tool {
	protected $textdomain = "wp-cookie-tool";
	protected $prefix = "wp_ct";
	protected $longname = "wp_cookie_tool";

    /**
     * Returns the options array
     *
     * @since WP Cookie Tool 0.1
     */
    function get_options() {
    	$saved = (array) get_option($this->longname . '_options');
    	$defaults = array(
    	                  'load_css' => 'on',
    	                  'link' => 'http://example.com/cookies',
    	                  'link_name' => 'Cookie Policy',
    	                  'panel_class' => 'cookietool-message-top',
    	                  'message' => __('The message you are going to show', $this->textdomain),
    	                  'agreetext' => __('I agree', $this->textdomain),
    	                  'declinetext' => __('I disagree', $this->textdomain)

    	                  );

    	$defaults = apply_filters($this->prefix. '_default_options', $defaults);

    	$options = wp_parse_args($saved, $defaults);
    	$options = array_intersect_key($options, $defaults);
    	return $options;
    }
}


class wp_cookie_tool_options extends wp_cookie_tool {
	function __construct() {
		add_action('admin_init', array(&$this, 'init'));
		add_action('admin_menu', array(&$this, 'add_menu_page'));
	}

	function add_menu_page() {
		$options_page = add_options_page(
                __('WP Cookie Tool Options', $this->textdomain), // Name of page (html title)
                __('WP Cookie Tool Options', $this->textdomain), // Label in menu
                'manage_options', // Capability required
                $this->longname . '_options_page', // Menu slug, used to uniquely identify the page
                array(&$this, 'render_page')
                );
	}

	function init() {
		register_setting(
                $this->longname . '_options', //Options name
                $this->longname . '_options', //DB entry
                array($this, 'validate') //Validate callback
                );

    // Register our settings field group
		add_settings_section(
                $this->longname . '_general', // Unique identifier for the settings section
                '', // Section title (we don't want one)
                '__return_false', // Section callback (we don't want anything)
                $this->longname . '_options_page' // Menu slug, used to uniquely identify the page; see add_menu_page()
                );


        //General fields
		add_settings_field(
		                   $this->longname.'_load_css',
		                   __('Load css?', $this->textdomain),
		                   array($this, 'render_load_css_field'),
		                   $this->longname . '_options_page',
		                   $this->longname . '_general'
		                   );
		add_settings_field(
		                   $this->longname.'_link',
		                   __('Cookie policy page link', $this->textdomain),
		                   array($this, 'render_link_field'),
		                   $this->longname . '_options_page',
		                   $this->longname . '_general'
		                   );
		add_settings_field(
		                   $this->longname.'_link_name',
		                   __('Cookie policy page title', $this->textdomain),
		                   array($this, 'render_link_name_field'),
		                   $this->longname . '_options_page',
		                   $this->longname . '_general'
		                   );
		add_settings_field(
		                   $this->longname.'_panel_class',
		                   __('Panel class', $this->textdomain),
		                   array($this, 'render_panel_class_field'),
		                   $this->longname . '_options_page',
		                   $this->longname . '_general'
		                   );
		add_settings_field(
		                   $this->longname.'_message',
		                   __('Message', $this->textdomain),
		                   array($this, 'render_message_field'),
		                   $this->longname . '_options_page',
		                   $this->longname . '_general'
		                   );
		add_settings_field(
		                   $this->longname.'_agreetext',
		                   __('Agree text', $this->textdomain),
		                   array($this, 'render_agreetext_field'),
		                   $this->longname . '_options_page',
		                   $this->longname . '_general'
		                   );
		add_settings_field(
		                   $this->longname.'_declinetext',
		                   __('Decline text', $this->textdomain),
		                   array($this, 'render_declinetext_field'),
		                   $this->longname . '_options_page',
		                   $this->longname . '_general'
		                   );
	}

    /**
     * Renders the options page.
     *
     * @since WP Cookie Tool 0.1
     */
    function render_page() {
    	?>
    	<div class="wrap">

    		<h2><?php _e('WP Cookie Tool Options', $this->textdomain) ?></h2>
    		<form method="post" action="options.php">
    			<?php
    			settings_fields($this->longname . '_options');
    			do_settings_sections($this->longname . '_options_page');
    			submit_button();
    			?>
    		</form>
    	</div>
    	<?php
    }

    /**
     * Renders the load css checkbox
     *
     * @since WP Cookie Tool 0.1
     */
    function render_load_css_field() {
    	$options = $this->get_options();
    	?>
    	<label for="load_css" class="description">
    		<input type="checkbox" name="wp_cookie_tool_options[load_css]" id="load_css" <?php checked('on', $options['load_css']); ?> />
    		<?php _e('Uncheck if you don\'t want to include the default styles.', $this->textdomain); ?>
    	</label>
    	<?php
    }

    /**
     * Renders the input field for the link
     *
     * @since WP Cookie Tool 0.1
     */
    function render_link_field() {
    	$options = $this->get_options();
    	?>
    	<input type="url" name="wp_cookie_tool_options[link]" id="link" value="<?php echo esc_attr($options['link']); ?>" />
    	<p class="description"><?php _e('Place here your privacy/cookie policy page URL.', $this->textdomain); ?></p>
    	<?php
    }

    /**
     * Renders the input field for the link name
     *
     * @since WP Cookie Tool 0.1
     */
    function render_link_name_field() {
    	$options = $this->get_options();
    	?>
    	<input type="text" name="wp_cookie_tool_options[link_name]" id="link" value="<?php echo esc_attr($options['link_name']); ?>" />
    	<p class="description"><?php _e('Place here your privacy/cookie policy page URL.', $this->textdomain); ?></p>
    	<?php
    }
    /**
     * Renders the input field for the panel class
     *
     * @since WP Cookie Tool 0.1
     */
    function render_panel_class_field() {
    	$options = $this->get_options();
    	?>
    	<input type="text" name="wp_cookie_tool_options[panel_class]" id="panel_class" value="<?php echo esc_attr($options['panel_class']); ?>" />
    	<p class="description"><?php _e('Panel\'s CSS class name.', $this->textdomain); ?></p>
    	<?php
    }
    /**
     * Renders the input field for the message
     *
     * @since WP Cookie Tool 0.1
     */
    function render_message_field() {
    	$options = $this->get_options();
    	?>
    	<input type="text" name="wp_cookie_tool_options[message]" id="message" value="<?php echo esc_attr($options['message']); ?>" />
    	<p class="description"><?php _e('This is the text you show to your visitors.', $this->textdomain); ?></p>
    	<?php
    }
    /**
     * Renders the input field for the agreetext
     *
     * @since WP Cookie Tool 0.1
     */
    function render_agreetext_field() {
    	$options = $this->get_options();
    	?>
    	<input type="text" name="wp_cookie_tool_options[agreetext]" id="agreetext" value="<?php echo esc_attr($options['agreetext']); ?>" />
    	<p class="description"><?php _e('Text to agree.', $this->textdomain); ?></p>
    	<?php
    }

    /**
     * Renders the input field for the declinetext
     *
     * @since WP Cookie Tool 0.1
     */
    function render_declinetext_field() {
    	$options = $this->get_options();
    	?>
    	<input type="text" name="wp_cookie_tool_options[declinetext]" id="declinetext" value="<?php echo esc_attr($options['declinetext']); ?>" />
    	<p class="description"><?php _e('Text to decline.', $this->textdomain); ?></p>
    	<?php
    }

    function validate($input) {
    	$output = array();

    	if (isset($input['load_css'])) {
    		$output['load_css'] = 'on';
    	} else {
    		$output['load_css'] = 'off';
    	}

    	if (isset($input['link'])) {
    		$output['link'] = esc_url_raw($input['link']);
    	}

    	if (isset($input['link_name'])) {
    		$output['link_name'] = esc_html($input['link_name']);
    	}

    	if (isset($input['panel_class'])) {
    		$output['panel_class'] = esc_attr($input['panel_class']);
    	}

    	if (isset($input['message'])) {
    		$output['message'] = esc_html($input['message']);
    	}

    	if (isset($input['agreetext'])) {
    		$output['agreetext'] = esc_html($input['agreetext']);
    	}

    	if (isset($input['declinetext'])) {
    		$output['declinetext'] = esc_html($input['declinetext']);
    	}

    	return apply_filters($this->longname . '_options_validate', $output, $input);
    }

}

class wp_cookie_tool_init extends wp_cookie_tool {

	function render_cookie_div() {
		return '<div id="cookietool-settings"></div>';
	}

	function __construct() {
		add_action('wp_enqueue_scripts', array(&$this, 'add_styles'));
		add_action('wp_footer', array(&$this, 'config_script'), 100);
		load_plugin_textdomain( $this->textdomain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		add_shortcode('cookie-div', array(&$this, 'render_cookie_div'));
	}

	function add_styles() {
		$options = $this->get_options();
		if ($options['load_css'] == "on") {
			wp_enqueue_style($this->prefix . 'css', plugins_url('/cookietool.css', __FILE__));
		}
		wp_enqueue_script( $this->prefix . 'js', plugins_url('/cookietool.js', __FILE__), false, false, true );
        // wp_enqueue_script( $this->prefix . 'js', plugins_url('/cookieconfig.js', __FILE__), false, false, true );
	}

	function config_script() {
		$options = $this->get_options();

		$config_array = array(
		                      "link" => $options['link'],
		                      "linkName" => $options['link_name'],
		                      "panelClass" => $options['panel_class'],
		                      "message" => $options['message'],
		                      "agreetext" => $options['agreetext'],
		                      "declinetext" => $options['declinetext'],
		                      "agreeStatusText" => __('You currently <strong>allow</strong> cookies in this site. <button type="button" class="button-basic" data-action="decline">Click here to disallow cookies</button>', $this->textdomain),
		                      "disagreeStatusText" => __('You currently <strong>disallow</strong> cookies in this site. <button type="button" class="button-basic" data-action="agree">Click here to allow cookies</button>', $this->textdomain),
		                      "notSetText" => __('You haven\'t yet established your configuration. <button type="button" class="button-basic" data-action="agree">Click here to allow cookies</button> or <button type="button" class="button-basic" data-action="decline">click here to disallow cookies</button>', $this->textdomain)
		                      );

		                      ?>

		                      <script type="text/javascript">
		                      /* <![CDATA[ */
		                      CookieTool.Config.set(<?php echo json_encode($config_array) ?>);
		                      CookieTool.API.ask();
		                      if( document.getElementById('cookietool-settings') ) {
		                      	CookieTool.API.displaySettings(document.getElementById('cookietool-settings'));
		                      }
		                      /* ]]> */
		                      </script>
		                      <?php
		                  }
		              }

		              new wp_cookie_tool_init;
		              new wp_cookie_tool_options;


