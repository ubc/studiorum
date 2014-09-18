jQuery( document ).ready( function( $ ){

	$( '.studiorum_groups' ).on( 'click', '.plugin-group', click__handleGroupBeingClicked );

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
	function click__handleGroupBeingClicked(){

		// Determine if we're enabling or disabling
		var isActive = false;

		if( $( this ).hasClass( 'group-active' ) ){
			isActive = true;
		}

		var messageToShow = 'enable';

		if( isActive ){
			messageToShow = 'disable';
		}

		var thisName = $( this ).data( 'name' );

		var confirmed = confirm( 'Are you sure you want to ' + messageToShow + ' ' + thisName );

	}/* click__handleGroupBeingClicked() */

} );