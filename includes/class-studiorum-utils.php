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

		public static function usersRoleIs( $role = false, $current_user = false )
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
			if( !$current_user )
			{

				global $current_user; 
				get_currentuserinfo();
				
			}

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


		/**
		 * Method to fetch, rather than echo, the contents of a template file
		 * Uses output buffering. Bad times, but at the moment, there's no better way to do this neatly.
		 *
		 * @since 0.1
		 *
		 * @param string $path - the full absolute URL to the template part
		 * @return string $content - the content of that template
		 */

		public static function fetchTemplatePart( $path, $data = false )
		{

			if( !is_array( $data ) ){
				$data = array( $data );
			}

			ob_start();

			include( $path );

			$content = ob_get_contents();

			ob_end_clean();

			return $content;

		}/* fetchTemplatePart() */


		/**
		 * Get a list of all of our available modules
		 *
		 *
		 * @since 0.1
		 *
		 * @param null
		 * @return array An array of arrays of all of our studiorum modules
		 */

		public static function getAllModules()
		{

			$modules = array();

			return apply_filters( 'studiorum_modules', $modules );

		}/* getAllModules() */


		/**
		 * Method to return a list of plugins currently available which are 'studiorum' plugins
		 *
		 * @since 0.1
		 *
		 * @param null
		 * @return array list of studiorum plugins
		 */

		public static function getStudiorumPlugins()
		{

			if( !function_exists( 'get_plugins' ) ){
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			// Returns a list of all available plugins
			$allPlugins = get_plugins();

			if( !$allPlugins || !is_array( $allPlugins ) || empty( $allPlugins ) ){
				return;
			}

			$output = array();

			foreach( $allPlugins as $pathAndSlug => $pluginData )
			{
			
				$splitPathAndSlug = explode( '/', $pathAndSlug );

				// Search for 'studiorum' in the folder name
				$folderName = strtolower( $splitPathAndSlug[0] );

				$searchFor = 'studiorum';

				$pos = strpos( $folderName, $searchFor );

				if( $pos === false ){
					continue;
				}

				$output[$folderName] = $pluginData;

			}

			return $output;

		}/* getStudiorumPlugins() */


		/**
		 * Get a list of all studiorum plugins that are *active*
		 *
		 * @since 0.1
		 *
		 * @param null
		 * @return array of currently activated studiorum plugins
		 */

		public static function getActiveStudiorumPlugins()
		{

			$activePlugins = wp_get_active_and_valid_plugins();

			if( !$activePlugins || !is_array( $activePlugins ) || empty( $activePlugins ) ){
				return;
			}

			$output = array();

			// Each active plugin file has the plugin dir prepended, we need to strip that
			$pathToRemove = WP_PLUGIN_DIR . '/';

			foreach( $activePlugins as $key => $path )
			{
			
				$searchFor = 'studiorum';

				$pos = strpos( $path, $searchFor );

				if( $pos === false ){
					continue;
				}

				// Remove the path
				$withoutPath = str_replace( $pathToRemove, '', $path );

				// Now we just need the directory name
				$splitPathAndSlug = explode( '/', $withoutPath );

				// Search for 'studiorum' in the folder name
				$folderName = strtolower( $splitPathAndSlug[0] );

				$output[] = $folderName;

			}

			return $output;

		}/* getActiveStudiorumPlugins() */


		/**
		 * Get all active plugins and strip off the path
		 *
		 * @since 0.1
		 *
		 * @param string $param description
		 * @return string|int returnDescription
		 */

		public static function getAllActivePluginsWithoutPluginPath()
		{

			$activePlugins = wp_get_active_and_valid_plugins();

			if( !$activePlugins || !is_array( $activePlugins ) || empty( $activePlugins ) ){
				return;
			}

			$output = array();

			// Each active plugin file has the plugin dir prepended, we need to strip that
			$pathToRemove = WP_PLUGIN_DIR . '/';

			foreach( $activePlugins as $key => $path )
			{

				// Remove the path
				$withoutPath = str_replace( $pathToRemove, '', $path );

				$output[] = $withoutPath;

			}

			return $output;

		}/* getAllActivePluginsWithoutPluginPath() */


		/**
		 * Get the plugin 'groups' which are sets of plugins with some metadata about them
		 *
		 * @since 0.1
		 *
		 * @todo There should be a class stub which sets up each property and then each group should be an extension of that class which then adds a filter to a list. This method should then simply return that filter
		 * @param null
		 * @return array An array of plugin groups
		 */

		public static function getPluignGroups()
		{

			$groups = array(

				'christina' => array(
					'id'				=> 'christina',
					'title'				=> 'Christina',
					'icon' 				=> 'admin-appearance',
					'excerpt' 			=> 'Enable your students to submit assignments, have them collaborate in groups and get inline feedback.',
					'image' 			=> 'http://dummyimage.com/308x160',
					'link' 				=> 'http://code.ubc.ca/studiorum/courses/christina',
					'content' 			=> __( '<p>Create assignments - with deadlines - that enable your students to submit their work in a beautiful rich text editor. The student (and peers in their custom user groups) are able to make inline comments to get fine-grained critique.</p>', 'studiorum' ),
					'content_sidebar' 	=> 'http://dummyimage.com/300x150',
					'date'				=> '2014-06-01',
					'plugins'			=> array(
						'gravityforms/gravityforms.php',
						'gravity-forms-custom-post-types/gfcptaddon.php',
						'gravity-forms-wysiwyg/gf_wysiwyg.php',
						'studiorum/studiorum.php',
						'studiorum-lectio/studiorum-lectio.php',
						'studiorum-side-comments/studiorum-side-comments.php',
						'studiorum-user-groups/studiorum-user-groups.php'
					),
					'examples' 			=> array(
						'http://arts.ubc.ca/arts-one/'
					)
				),
				
				'simon' => array(
					'id'				=> 'simon',
					'title'				=> 'Simon',
					'icon' 				=> 'lightbulb',
					'excerpt' 			=> 'Enable students to rate each others\' work simply and easily using a hot-or-not style voting system.',
					'image' 			=> 'http://dummyimage.com/308x160/222/fff',
					'link' 				=> 'http://code.ubc.ca/studiorum/courses/simon',
					'content' 			=> __( '<p>Provide a simple way for your students to provide hot-or-not style feedback to easily discover which content is best.</p>', 'studiorum' ),
					'content_sidebar' 	=> 'http://dummyimage.com/300x150',
					'date'				=> '2014-07-01',
					'plugins'			=> array(
						'gravityforms/gravityforms.php',
						'gravity-forms-custom-post-types/gfcptaddon.php',
						'gravity-forms-wysiwyg/gf_wysiwyg.php',
						'studiorum/studiorum.php',
						'studiorum-lectio/studiorum-lectio.php',
						'studiorum-hon/studiorum-hon.php'
					),
					'examples' 			=> array(
						'http://physics.ubc.ca/'
					)
				),

				'paul' => array(
					'id'				=> 'paul',
					'title'				=> 'Paul',
					'icon' 				=> 'share',
					'excerpt' 			=> 'Curate content via delicious.com and show it on your site in a beautiful, searchable, engaging way.',
					'image' 			=> 'http://dummyimage.com/308x160/fff/333',
					'link' 				=> 'http://code.ubc.ca/studiorum/courses/paul',
					'content' 			=> __( '<p>Curate and tag content using the popular delicious.com service and then allow students to search quickly and easily to see the content you want them to see.</p>', 'studiorum' ),
					'content_sidebar' 	=> 'http://dummyimage.com/300x150',
					'date'				=> '2014-05-01',
					'plugins'			=> array(
						'studiorum/studiorum.php',
						'studiorum-hon/studiorum-delicious.php'
					),
					'examples' 			=> array(
						'http://sauder.ubc.ca/'
					)
				),

			);

			return $groups;

		}/* getPluignGroups() */


		/**
		 * Helper method to determine if the passed plugin slug is an active plugin
		 *
		 * @since 0.1
		 *
		 * @param string $slug slug of plugin to check if it is active
		 * @param array $plugins An array of plugins to check through
		 * @return string|int returnDescription
		 */

		public static function isStudiorumPluginActive( $slug = false, $plugins = false )
		{

			if( !$slug ){
				return false;
			}

			if( !$plugins ){
				$plugins = static::getActiveStudiorumPlugins();
			}

			// Sanitize the slug
			$slug = sanitize_text_field( $slug );
			
			if( in_array( $slug, array_values( $plugins ) ) ){
				return true;
			}

			return false;

		}/* isStudiorumPluginActive() */

		/**
		 * Determine whether a plugin is 'new' or not - basically has it been added in the last month
		 *
		 * @since 0.1
		 *
		 * @param string $pluginDate - the date the plugin was published
		 * @return bool
		 */

		public static function isPluginNew( $pluginDate = false )
		{

			if( !$pluginDate ){
				return false;
			}

			$now = strtotime( 'now' );
			$dateToCheck = strtotime( $pluginDate );
			$thirtyDaysAgo = strtotime( '30 days ago' );

			if( $dateToCheck > $thirtyDaysAgo ){
				return true;
			}

			return false;

		}/* isPluginNew() */

		

	}/* class Studiorum_Utils */