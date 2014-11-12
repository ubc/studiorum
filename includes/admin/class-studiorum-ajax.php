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

			// AJAX Enable plugin group
			add_action( 'wp_ajax_enable_plugin_group', array( 'Studiorum_AJAX', 'wp_ajax_enable_plugin_group__ajaxHandler' ) );
			add_action( 'wp_ajax_nopriv_enable_plugin_group', array( 'Studiorum_AJAX', 'wp_ajax_nopriv_plugin_group__noDice' ) );

			// AJAX Disable plugin group
			add_action( 'wp_ajax_disable_plugin_group', array( 'Studiorum_AJAX', 'wp_ajax_disable_plugin_group__ajaxHandler' ) );
			add_action( 'wp_ajax_nopriv_disable_plugin_group', array( 'Studiorum_AJAX', 'wp_ajax_nopriv_plugin_group__noDice' ) );			

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

			// Validate the nonce and the group including sanitization
			$result = static::validateNonceAndGroup( $_REQUEST );

			// Do we have an error? Run away.
			static::dieIfError( $result );

			// Get the group ID For the request
			$groupID = static::getGroupID( $_REQUEST );

			// OK, looks like we have a valid request to enable a group, let's pass that off to a util function
			$pluginGroupEnabled = Studiorum_Utils::enablePluginGroup( $groupID );

			// Now vlidate that response
			$result = static::validatePluginGroupEnablement( $pluginGroupEnabled );

			// Do we have an error? Run away.
			static::dieIfError( $result );

			// OK, looks like we're good, let's report back to the JS
			$result['type'] = 'success';
			$result = json_encode( $result );
			echo $result;
			die();

		}/* wp_ajax_enable_plugin_group__ajaxHandler() */


		/**
		 * AJAX Handler for when someone disables a set of plugins
		 *
		 * @author Richard Tape <@richardtape>
		 * @since 1.0
		 * @param null
		 * @return null
		 */
		
		public function wp_ajax_disable_plugin_group__ajaxHandler()
		{

			// Validate the nonce and the group including sanitization
			$result = static::validateNonceAndGroup( $_REQUEST );

			// Do we have an error? Run away.
			static::dieIfError( $result );

			// Get the group ID For the request
			$groupID = static::getGroupID( $_REQUEST );

			// OK, looks like we have a valid request to enable a group, let's pass that off to a util function
			$pluginGroupEnabled = Studiorum_Utils::disablePluginGroup( $groupID );

			// Now vlidate that response
			$result = static::validatePluginGroupEnablement( $pluginGroupEnabled );

			// Do we have an error? Run away.
			static::dieIfError( $result );

			// OK, looks like we're good, let's report back to the JS
			$result['type'] = 'success';
			$result = json_encode( $result );
			echo $result;
			die();

		}/* wp_ajax_disable_plugin_group__ajaxHandler() */
		

		/**
		 * Some checking for nonces and group IDs including sanitization
		 *
		 * @author Richard Tape <@richardtape>
		 * @since 1.0
		 * @param (array) $_REQUEST - the AJAX Request
		 * @return (array) $result - if we have an error, return a full array with messages otherwise an empty array
		 */
		
		private static function validateNonceAndGroup( $request = array() )
		{

			// It's an empty array by default, only adjusted if there's an error
			$result = array();

			// Check we've been passed a nonce
			if( !array_key_exists( 'nonce', $request ) ){
				$result['type'] = 'error';
				$result['reason'] = 'no nonce';
				return $result;
			}

			// Check we have been passed a group ID
			if( !array_key_exists( 'groupID', $request ) ){
				$result['type'] = 'error';
				$result['reason'] = 'no group ID';
				return $result;
			}

			// Verify check?
			if ( !wp_verify_nonce( $request['nonce'], 'studiorum_group_action_nonce' ) ) {
				$result['type'] = 'error';
				$result['reason'] = 'nonce';
				return $result;
			}

			// Sanitize/Fetch the group ID
			$groupID = static::getGroupID( $request );

			// Ensure we have been given a groupID
			if( $groupID === false ){
				$result['type'] = 'error';
				$result['reason'] = 'no or invalid groupID';
				return $result;
			}

			return $result;

		}/* validateNonceAndGroup() */
		

		/**
		 * Validate and check the return value of enabling a group of plugins
		 *
		 * @author Richard Tape <@richardtape>
		 * @since 1.0
		 * @param (mixed) $pluginGroupResult - the result of Studiorum_Utils::enablePluginGroup( $groupID )
		 * @return (mixed)
		 */
		
		private static function validatePluginGroupEnablement( $pluginGroupResult = false )
		{

			// It's an empty array by default, only adjusted if there's an error
			$result = array();

			// Error enabling plugins
			if( $pluginGroupResult === false ){
				$result['type'] = 'error';
				$result['reason'] = 'plugin enablement failure';
				return $result;
			}

			if( is_wp_error( $pluginGroupResult ) ){
				$result['type'] = 'error';
				$result['reason'] = $pluginGroupResult->get_error_message();
				return $result;
			}

			return $result;

		}/* validatePluginGroupEnablement() */
		

		/**
		 * Look for - and, if found sanitize - a group ID
		 *
		 * @author Richard Tape <@richardtape>
		 * @since 1.0
		 * @param (array) $request - where the groupID should come from
		 * @return (string) $groupID - a sanitized group ID or false
		 */
		
		private static function getGroupID( $request = array() )
		{

			$groupID = ( isset( $request['groupID'] ) ) ? sanitize_title_with_dashes( $request['groupID'] ) : false;

			return $groupID;

		}/* getGroupID() */
		

		/**
		 * 
		 *
		 * @author Richard Tape <@richardtape>
		 * @since 1.0
		 * @param (array) $result - what to check for
		 * @return mixed - calls die() if we have an error after echoing the json
		 */
		
		private static function dieIfError( $result = array() )
		{

			if( !is_array( $result ) ){
				$result = array();
				$result['type'] = 'error';
				$result['reason'] = 'result must be an array';
				echo $result;
				die();
			}

			if( !empty( $result ) ){
				$result = json_encode( $result );
				echo $result;
				die();
			}

		}/* dieIfError() */


		/**
		 * AJAX handler for when a person tries to enable a group of plugins without the correct privs
		 *
		 * @author Richard Tape <@richardtape>
		 * @since 1.0
		 * @param 
		 * @return 
		 */

		public function wp_ajax_nopriv_plugin_group__noDice()
		{

			echo json_encode( array( 'no' => 'stop' ) );
			die();

		}/* wp_ajax_nopriv_enable_plugin_group__noDice() */


		

	}/* Studiorum_AJAX() */

	add_action( 'plugins_loaded', array( 'Studiorum_AJAX', 'init' ) );