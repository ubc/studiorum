<?php

	/**
	 * Install mechanism for Studiorum
	 *
	 * @package     Studiorum
	 * @subpackage  Admin/install
	 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
	 * @since       0.1.0
	 */

	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ){
		exit;
	}

	/**
	 * Install
	 *
	 * Run when the plugin is activated. Sets up roles, settings pages. After install the user is redirected to
	 * the install screen.
	 *
	 * @since 0.1.0
	 *
	 * @param null
	 * @return null
	 */

	function register_activation_hook__studiorumInstall()
	{

		// Fire an action so we can hook in here if absolutely necessary (difficult to hook in here though?)
		do_action( 'studiorum_before_install', array() );

		// Add Upgraded From Option
		$current_version = get_option( 'studiorum_version' );
		if( $current_version ){
			update_option( 'studiorum_version_upgraded_from', $current_version );
		}

		// Setup some default options
		$options = array();

		// Add a temporary option to note that Studiorum pages have been created
		set_transient( '_studiorum_installed', $options, 30 );

		update_option( 'studiorum_version', STUDIORUM_VERSION );

		// Create Studiorum roles
		$roles = new Studiorum_Roles;
		$roles->add_roles();
		$roles->add_caps();

		// Bail if activating from network, or bulk
		if( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}

		// Add the transient to redirect
		set_transient( '_studiorum_activation_redirect', true, 30 );

	}/* register_activation_hook__studiorumInstall() */

	register_activation_hook( STUDIORUM_PLUGIN_FILE, 'register_activation_hook__studiorumInstall' );


	/**
	 * Post-installation
	 *
	 * Runs just after plugin installation and exposes the
	 * studiorum_after_install hook.
	 *
	 * @since 0.1.0
	 * @return void
	 */

	function admin_init__studiorumAfterInstall()
	{

		if( ! is_admin() ){
			return;
		}

		$studiorum_options = get_transient( '_studiorum_installed' );

		// Exit if not in admin or the transient doesn't exist
		if( false === $studiorum_options ) {
			return;
		}

		// Delete the transient
		delete_transient( '_studiorum_installed' );

		do_action( 'studiorum_after_install', $studiorum_options );

	}/* admin_init__studiorumAfterInstall() */

	add_action( 'admin_init', 'admin_init__studiorumAfterInstall' );


	/**
	 * Install user roles on sub-sites of a network
	 *
	 * Roles do not get created when Studiorum is network activation so we need to create them during admin_init
	 *
	 * @since 0.1.0
	 * @return void
	 */

	function admin_init__studiorumInstallRolesOnNetwork()
	{

		global $wp_roles;

		if( ! is_object( $wp_roles ) ){
			return;
		}

		if( !in_array( 'studiorum_student', $wp_roles->roles ) )
		{

			// Create Studiorum roles
			$roles = new Studiorum_Roles;
			$roles->add_roles();
			$roles->add_caps();

		}

	}/* admin_init__studiorumInstallRolesOnNetwork() */

	add_action( 'admin_init', 'admin_init__studiorumInstallRolesOnNetwork' );