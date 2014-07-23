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


		/**
		 * WordPress's native get_the_excert function has to be called from within a loop. This provides a method to fetch
		 * a post's excerpt outside of the loop, by ID
		 *
		 * @since 0.1
		 *
		 * @param int $postID The ID of the post
		 * @param int $excerptLength The length of the excerpt
		 * @param bool $withP Whether to wrap the excerpt in <p> tags or not
		 * @return string The post excerpt
		 */

		public static function getExcerptFromPostID( $postID, $excerptLength = 35, $withP = false )
		{

			$postObject = get_post( $postID );

			// Gets post_content to be used as a basis for the excerpt
			$excerpt = $postObject->post_content;
			
			// Strips tags and images
			$excerpt = strip_tags( strip_shortcodes( $excerpt ) );
			$words = explode( ' ', $excerpt, $excerptLength + 1 );

			if( count( $words ) > $excerptLength ) :

				array_pop( $words );
				array_push( $words, '…' );
				$excerpt = implode( ' ', $words );

			endif;

			if( $withP ){
				$excerpt = '<p>' . $excerpt . '</p>';
			}

			return $excerpt;

		}/* getExcerptFromPostID() */


		/**
		 * Method to allow including of a template part from within a plugin. Replicates what locate_template() does in
		 * WordPress core, but allows you to specify the path where to start looking. Falls back to looking in the
		 * 
		 *
		 * @since 0.1
		 *
		 * @param string $startPath The path - probably set via a constant - of where to start looking
		 * @param string|array $templateNames Template file(s) to search for, in order.
		 * @param bool $load If true the template file will be loaded if it is found.
		 * @param bool $requireOnce Whether to require_once or require. Default true. Has no effect if $load is false.
		 * @return string The template filename if one is located.
		 */

		public static function locateTemplateInPlugin( $startPath, $templateNames, $load = false, $requireOnce = true )
		{

			$located = '';

			foreach( (array) $templateNames as $templateName )
			{

				if( !$templateName ){
					continue;
				}

				if( file_exists( untrailingslashit( $startPath ) . '/' . $templateName ) )
				{

					$located =  untrailingslashit( $startPath ) . '/' . $templateName;
					break;

				}
				else if( file_exists( untrailingslashit( STYLESHEETPATH ) . '/' . $templateName ) )
				{
					$located = untrailingslashit( STYLESHEETPATH ) . '/' . $templateName;
					break;

				}
				else if( file_exists( untrailingslashit( TEMPLATEPATH ) . '/' . $templateName ) )
				{

					$located = untrailingslashit( TEMPLATEPATH ) . '/' . $templateName;
					break;
				
				}
			
			}

			if( $load && '' != $located ){
				load_template( $located, $require_once );
			}

			return $located;

		}/* locateTemplateInPlugin() */

	}/* class Studiorum_Utils */