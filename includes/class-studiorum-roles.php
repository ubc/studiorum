<?php 

	/**
	 * Set up the custom roles for Studiorum
	 *
	 * @package     Studiorum
	 * @subpackage  Admin/Studiorum-Roles
	 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
	 * @since       0.1.0
	 */

	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ){
		exit;
	}

	class Studiorum_Roles
	{

		/**
		 * Actions and filters for roles/caps
		 *
		 * @since 0.1
		 *
		 * @param null
		 * @return null
		 */
		
		public function __construct()
		{

			// add_filter( 'map_meta_cap', array( $this, 'map_meta_cap' ), 10, 4 );
	
		}

		/**
		 * Add new shop roles with default WP caps
		 *
		 * @access public
		 * @since 1.4.4
		 * @return void
		 */

		public function add_roles()
		{

			// Edutcators are similar to admins
			add_role( 
				'studiorum_educator', __( 'Educator', 'studiorum' ), array(
					'read'                   => true,
					'edit_posts'             => true,
					'delete_posts'           => true,
					'unfiltered_html'        => true,
					'upload_files'           => true,
					'export'                 => true,
					'import'                 => true,
					'delete_others_pages'    => true,
					'delete_others_posts'    => true,
					'delete_pages'           => true,
					'delete_private_pages'   => true,
					'delete_private_posts'   => true,
					'delete_published_pages' => true,
					'delete_published_posts' => true,
					'edit_others_pages'      => true,
					'edit_others_posts'      => true,
					'edit_pages'             => true,
					'edit_private_pages'     => true,
					'edit_private_posts'     => true,
					'edit_published_pages'   => true,
					'edit_published_posts'   => true,
					'manage_categories'      => true,
					'manage_links'           => true,
					'moderate_comments'      => true,
					'publish_pages'          => true,
					'publish_posts'          => true,
					'read_private_pages'     => true,
					'read_private_posts'     => true
				)
			);

			// Students get a very limited set of capabilities
			add_role( 
				'studiorum_student', __( 'Student', 'studiorum' ), array(
					'read'                   => true,
					'edit_posts'             => false,
					'upload_files'           => true,
					'delete_posts'           => false
				)
			);

		}/* add_roles() */


		/**
		 * Add new shop-specific capabilities
		 *
		 * @access public
		 * @since  1.4.4
		 * @global WP_Roles $wp_roles
		 * @return void
		 */

		public function add_caps()
		{

			global $wp_roles;

			if( class_exists('WP_Roles') )
			{

				if( ! isset( $wp_roles ) )
				{

					$wp_roles = new WP_Roles();

				}

			}

			if( is_object( $wp_roles ) )
			{

				$wp_roles->add_cap( 'studiorum_educator', 'view_studiorum_reports' );
				$wp_roles->add_cap( 'studiorum_educator', 'view_studiorum_sensitive_data' );
				$wp_roles->add_cap( 'studiorum_educator', 'export_studiorum_reports' );
				$wp_roles->add_cap( 'studiorum_educator', 'manage_studiorum_settings' );

				$wp_roles->add_cap( 'administrator', 'view_studiorum_reports' );
				$wp_roles->add_cap( 'administrator', 'view_studiorum_sensitive_data' );
				$wp_roles->add_cap( 'administrator', 'export_studiorum_reports' );
				$wp_roles->add_cap( 'administrator', 'manage_studiorum_settings' );

			}

		}/* add_caps() */


		/**
		 * Map meta caps to primitive caps
		 *
		 * @access public
		 * @since  2.0
		 * @return array $caps
		 */

		public function meta_caps( $caps, $cap, $user_id, $args )
		{

			// switch( $cap )
			// {

			// 	case 'view_product_stats' :

			// 		$download = get_post( $args[0] );
			// 		if ( empty( $download ) ) {
			// 			break;
			// 		}

			// 		if( user_can( $user_id, 'view_shop_reports' ) || $user_id == $download->post_author ) {
			// 			$caps = array();
			// 		}

			// 		break;

			// }

			// return $caps;

		}


		/**
		 * Remove core post type capabilities (called on uninstall)
		 *
		 * @access public
		 * @since 1.5.2
		 * @return void
		 */

		public function remove_caps()
		{

			if ( class_exists( 'WP_Roles' ) )
			{

				if ( ! isset( $wp_roles ) )
				{
				
					$wp_roles = new WP_Roles();
				
				}
			
			}

			if ( is_object( $wp_roles ) ) {

				$wp_roles->remove_cap( 'studiorum_educator', 'view_studiorum_reports' );
				$wp_roles->remove_cap( 'studiorum_educator', 'view_studiorum_sensitive_data' );
				$wp_roles->remove_cap( 'studiorum_educator', 'export_studiorum_reports' );
				$wp_roles->remove_cap( 'studiorum_educator', 'manage_studiorum_settings' );

				$wp_roles->remove_cap( 'administrator', 'view_studiorum_reports' );
				$wp_roles->remove_cap( 'administrator', 'view_studiorum_sensitive_data' );
				$wp_roles->remove_cap( 'administrator', 'export_studiorum_reports' );
				$wp_roles->remove_cap( 'administrator', 'manage_studiorum_settings' );

			}

		}/* remove_caps() */

	}/* Studiorum_Roles() */