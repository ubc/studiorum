<?php
	
	/**
	 * Set up the dashboard. Remove all default WP dashboard widgets for students and educators
	 *
	 * @package     Studiorum
	 * @subpackage  Admin/Dashboard-setup
	 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
	 * @since       0.1.0
	 */

	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ){
		exit;
	}

	class Studiorum_Dashboard_Setup
	{

		/**
		 * Set up actions and filters
		 *
		 * @since 0.1
		 *
		 * @param null
		 * @return null
		 */

		public static function init()
		{

			// For students, remove the default dashboard widgets
			add_action( 'wp_dashboard_setup', array( __CLASS__, 'wp_dashboard_setup__removeDefaultWidgetsForStudents' ), 9999 );
			add_action( 'wp_user_dashboard_setup', array( __CLASS__, 'wp_dashboard_setup__removeDefaultWidgetsForStudents' ) );

			// We also need to clean up the educator's dashboard a little
			add_action( 'wp_dashboard_setup', array( __CLASS__, 'wp_dashboard_setup__removeDefaultWidgetsForEducators' ), 9999 );

			// Students do not need to see the 'media' menu item or view that page, either
			add_action( 'admin_menu', array( __CLASS__, 'admin_menu__removeMenuItemsForStudents' ) );
			add_action( 'wp_before_admin_bar_render', array( __CLASS__, 'wp_before_admin_bar_render__removeMenuItemsForStudents' ) );

			// Force a 1-column dashboard layout for students
			add_filter( 'screen_layout_columns', array( __CLASS__, 'screen_layout_columns__oneColumnDashboard' ), 999, 3 );
			add_filter( 'get_user_option_screen_layout_dashboard', array( __CLASS__, 'get_user_option_screen_layout_dashboard__oneColumnDashboard' ), 999, 3 );

		}/* __construct() */


		/**
		 * We really strip back the dashboard for students, starting with removing the default dashboard widgets
		 *
		 * @since 0.1
		 *
		 * @param null
		 * @return null
		 */

		public static function wp_dashboard_setup__removeDefaultWidgetsForStudents()
		{

			// We only do this for students by default, but run it through a filter so we can add-to or remove roles
			$rolesToRemoveAllDefaultDashboardWidgets = apply_filters( 'studiorum_roles_to_remove_default_widgets', array( 'studiorum_student' ) );
			
			if( !$rolesToRemoveAllDefaultDashboardWidgets || !is_array( $rolesToRemoveAllDefaultDashboardWidgets ) || empty( $rolesToRemoveAllDefaultDashboardWidgets ) ){
				return;
			}

			// Default is to not hide the metaboxes
			$hideMetaBoxes = false;

			foreach( $rolesToRemoveAllDefaultDashboardWidgets as $key => $role )
			{

				if( Studiorum_Utils::usersRoleIs( $role ) ){

					// This user is in a set of roles where we're hiding the metaboxes
					$hideMetaBoxes = true;
					break;

				}

			}

			// role set?
			if( !$hideMetaBoxes ){
				return;
			}

			// OK, we're clearly hiding metaboxes for this user
			global $wp_meta_boxes;
		    $wp_meta_boxes['dashboard']['normal']['core'] = array();
		    $wp_meta_boxes['dashboard']['side']['core'] = array();

		}/* wp_dashboard_setup__removeDefaultWidgetsForStudents() */


		/**
		 * Clean up the educator dashboard a little
		 *
		 * @since 0.1
		 *
		 * @param null
		 * @return null
		 */

		public static function wp_dashboard_setup__removeDefaultWidgetsForEducators()
		{

			if( !Studiorum_Utils::usersRoleIs( 'studiorum_educator' ) ){
				return false;
			}

			global $wp_meta_boxes;

			unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins'] );
			unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts'] );
			unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_primary'] );

		}/* wp_dashboard_setup__removeDefaultWidgetsForEducators() */


		/**
		 * We need to hide certain menu items for students. Starting with the media page
		 *
		 * @since 0.1
		 *
		 * @param null
		 * @return null
		 */

		public static function admin_menu__removeMenuItemsForStudents()
		{

			if( !Studiorum_Utils::usersRoleIs( 'studiorum_student' ) ){
				return false;
			}

			remove_menu_page( 'upload.php' ); 							// Uploads
			remove_menu_page( 'media-new.php' ); 						// Media
			remove_menu_page( 'link-manager.php' ); 					// Links
			remove_menu_page( 'edit.php' ); 							// Posts
			remove_menu_page( 'edit-comments.php' ); 					// Comments
			remove_menu_page( 'tools.php' ); 							// Tools
			remove_menu_page( 'edit.php?post_type=lectio-submission' ); // Submissions entries

		}/* admin_menu__removeMenuItemsForStudents() */


		/**
		 * Also need to remove some items from the menu bar
		 *
		 * @since 0.1
		 *
		 * @param null
		 * @return null
		 */

		public static function wp_before_admin_bar_render__removeMenuItemsForStudents()
		{

			if( !Studiorum_Utils::usersRoleIs( 'studiorum_student' ) ){
				return false;
			}

			global $wp_admin_bar;

			$wp_admin_bar->remove_menu( 'new-media' );

		}/* wp_before_admin_bar_render__removeMenuItemsForStudents() */


		/**
		 * Force a 1-column layout for WP Dashboard
		 *
		 * @since 0.1
		 *
		 * @param string $param description
		 * @return string|int returnDescription
		 */

		public static function screen_layout_columns__oneColumnDashboard( $cols, $id, $screen )
		{

			$rolesForOneColumnDashboard = apply_filters( 'studiorum_roles_to_make_one_column_dashboard', array( 'studiorum_student', 'studiorum_educator' ) );

			if( !$rolesForOneColumnDashboard || !is_array( $rolesForOneColumnDashboard ) || empty( $rolesForOneColumnDashboard ) ){
				return $cols;
			}

			$changeCols = false;

			foreach( $rolesForOneColumnDashboard as $key => $role )
			{
				
				if( Studiorum_Utils::usersRoleIs( $role ) ){

					// This user is in a set of roles where we're changing the cols
					$changeCols = true;
					break;

				}

			}

			if( !$changeCols ){
				return $cols;
			}

			// 1 for this user
			$cols['dashboard'] = 1;
			return $cols;

		}/* screen_layout_columns__oneColumnDashboard() */


		/**
		 * Force a 1-column layout for WP Dashboard
		 *
		 * @since 0.1
		 *
		 * @param string $param description
		 * @return string|int returnDescription
		 */

		public static function get_user_option_screen_layout_dashboard__oneColumnDashboard( $result, $option, $user )
		{

			$rolesForOneColumnDashboard = apply_filters( 'studiorum_roles_to_make_one_column_dashboard', array( 'studiorum_student', 'studiorum_educator' ) );

			if( !$rolesForOneColumnDashboard || !is_array( $rolesForOneColumnDashboard ) || empty( $rolesForOneColumnDashboard ) ){
				return $result;
			}

			$changeCols = false;

			foreach( $rolesForOneColumnDashboard as $key => $role )
			{
				
				if( Studiorum_Utils::usersRoleIs( $role ) ){

					// This user is in a set of roles where we're changing the cols
					$changeCols = true;
					break;

				}

			}

			if( !$changeCols ){
				return $result;
			}

			// Don't change non-students
			if( !$changeCols ){
				return $result;
			}

			return 1;

		}/* get_user_option_screen_layout_dashboard__oneColumnDashboard() */

	}/* Studiorum_Dashboard_Setup() */

	add_action( 'plugins_loaded', array( 'Studiorum_Dashboard_Setup', 'init' ) );