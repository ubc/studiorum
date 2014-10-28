<?php
	
	/**
	 * AJAX methods for Studiorum
	 *
	 * @package     Studiorum
	 * @subpackage  Admin/AJAX
	 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
	 * @since       0.1.0
	 */

	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ){
		exit;
	}

	class Studiorum_AJAX
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

			add_action( 'wp_ajax_enable_plugin_group', array( $this, 'wp_ajax_enable_plugin_group__ajaxHandler' ) );
			add_action( 'wp_ajax_nopriv_enable_plugin_group', array( $this, 'wp_ajax_nopriv_enable_plugin_group__noDice' ) );

		}/* init() */

		/**
		 * AJAX handler for when a plugin group is enabled and the user has privs
		 *
		 * @author Richard Tape <@richardtape>
		 * @since 1.0
		 * @param 
		 * @return 
		 */
		
		public function wp_ajax_enable_plugin_group__ajaxHandler()
		{

			$result = array();

			// Nonce check?
			if ( !wp_verify_nonce( $_REQUEST['nonce'], 'my_user_vote_nonce' ) ) {
				$result['type'] = 'error';
				$result['reason'] = 'nonce';
			}

			// Sanitize/Fetch the group ID
			$groupID = ( isset( $_REQUEST['groupID'] ) ) ? sanitize_title_with_dashes( $_REQUEST['groupID'] ) : false;

			// Ensure we have been given a groupID
			if( $groupID === false ){
				$result['type'] = 'error';
				$result['reason'] = 'no groupID';
			}

			// check this group ID exists and we have data for it
			$pluginGroups = Studiorum_Utils::getPluignGroups();

			if( !$pluginGroups || !is_array( $pluginGroups ) ){
				$result['type'] = 'error';
				$result['reason'] = 'no groups';
			}

			// Group ID doesn't exist
			if( !array_key_exists( $groupID, $pluginGroups ) ){
				$result['type'] = 'error';
				$result['reason'] = 'groupID does not exist';
			}

			// Do we have an error? Run away.
			if( !empty( $result ) ){
				$result = json_encode($result);
				echo $result;
				die();
			}

			// OK, looks like we have a valid request to enable a group, let's pass that off to a util function
			$pluginGroupEnabled = Studiorum_Utils::enablePluginGroup( $groupID );

			// Error enabling plugins
			if( $pluginGroupEnabled === false ){
				$result['type'] = 'error';
				$result['reason'] = 'plugin enablement failure';
			}

			// OK, looks like we're good, let's report back to the JS
			$result['type'] = 'success';
			$result = json_encode($result);
			echo $result;
			die();

		}/* wp_ajax_enable_plugin_group__ajaxHandler() */


		/**
		 * AJAX handler for when a person tries to enable a group of plugins without the correct privs
		 *
		 * @author Richard Tape <@richardtape>
		 * @since 1.0
		 * @param 
		 * @return 
		 */

		public function wp_ajax_nopriv_enable_plugin_group__noDice()
		{



		}/* wp_ajax_nopriv_enable_plugin_group__noDice() */


		

	}/* Studiorum_AJAX() */

	add_action( 'plugins_loaded', array( 'Studiorum_AJAX', 'init' ) );