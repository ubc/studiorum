<?php 

	/**
	 * Generic helper utils
	 *
	 * @package     Studiorum
	 * @subpackage  Admin/Studiorum-Utils
	 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
	 * @since       0.1.0
	 */

	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ){
		exit;
	}

	class Studiorum_Utils
	{

		/**
		 * Method to determine if a user is of a specific role (rather than via capabilities)
		 *
		 * @since 0.1
		 *
		 * @param string $role The role we're checking
		 * @return bool whether the user is the specified role
		 */

		public static function usersRoleIs( $role = false )
		{

			// Simple sanitization
			$role = sanitize_text_field( $role );

			// Must be passed a role
			if( !$role ){
				return new WP_Error( '1', 'usersRoleIs() was not passed a role' );
			}

			// Ensure they're logged in, otherwise we can't tell
			if( !is_user_logged_in() ){
				return false;
			}

			// OK, we can check this user, use WP's globalized $curent_user
			global $current_user; 
			get_currentuserinfo();

			if( $current_user && isset( $current_user->caps ) && is_array( $current_user->caps ) && isset( $current_user->caps[ $role ] ) && $current_user->caps[ $role ] == 1 ){

				return true;

			}else{

				return false;

			}

		}/* usersRoleIs() */

	}/* class Studiorum_Utils */