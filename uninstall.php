<?php

	/**
	 * Uninstall Studiorum
	 *
	 * @package     Studiorum
	 * @subpackage  Uninstall
	 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
	 * @since       0.1.0
	 */

	// Exit if accessed directly
	if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ){
		exit;
	}

	// Load Studiorum file
	include_once( 'studiorum.php' );

	global $wpdb, $wp_roles;

	// Delete all the Plugin Options
	delete_option( 'studiorum_settings' );

	// Delete Capabilities
	Studiorum()->roles->remove_caps();

	// Delete the Roles
	$StudiorumRoles = array( 'studiorum_educator', 'studiorum_student' );
	foreach( $StudiorumRoles as $role ){
		remove_role( $role );
	}