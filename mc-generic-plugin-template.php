<?php

/*
Plugin name: Generic plugin template
Plugin version: 1.0
Author name: Marian Cerny
Author URL: http://mariancerny.com
Description: A generic object-oriented plugin template with easy settings configuration and a default Javascript file.
*/

class mc_gp_plugin
{


// *******************************************************************
// ------------------------------------------------------------------
//					CONSTRUCTOR AND INITIALIZATION
// ------------------------------------------------------------------
// *******************************************************************


	var $settings = array(
		'general' => array(
			'title' => 'General settings',
			'output_function' => 'output_settings_section_general',
			'fields' => array(
				'first_setting' => array(
					'title' => 'First setting',
					'type' => 'number',
					'value' => 15,
					'description' => 'The first setting is a number.'
				),
				'second_setting' => array(
					'title' => 'Second setting',
					'type' => 'text',
					'value' => 'random text',
					'description' => 'The second setting. Plain text'
				),
			),
		),
		'non_general' => array(
			'title' => 'Non General settings',
			'output_function' => 'output_settings_section_general',
			'fields' => array(
				'third_setting' => array(
					'title' => 'Third setting',
					'type' => 'checkbox',
					'value' => true,
					'description' => 'The third setting. A lovely checkbox !'
				),
				'fourth_setting' => array(
					'title' => 'Fourth setting',
					'description' => 'The fourth setting. Radio buttons !',
					'type' => 'radio',
					'value' => 'option_one',
					'options' => array(
						'option_one' => 'Option one',
						'option_two' => 'Option two',
						'option_three' => 'Option three',
					),
				)
			),
		),
	);
	

	var $plugin_name;
	var $plugin_slug;
	var $plugin_url;
	var $plugin_version;
	var $plugin_namespace;
	
	
	function __construct()
	{	
		/* SET UP PLUGIN VARIABLES */
		$this->plugin_name = 'Generic plugin';
		$this->plugin_slug = 'generic-plugin';
		$this->plugin_url = plugins_url( '', __FILE__ );
		$this->plugin_version = '1.0';
		$this->plugin_namespace = 'mc_gp_';
		
		/* GET SETTINGS FROM DATABASE */
		$this->get_settings_from_db();			
		
		/* ADD ACTIONS, SHORTCODES AND FILTERS */
  		add_action( 'admin_menu', array( $this, 'register_settings') );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_and_scripts' ) );	
	}


// *******************************************************************
// ------------------------------------------------------------------
//							PUBLIC FUNCTIONS
// ------------------------------------------------------------------
// *******************************************************************





// *******************************************************************
// ------------------------------------------------------------------
//							PRIVATE FUNCTIONS
// ------------------------------------------------------------------
// *******************************************************************
	
	/* ENQUEUE STYLES AND SCRIPTS */
	function enqueue_styles_and_scripts()
	{
		// ENQUEUE JQUERY
		wp_enqueue_script( 'jquery' );
		
		// ENQUEUE PLUGIN-NAME.JS SCRIPT
		wp_enqueue_script( 
			$this->plugin_slug, 
			$this->plugin_url . $this->plugin_slug . '.js',
			array( 'jquery' ),
			$this->plugin_version
		);
		
		// PASS PLUGIN SETTINGS TO THE SCRIPT
		$a_ajax_vars = array(
			'settings' => $this->settings,
		);		
		wp_localize_script(
			$this->plugin_slug, 
			$this->plugin_namespace . 'ajax_vars', 
			$a_ajax_vars
		);
	}
	
	
	/* ASSIGN SETTINGS FROM PLUGIN OPTIONS TO THE SETTINGS ARRAY */
	private function get_settings_from_db()
	{
		foreach ( $this->settings as $s_setting_key => $a_setting )
		{
			foreach( $a_setting['fields'] as $s_field_key => $m_field ) 
			{		
				$this->settings[$s_setting_key]['fields'][$s_field_key]['value'] 
					= get_option( $this->plugin_namespace . $s_field_key, $m_field['value'] );
				
			}
		}
	}
	
	
	/* RETURN THE VALUE OF A GIVEN SETTING FROM THE SETTINGS ARRAY */
	private function get_setting( $s_field_name )
	{
		foreach ( $this->settings as $a_setting )
		{
			if ( array_key_exists( $s_field_name, $a_setting['fields'] ) )
				return $a_setting['fields'][$s_field_name]['value'];
		}
		return false;
	}


// *******************************************************************
// ------------------------------------------------------------------
//							OPTIONS MENU
// ------------------------------------------------------------------
// *******************************************************************
	
	
	/* CREATE AN ENTRY IN THE SETTINGS MENU AND REGISTER/OUTPUT ALL SETTINGS */
	function register_settings() 
	{
		add_options_page(
			$this->plugin_name, 
			$this->plugin_name, 
			'manage_options', 
			$this->plugin_slug, 
			array( $this, 'output_options_page' )
		);
		
		// CREATE OPTIONS SECTIONS		
		foreach ( $this->settings as $s_section_name => $a_settings_section )
		{
				
			add_settings_section( 
				$this->plugin_namespace . $s_section_name, 
				$a_settings_section['title'], 
				array( $this, 'output_settings_section_general' ), 
				$this->plugin_slug
			);
			
			// CREATE OPTIONS FIELDS AND REGISTER SETTINGS
			foreach( $a_settings_section['fields'] as $s_field_name => $a_settings_field )
			{				
				add_settings_field(
					$this->plugin_namespace . $s_field_name, 
					$a_settings_field['title'],
					array($this, 'output_option'), 
					$this->plugin_slug, 
					$this->plugin_namespace . $s_section_name,
					array(
						'type' => $a_settings_field['type'],
						'name' => $s_field_name,
						'section' => $s_section_name,
						'description' => $a_settings_field['description'],
					)
				);
			
				register_setting( $this->plugin_namespace . 'settings', $this->plugin_namespace . $s_field_name );
			}
			
		}
		
	}
	
	/* OUTPUT OPTIONS PAGE */
	function output_options_page()
	{
		?>
		<div class="wrap">
		<h2><?php echo $this->plugin_name; ?> Settings</h2>
		
        <p>Generic plugin settings</p>
		
		<form method="post" action="options.php">
		
			<?php
			
			foreach ( $this->settings as $s_section_name => $a_settings_section )
				settings_fields( $this->plugin_namespace . 'settings' );
			
			do_settings_sections( $this->plugin_slug  );     
			submit_button(); 
			
			?>
		
		</form>
		</div>
		<?php
	}
	
	/* OUTPUT GENERAL SETTINGS SECTION */
	function output_settings_section_general()
	{
		echo '';
	}
	
	/* OUTPUT OPTION */
	function output_option( $args )
	{
		if ( $args['type'] == 'radio' )
		{
			$orig_value = get_option( $this->plugin_namespace . $args['name'] );
			
			// echo "<pre>"; print_r( $this->settings[$args['section']]['fields'][$args['name']]['options'] ); echo "</pre>";
			
			// echo $args['name'];
			
			foreach ( $this->settings[$args['section']]['fields'][$args['name']]['options'] as $key => $value )
			{
				$s_output = "<label for='" . $this->plugin_namespace . $key . "'>";
				$s_output .= "<input 
				type='radio' 
				name='". $this->plugin_namespace . $args['name'] ."' 
				value='" . $key . "' 
				id='" . $this->plugin_namespace . $key . "'";
						
				$s_output .= checked( $orig_value, $key, false );
				$s_output .= "'/>" . $value . " </label> <br/>";
			
				echo $s_output;
			}
		}
		else 
		{
			$s_output = "<input 
				name='" . $this->plugin_namespace . $args['name'] ."'
				id='" . $this->plugin_namespace . $args['name'] ."'
				type='" . $args['type']."'";
			
			if ( $args['type'] == 'checkbox' )
				$s_output .= checked( 'on', get_option( $this->plugin_namespace . $args['name'], false ), false );
			else
				$s_output .= "value='".get_option( $this->plugin_namespace . $args['name'] )."'";
				
			$s_output .= "/> (" . $args['description'] . ")";
			
			echo $s_output;	
		}
		
	}


}


// *******************************************************************
// ------------------------------------------------------------------
//						FUNCTION SHORTCUTS
// ------------------------------------------------------------------
// *******************************************************************

$mc_gp_plugin = new mc_gp_plugin();



?>