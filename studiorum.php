<?php
	/*
	 * Plugin Name: Studiorum
	 * Description: Studiorum is latin for 'Higher Education'. This plugin is the 'core' and sets up the environment for the other Studiorum add-ons.
	 * Version:     0.1
	 * Plugin URI:  #
	 * Author:      UBC, CTLT, Richard Tape
	 * Author URI:  http://ubc.ca/
	 * Text Domain: studiorum
	 * License:     GPL v2 or later
	 * Domain Path: languages
	 *
	 * studiorum is free software: you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation, either version 2 of the License, or
	 * any later version.
	 *
	 * studiorum is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with studiorum. If not, see <http://www.gnu.org/licenses/>.
	 *
	 * @package studiorum
	 * @category Core
	 * @author Richard Tape
	 * @version 0.1.0
	 */

	if( !defined( 'ABSPATH' ) ){
		die( '-1' );
	}


	if( !class_exists( 'Studiorum' ) ) :

		final class Studiorum
		{

			// private instance of the Studiorum class (one 'true' class)
			private static $instance;


			/**
			 * Main Studiorum Instance
			 *
			 * Insures that only one instance of Studiorum exists in memory at any one
			 * time. Also prevents needing to define globals all over the place.
			 *
			 * @since 0.1
			 * @static
			 * @staticvar array $instance
			 * @uses Studiorum::setup_constants() Setup the constants needed
			 * @uses Studiorum::includes() Include the required files
			 * @uses Studiorum::load_textdomain() load the language files
			 * @see Studiorum()
			 * @return The one true Studiorum
			 */

			public static function instance()
			{

				if( !isset( self::$instance ) && !( self::$instance instanceof Studiorum ) )
				{

					self::$instance 			= new Studiorum;
					self::$instance->setup_constants();
					self::$instance->includes();
					self::$instance->load_textdomain();

					self::$instance->roles      = new Studiorum_Roles();

				}

				return self::$instance;

			}/* instance() */


			/**
			 * Throw error on object clone
			 *
			 * The whole idea of the singleton design pattern is that there is a single
			 * object therefore, we don't want the object to be cloned.
			 *
			 * @since 0.1
			 * @access protected
			 * @return void
			 */

			public function __clone()
			{

				// Cloning instances of the class is forbidden
				_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'studiorum' ), '0.1' );

			}/* __clone() */

			/**
			 * Disable unserializing of the class
			 *
			 * @since 0.1
			 * @access protected
			 * @return void
			 */

			public function __wakeup()
			{

				// Unserializing instances of the class is forbidden
				_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'studiorum' ), '0.1' );

			}/* __wakeup() */


			/**
			 * Setup plugin constants
			 *
			 * @access private
			 * @since 0.1
			 * @return void
			 */

			private function setup_constants()
			{
				
				// Plugin version
				if( !defined( 'STUDIORUM_VERSION' ) ){
					define( 'STUDIORUM_VERSION', '0.1.0' );
				}

				// Plugin Folder Path
				if( !defined( 'STUDIORUM_PLUGIN_DIR' ) ){
					define( 'STUDIORUM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
				}

				// Plugin Folder URL
				if( !defined( 'STUDIORUM_PLUGIN_URL' ) ){
					define( 'STUDIORUM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
				}

				// Plugin Root File
				if( !defined( 'STUDIORUM_PLUGIN_FILE' ) ){
					define( 'STUDIORUM_PLUGIN_FILE', __FILE__ );
				}

			}/* setup_constants() */


			/**
			 * Include required files
			 *
			 * @access private
			 * @since 0.1
			 * @return void
			 */

			private function includes()
			{

				global $Studiorum_Options;

				require_once STUDIORUM_PLUGIN_DIR . 'includes/class-studiorum-utils.php';
				require_once STUDIORUM_PLUGIN_DIR . 'includes/class-studiorum-roles.php';
				require_once STUDIORUM_PLUGIN_DIR . 'includes/class-studiorum-addon.php';


				if( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) )
				{

					require_once STUDIORUM_PLUGIN_DIR . 'includes/admin/class-studiorum-dashboard-setup.php';
					
					if( !class_exists( 'AdminPageFramework' ) ){
						require_once STUDIORUM_PLUGIN_DIR . 'includes/admin/libraries/settings/library/admin-page-framework.min.php';
					}

					require_once STUDIORUM_PLUGIN_DIR . 'includes/admin/class-studiorum-options.php';
				
				}
				else
				{

					// require_once STUDIORUM_PLUGIN_DIR . 'includes/process-download.php';
					// require_once STUDIORUM_PLUGIN_DIR . 'includes/theme-compatibility.php';

				}

				require_once STUDIORUM_PLUGIN_DIR . 'includes/install.php';

				// Allow us to hook in here
				do_action( 'studiorum_after_includes' );

			}/* includes() */


			/**
			 * Loads the plugin language files
			 *
			 * @access public
			 * @since 1.4
			 * @return void
			 */

			public function load_textdomain()
			{

				// Set filter for plugin's languages directory
				$studiorum_lang_dir = dirname( plugin_basename( STUDIORUM_PLUGIN_FILE ) ) . '/languages/';
				$studiorum_lang_dir = apply_filters( 'studiorum_languages_directory', $studiorum_lang_dir );

				// Traditional WordPress plugin locale filter
				$locale        = apply_filters( 'plugin_locale',  get_locale(), 'studiorum' );
				$mofile        = sprintf( '%1$s-%2$s.mo', 'studiorum', $locale );

				// Setup paths to current locale file
				$mofile_local  = $studiorum_lang_dir . $mofile;
				$mofile_global = WP_LANG_DIR . '/studiorum/' . $mofile;

				if( file_exists( $mofile_global ) )
				{

					// Look in global /wp-content/languages/studiorum folder
					load_textdomain( 'studiorum', $mofile_global );

				}
				elseif( file_exists( $mofile_local ) )
				{

					// Look in local /wp-content/plugins/studiorum/languages/ folder
					load_textdomain( 'studiorum', $mofile_local );

				}
				else
				{

					// Load the default language files
					load_plugin_textdomain( 'studiorum', false, $studiorum_lang_dir );

				}

			}/* load_textdomain() */

		}/* class Studiorum */

	endif;

	/**
	 * The main function responsible for returning the one true Studiorum
	 * Instance to functions everywhere.
	 *
	 * Use this function like you would a global variable, except without needing
	 * to declare the global.
	 *
	 * Example: <?php $Studiorum = Studiorum(); ?>
	 *
	 * @since 0.1.0
	 * @return object The one true Studiorum Instance
	 */

	function Studiorum()
	{

		return Studiorum::instance();

	}/* Studiorum() */

	// Get Studiorum Running
	add_action( 'plugins_loaded', 'Studiorum', 1 );


	if( !function_exists( 'get_studiorum_option' ) ) :

		/**
		 * Produce a generic helper function to get our options
		 *
		 * @since 0.1
		 *
		 * @param string $sectionID The section ID (key in the main array)
		 * @param string $fieldID The FieldID - the individual setting
		 * @param string $default What to return if not found
		 * @return mixed $data - the found setting or the default
		 */

		function get_studiorum_option( $sectionID = false, $fieldID = false, $default = false )
		{

			// We *must* provide a section ID and field ID (much faster checking)
			if( !$sectionID || !$fieldID ){
				return new WP_Error( '1', __( 'You must provide a section ID and Field ID', 'studiorum' ) );
			}

			$mainOptionName = 'Studiorum_Options'; // Same as the instantiated class name above

			$sectionAndField = array( $sectionID, $fieldID );

			if( !class_exists( 'AdminPageFramework' ) )
			{
				
				$fullOption = get_option( $mainOptionName, $default );

				$data = ( isset( $fullOption[$sectionID] ) && isset( $fullOption[$sectionID][$fieldID] ) ) ? $fullOption[$sectionID][$fieldID] : $default;

			}
			else
			{

				$data = AdminPageFramework::getOption( $mainOptionName, $sectionAndField, $default );

			}

			return $data;

		}/* get_studiorum_option() */

	endif;