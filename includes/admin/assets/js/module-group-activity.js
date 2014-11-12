;var STUDIORUM = {};

;( function($) {

	STUDIORUM.groupActivity = {

		/**
		 * When a module group is clicked we determine if we are activating or deactivating a group
		 * Then we ask the user if they really want to do this and inform them what is about to happen
		 * If they choose to, then we fire off an AJAX request to activate/deactivate the necessary plugins
		 * 
		 * @since 0.1
		 *
		 * @param null
		 * @return null 
		 */
		click__handleGroupBeingClicked: function( event, bttn, instance ){

			// We don't want the link click to 'do' anything
			// event.preventDefault();

			// Determine if we're enabling or disabling
			var isActive = false;

			if( $( this ).hasClass( 'group-active' ) ){
				isActive = true;
			}

			var messageToShow = studiorum_group_vars.strings.activate;

			if( isActive ){
				messageToShow = studiorum_group_vars.strings.deactivate;
			}

			var ays 		= studiorum_group_vars.strings.ays;

			var thisName 	= $( bttn ).data( 'name' );
			var groupID 	= $( bttn ).data( 'groupid' );
			var nonce 		= $( bttn ).data( 'nonce' );

			// Double check we want to do this
			// var confirmed = confirm( ays + ' ' + messageToShow + ' ' + thisName );

			// OK, let's enable/disable
			// if( confirmed ){

				if( isActive ){
					$( this ).trigger( 'studiorum_disable_group_confirmed', [ groupID, nonce, bttn, instance ] );
					STUDIORUM.groupActivity.disablePluginGroup( groupID, nonce, bttn, instance );
				}else{
					$( this ).trigger( 'studiorum_enable_group_confirmed', [ groupID, nonce, bttn, instance ] );
					STUDIORUM.groupActivity.enablePluginGroup( groupID, nonce, bttn, instance );
				}

			// }

		},/* click__handleGroupBeingClicked() */


		/**
		 * Enable a group of plugins
		 *
		 * @author Richard Tape <@richardtape>
		 * @since 1.0
		 * @param (string) groupID - the group id of the plugins to enable
		 * @param (string) nonce - the nonce for this action
		 * @param (object) bttn - the button object that has been clicked
		 * @param (object) instance - The loading bar instance object
		 * @return null
		 */
		
		enablePluginGroup: function( groupID, nonce, bttn, instance ){

			var progress = 0,
			interval = setInterval( function() {
				progress = Math.min( progress + Math.random() * 0.1, 1 );
				instance._setProgress( progress );

				if( progress === 1 ) {
					instance._stop(1);
					clearInterval( interval );
				}
			}, 200 );

			// The data for our generic AJAX request, for enabling
			data = {
				action: 'enable_plugin_group',
				groupID : groupID,
				nonce: nonce
			}

			successData = {};

			failureData = {}

			STUDIORUM.groupActivity.runGroupAJAXRequest( data, 'enablePluginGroupSuccessCallback', successData, 'enablePluginGroupFailureCallback', failureData, 'enablePluginGroupBeforeSendCallback' );

		},/* enablePluginGroup() */


		/**
		 * Disable a group of plugins
		 *
		 * @author Richard Tape <@richardtape>
		 * @since 1.0
		 * @param (string) groupID - the group id of the plugins to enable
		 * @param (string) nonce - the nonce for this action
		 * @param (object) bttn - the button object that has been clicked
		 * @param (object) instance - The loading bar instance object
		 * @return null
		 */
		
		 disablePluginGroup: function( groupID, nonce, bttn, instance ){

			var progress = 0,
			interval = setInterval( function() {
				progress = Math.min( progress + Math.random() * 0.1, 1 );
				instance._setProgress( progress );

				if( progress === 1 ) {
					instance._stop(1);
					clearInterval( interval );
				}
			}, 200 );

			// The data for our generic AJAX request, for enabling
			data = {
				action: 'disable_plugin_group',
				groupID : groupID,
				nonce: nonce
			}

			successData = {};

			failureData = {}

			STUDIORUM.groupActivity.runGroupAJAXRequest( data, 'disablePluginGroupSuccessCallback', successData, 'disablePluginGroupFailureCallback', failureData, 'disablePluginGroupBeforeSendCallback' );

		},/* disablePluginGroup() */
		


		/**
		 * A generic helper method to run an AJAX request
		 *
		 * @author Richard Tape <@richardtape>
		 * @since 1.0
		 * @param (object) data 				- A data object for the AJAX request, including an action, groupID and nonce
		 * @param (string) successCallback 		- the method name to call on success
		 * @param (object) successData 			- an object to send to the success callback as a parameter
		 * @param (string) failureCallback 		- the method name to call on success
		 * @param (object) failureData 			- an object to send to the success callback as a parameter
		 * @param (string) beforeSendCallback 	- the method name to call on the AJAX's beforeSend callback
		 * @return 
		 */
		
		runGroupAJAXRequest: function( data, successCallback, successData, failureCallback, failureData, beforeSendCallback ){

			jQuery.ajax( {

				type : 'post',
				dataType : 'json',
				url : studiorum_group_vars.admin_ajax_url,
				
				data : data,
			
				beforeSend: function( jqXHR, settings ){

					var beforeSendFunctionName = "STUDIORUM.groupActivity." + beforeSendCallback;

					var funcArgs = { 
						'jqXHR' : jqXHR,
						'settings' : settings,
						'AJAXData' : data
					};

					// Fire the requested callback
					STUDIORUM.groupActivity.executeFunctionByName( beforeSendFunctionName, window, funcArgs );

				},

				success: function( response ){

					if( response.type == 'success' ) {

						var funcArgs = { 
							'data' : successData,
							'response' : response,
							'AJAXData' : data
						};

						var successFunctionName = "STUDIORUM.groupActivity." + successCallback;

						// Fire the requested callback
						STUDIORUM.groupActivity.executeFunctionByName( successFunctionName, window, funcArgs );

					}else{

						var funcArgs = { 
							'data' : failureData,
							'response' : response,
							'AJAXData' : data
						};

						var failureFunctionName = "STUDIORUM.groupActivity." + failureCallback;
						
						// Fire the requested callback
						STUDIORUM.groupActivity.executeFunctionByName( failureFunctionName, window, funcArgs );

					}

				},

				error: function( jqXHR, errorType, exceptionObject ){

					console.log( 'in error' );

				},

				complete: function( jqXHR, resultCode ){

					

				}

			} );

		},
		
		/**
		 * When we have successfully activated a group of plugins
		 *
		 * @author Richard Tape <@richardtape>
		 * @since 1.0
		 * @param (object) callbackData - contains: 
		 *                              (object) data - anything passed from the function which calls this
		 *                              (object) response - the full response from the AJAX request
		 *                              (object) AJAXData - the data object originally sent in the AJAX request
		 * @return null
		 */
		
		enablePluginGroupSuccessCallback: function( callbackData ){

		},


		/**
		 * When we have unsuccessfully activated a group of plugins
		 *
		 * @author Richard Tape <@richardtape>
		 * @since 1.0
		 * @param (object) callbackData - contains: 
		 *                              (object) data - anything passed from the function which calls this
		 *                              (object) response - the full response from the AJAX request
		 *                              (object) AJAXData - the data object originally sent in the AJAX request
		 * @return null
		 */
		enablePluginGroupFailureCallback: function( callbackData ){

		},


		/**
		 * Before sending an activate plugin group request we have a beforeSend method
		 *
		 * @author Richard Tape <@richardtape>
		 * @since 1.0
		 * @param (object) callbackData - contains: 
		 *                              (object) jqXHR - the jQuery AJAX XHR object
		 *                              (object) settings - the jQuery AJAX settings object
		 *                              (object) AJAXData - the data object originally sent in the AJAX request
		 * @return null
		 */
		
		enablePluginGroupBeforeSendCallback: function( callbackData ){

		},


		/**
		 * Allow calling a function from it's name (as a string) with arguments
		 *
		 * @author Richard Tape <@richardtape>
		 * @since 1.0
		 * @url http://stackoverflow.com/questions/359788/how-to-execute-a-javascript-function-when-i-have-its-name-as-a-string
		 * @param (string) functionName - the function name to call
		 * @param (string) context - the namespace/context to call, i.e. window or my.namespace
		 * @param (array) args - arguments to pass to the function
		 * @return 
		 */
		
		executeFunctionByName: function( functionName, context, args ){

			var args = Array.prototype.slice.call( arguments, 2 );
			var namespaces = functionName.split(".");
			var func = namespaces.pop();

			for( var i = 0; i < namespaces.length; i++ ) {
				context = context[namespaces[i]];
			}

			return context[func].apply( context, args );

		},/* executeFunctionByName() */


		/**
		 * 
		 *
		 * @author Richard Tape <@richardtape>
		 * @since 1.0
		 * @param (object) event - the fired event
		 * @param (int) status - success or failure (0 or 1 probably)
		 * @param (object) button - the button that was clicked
		 * @return 
		 */
		
		changeButtonText: function( event, status, button ){

			var statusClass = status >= 0 ? 'state-success' : 'state-error';
			classie.add( button, statusClass );

			setTimeout( function() {
				classie.remove( button, statusClass );
				var deactivateText	= studiorum_group_vars.strings.deactivate;

				$( button ).find( 'span.content' ).text( deactivateText );
				$( button ).parents( 'li' ).addClass( 'group-active' ).removeClass( 'group-inactive' );
				button.removeAttribute( 'disabled' );
			}, 1500 );

		},

	}

} )( jQuery );

jQuery( document ).ready( function( $ ){

	// Button clicked, work out what to do
	$( document ).on( 'studiorum_loading_callback_start', STUDIORUM.groupActivity.click__handleGroupBeingClicked );

	// After the loading callback state class is changed, change the text
	$( document ).on( 'studiorum_loading_callback_after_state_class', STUDIORUM.groupActivity.changeButtonText );

} );