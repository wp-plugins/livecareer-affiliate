<?php
/*
	Plugin Name: LiveCareer Affiliate Plugin
	Plugin URI: https://affiliate.livecareer.com/
	Description: Implements a shortcode and widget to conveniently add the LiveCareer affiliate resume builder tool and links to your site or blog.
	Author: LiveCareer
	Version: 1.0
	Author URI: http://www.livecareer.com
	License: GPL
	Text Domain: livecareer_affiliate
	Domain Path: /lang
*/


 /*  Copyright 2014 Calen Lopata  (email : calen.lopata@gmail.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



/**
 * Class to conveniently set up the settings page.
 */

class LiveCareerAffiliateSettingsPage
{
	/******************************************************************************************************
	 * Copy text used within this plugin.
	 * Edit the text below if needed in order to update the text for the various plugin pages.
	 */

	/**
	 * Plugin listing text
	 */
	 
	// Settings text. Visible on the Plugins page, just below the plugin Description
	// (note: the Descripion itself is at the top of this file (line 5).
	private $PluginSettingsPageLinkText = 'Settings';
	
	// Menu label (on left side of Wordpress admin area, under the Settings section).
	private $SettingsMenuLabel = 'LiveCareer Affiliate';
		
	/**
	 * Plugin Settings page text
	 */

	private $SettingsPageHeader = 'LiveCareer Affiliate Settings';
	private $ReferrerIDSectionTitle = 'Referrer ID';
	private $ReferrerIDInputLabel = 'Your Referrer ID:';
	
	private $ReferrerIDSectionText = <<<END_ReferrerIDSectionText
	
	<p>Enter your referrer ID below in order to receive credit 
		for your affiliate referrals. You can sign up for the LiveCareer 
		Affiliate Program at 
		<a href="https://affiliate.livecareer.com/membership/affiliate.aspx" target="_blank">https://affiliate.livecareer.com/membership/affiliate.aspx</a>.
	</p>

END_ReferrerIDSectionText;

	
	private $HelpSectionTitle = 'Plugin Help';
	
	private $PluginHelpSectionText = <<<END_PluginHelpSectionText
	
	<h4>Shortcode</h4>
	<p>With this plugin enabled, the following shortcode will insert the LiveCareer Resume Builder into your site: 
	<blockquote><code style="margin-left:10px;">[livecareer_affiliate_link]</code></blockquote>
	</p>
	<p>Given the width of the Resume Builder, LiveCareer recommends that you place the shortcode on a page that 
		is configured with a single-column-width layout (this layout allows the main page content to span the entire width of the page).
	</p>
	<h4>Widget</h4>
	<p>The LiveCareer Affiliate Widget is also available with this plugin enabled. This widget adds a LiveCareer affiliate image link to your site.
		When adding the widget to your pages (via the <a href="widgets.php">Widgets page</a>), you can choose which size of an image fits best on your page.
	</p>

END_PluginHelpSectionText;

	/*
	 * Done with the copy text.
	 ******************************************************************************************************/

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
	
	/* Static string identifiers */
	private $page_slug = 'live-career-affiliate-admin';
	private $option_group  = 'live_career_affiliate_group';
	private $option_name = 'live_career_affiliate';
	

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
		
		// Add settings link on plugin page
		$plugin = plugin_basename(__FILE__); 
		add_filter("plugin_action_links_$plugin", array( $this, 'livecareer_affiliate_plugin_page_link' ) );
		
		add_shortcode( 'livecareer_affiliate_link', array( $this, 'livecareer_affiliate_link_handler' ) );
		add_thickbox(); // For modal windows.
    }


	public function livecareer_affiliate_plugin_page_link($links) 
	{ 
		$settings_link = '<a href="options-general.php?page=' . $this->page_slug . '">' . $this->PluginSettingsPageLinkText . '</a>'; 
		array_unshift($links, $settings_link); 
		return $links; 
	}

	/**
	 * Function livecareer_affiliate_link_handler processes our shortcode.
	 * @param $attributes
	 * @param null $content
	 * @return string
	 */
	public function livecareer_affiliate_link_handler( $attributes, $content = null )
	{

		$output = '';

		$this->options = get_option( $this->option_name );
		
		// Standard procedure for gathering shortcode attributes.
		extract
		( 
			shortcode_atts
			(
				// Defaults array.
				array
				(
					'ref_id' => $this->options['referrer_id']
					//, 'attr_2' => 'attribute 2 default'
				)
				// User-provided attribute values.
				, $attributes
				, 'livecareer_affiliate_link'
			) 
		);
		
		// Generate the HTML for our affiliate link.
		$output = $this->affiliate_link_html($ref_id);

		return $output;
	}

	 /**
	 * Function affiliate_link_html
	 * Output an iframe with the resume builder.
	 */
	public function affiliate_link_html($ref_id)
	{
		return '<iframe src="//resume.livecareer.com/builder/iframebuilder-partner.aspx?ref=' . $ref_id . '" frameborder="0" height="1000px" width="980px"></iframe>';
	}

	
    /**
     * Add options page
     */
    public function add_plugin_page()
    {
		// This page will be under "Settings"
		add_options_page(
		    $this->SettingsPageHeader, 
		    $this->SettingsMenuLabel, 
		    'manage_options', 
		    $this->page_slug, 
		    array( $this, 'create_admin_page' )
		);
		
		// Original spec required a separate menu item; no longer needed.
		//add_menu_page( $this->SettingsPageHeader, $this->SettingsMenuLabel, 'manage_options', $this->page_slug, array( $this, 'create_admin_page' ), 'dashicons-id-alt' );
		//add_submenu_page( $this->page_slug, 'Settings', 'Settings', 'manage_options', 'livecareer-affiliate-settings-link', array( $this, 'create_admin_page' ) );
		
		//add_submenu_page( $this->page_slug, $SettingsPageHeader, $menu_title, $capability, $menu_slug, $function );
    }
	

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( $this->option_name );
        ?>
        <div class="wrap">
			<img src="https://www.livecareer.com/images/uploaded/plugins/logo.png" />
            <h2><?=$this->SettingsPageHeader?></h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( $this->option_group );   
                do_settings_sections( $this->page_slug );
                submit_button(); 
                do_settings_sections( $this->page_slug . '_help' );
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            $this->option_group, // Option group
            $this->option_name, // Option name
            array( $this, 'sanitize' ) // Sanitize
        );


        add_settings_section(
            'setting_section_referrer_id', // ID
            $this->ReferrerIDSectionTitle, // Title
            array( $this, 'print_referrer_id_info' ), // Callback
            $this->page_slug // Page
        ); 
		
        add_settings_field(
            'referrer_id', // ID
            $this->ReferrerIDInputLabel, // Title 
            array( $this, 'referrer_id_callback' ), // Callback
            $this->page_slug, // Page
            'setting_section_referrer_id' // Section           
        );
		
        add_settings_section(
            'setting_section_id', // ID
            $this->HelpSectionTitle, // Title
            array( $this, 'print_section_info' ), // Callback
            $this->page_slug . '_help' // Page
        );  

        //add_settings_field(
        //    'title', 
        //    'Title', 
        //    array( $this, 'title_callback' ), 
        //    $this->page_slug, 
        //    'setting_section_id'
        //);      
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['referrer_id'] ) )
		{
            $new_input['referrer_id'] = absint( $input['referrer_id'] );
		}

        //if( isset( $input['title'] ) )
        //    $new_input['title'] = sanitize_text_field( $input['title'] );

        return $new_input;
    }

    /** 
     * Print the referrer ID text
     */
    public function print_referrer_id_info()
    {
        print $this->ReferrerIDSectionText;
    }

    /** 
     * Print the help/info text
     */
    public function print_section_info()
    {
        print $this->PluginHelpSectionText;
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function referrer_id_callback()
    {
        printf(
            '<input type="text" id="referrer_id" name="' . $this->option_name . '[referrer_id]" value="%s" />',
            isset( $this->options['referrer_id'] ) ? esc_attr( $this->options['referrer_id']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    //public function title_callback()
    //{
    //    printf(
    //        '<input type="text" id="title" name="' . $this->option_name . '[title]" value="%s" />',
    //        isset( $this->options['title'] ) ? esc_attr( $this->options['title']) : ''
    //    );
    //}
}

$my_settings_page = new LiveCareerAffiliateSettingsPage();



/*************
 * Widget section
 */
 
 class wp_livecareer_affiliate_widget extends WP_Widget {

	// constructor
	function wp_livecareer_affiliate_widget() {
		$widget_ops = array('description' => __('Include a LiveCareer affiliate banner.', 'wp_widget_plugin'));
		//$control_ops = array('width' => 400, 'height' => 300);
		$control_ops = array();
		parent::WP_Widget(false, $name = __('LiveCareer Affiliate Widget', 'wp_widget_plugin'), $widget_ops, $control_ops );
	}

	// widget form creation
	function form($instance) {	

		// Check values
		if( $instance) {
			$title = esc_attr($instance['title']);
			$select = esc_attr($instance['banner_size']);
		} 
		else 
		{
			$title = '';
			$select = ''; 
		}
		?>

		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title (optional)', 'wp_widget_plugin'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id('banner_size'); ?>"><?php _e('Affiliate Image Link Size', 'wp_widget_plugin'); ?></label>
		<select name="<?php echo $this->get_field_name('banner_size'); ?>" id="<?php echo $this->get_field_id('banner_size'); ?>" class="widefat">
		<?php
		$options = array('Leaderboard (728x90)' => '728x90', 'Rectangle (300x250)' => '300x250', 'Skyscraper (160x600)' => '160x600');
		foreach ($options as $name => $value) {
		echo '<option value="' . $value . '" id="' . $value . '"', $select == $value ? ' selected="selected"' : '', '>', $name, '</option>';
		}
		?>
		</select>
		</p>
		<?php
	}

	// widget update
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		// Fields
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['banner_size'] = strip_tags($new_instance['banner_size']);
		return $instance;
	}

	// widget display
	function widget($args, $instance) {
		extract( $args );
		// these are the widget options
		$title = apply_filters('widget_title', $instance['title']);
		
		switch ($instance['banner_size'])
		{
			case '300x250':
				$image_url = '<img src="//www.livecareer.com/images/uploaded/plugins/300x250.jpg" border="0" width="300" height="250" alt="Click to build your resume">';
				break;
			case '160x600':
				$image_url = '<img src="//www.livecareer.com/images/uploaded/plugins/160x600.jpg" border="0" width="160" height="600" alt="Click to build your resume">';
				break;
			default:
				//728x90
				$image_url = '<img src="//www.livecareer.com/images/uploaded/plugins/728x90.jpg" border="0" width="728" height="90" alt="Click to build your resume">';
				break;
		}
		
		
		echo $before_widget;
		// Display the widget
		echo '<div class="widget-text wp_widget_plugin_box">';

		// Check if title is set
		if ( $title ) {
		  echo $before_title . $title . $after_title;
		}
		
		echo '<a href="//resume.livecareer.com/builder/iframebuilder-partner.aspx?ref=' . $ref_id . '&TB_iframe=true&width=1000&height=600" class="thickbox">' . $image_url . '</a>';
		
		echo '</div>';
		echo $after_widget;
	}
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("wp_livecareer_affiliate_widget");'));
?>