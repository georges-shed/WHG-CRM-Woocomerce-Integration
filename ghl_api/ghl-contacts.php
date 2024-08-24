<?php
if ( ! defined( 'ABSPATH' ) ) exit;
// Get Contact Data
if ( ! function_exists( 'ghlconnect_get_location_contact_data' ) ) {
    
    function ghlconnect_get_location_contact_data($contact_data) {

    
		$ghlconnect_access_token = get_option( 'ghlconnect_access_token' );
		$endpoint = GHLCONNECT_CONTACT_DATA_API;
		$ghl_version = GHLCONNECT_CONTACT_DATA_VERSION;

		$request_args = array(
			'body' 		=> $contact_data,
			'headers' 	=> array(
				'Authorization' => "Bearer {$ghlconnect_access_token}",
				'Version' 		=> $ghl_version
			),
		);

		$response = wp_remote_post( $endpoint, $request_args );
		$http_code = wp_remote_retrieve_response_code( $response );

		if ( 200 === $http_code || 201 === $http_code ) {

			$body = json_decode( wp_remote_retrieve_body( $response ) );
			$contact = $body->contact;
			return $contact;
		}

		return "";
    }
}

// Get Contact ID
if ( ! function_exists( 'ghlconnect_get_location_contact_id' ) ) {
    
    function ghlconnect_get_location_contact_id($contact_data) {

    	// Check if contact id is exists
    	$wp_user_email = $contact_data['email'];
    	$ghl_location_id = $contact_data['locationId'];
    	$ghl_id_key = 'ghl_id_' . $ghl_location_id;
    	$wp_user = get_user_by( 'email', $wp_user_email );

    	if ( $wp_user ) { // get_user_by() return false on failure
    		$wp_user_id = $wp_user->ID;    		
    		$ghl_contact_id = get_user_meta( $wp_user_id, $ghl_id_key, true );
			
			if ( !empty( $ghl_contact_id ) ) {
	    		return $ghl_contact_id;
			}
    	}

		$contact = ghlconnect_get_location_contact_data($contact_data);

		if ( !empty($contact) ) {

			$ghl_contact_id = $contact->id;

			if ( $wp_user ) {
	    		$wp_user_id = $wp_user->ID;
	    		add_user_meta( $wp_user_id, $ghl_id_key, $ghl_contact_id, true );
	    	}
			
			return $ghl_contact_id;
		}
    }
}

// Add Contact Tags
if ( ! function_exists( 'ghlconnect_location_add_contact_tags' ) ) {
    
    function ghlconnect_location_add_contact_tags($contactId, $tags) {

		$ghlconnect_access_token = get_option( 'ghlconnect_access_token' );
		$endpoint = GHLCONNECT_ADD_CONTACT_TAGS_API . "{$contactId}/tags";
		$ghl_version = GHLCONNECT_ADD_CONTACT_TAGS_VERSION;

		$request_args = array(
			'body' 		=> $tags,
			'headers' 	=> array(
				'Authorization' => "Bearer {$ghlconnect_access_token}",
				'Version' 		=> $ghl_version
			),
		);

		$response = wp_remote_post( $endpoint, $request_args );
		$http_code = wp_remote_retrieve_response_code( $response );

		if ( 200 === $http_code || 201 === $http_code ) {

			return wp_remote_retrieve_body( $response );			
		}
    }
}

// Add Contact to Workflow
if ( ! function_exists( 'ghlconnect_location_add_contact_to_workflow' ) ) {
    
    function ghlconnect_location_add_contact_to_workflow( $contactId, $workflow_id ) {

		$ghlconnect_access_token = get_option( 'ghlconnect_access_token' );
		$endpoint = GHLCONNECT_ADD_CONTACT_TO_WORKFLOW_API . "{$contactId}/workflow/{$workflow_id}";
		$ghl_version = GHLCONNECT_ADD_CONTACT_TO_WORKFLOW_VERSION;

		$request_args = array(
			'body' 		=> '',
			'headers' 	=> array(
				'Authorization' => "Bearer {$ghlconnect_access_token}",
				'Version' 		=> $ghl_version
			),
		);

		$response = wp_remote_post( $endpoint, $request_args );
		$http_code = wp_remote_retrieve_response_code( $response );

		if ( 200 === $http_code || 201 === $http_code ) {

			return wp_remote_retrieve_body( $response );			
		}
    }
}

// Sync User on Login in WP
function ghlconnect_sync_user_logged_in($user_login, $user) {

	$locationId = get_option( 'ghlconnect_locationId' );

	$contact_data = array(
		"locationId"    => $locationId,
        "firstName"     => $user->user_firstname,
        "lastName"      => $user->user_lastname,
        "email"         => $user->user_email
	);

	// Get Contact Data from the location.
	$contact = ghlconnect_get_location_contact_data($contact_data);
	$tags = $contact->tags;

	$meta_key = "ghl_{$locationId}_tags";

	update_user_meta( $user->ID, $meta_key, $tags );
}
add_action('wp_login', 'ghlconnect_sync_user_logged_in', 10, 2);

// Sync User on Register and update in WP
function ghlconnect_wp_user_on_register_and_update($user_id) {

	$locationId = get_option( 'ghlconnect_locationId' );
	$user = get_user_by('id', $user_id);

	$contact_data = array(
		"locationId"    => $locationId,
        "firstName"     => $user->user_firstname,
        "lastName"      => $user->user_lastname,
        "email"         => $user->user_email
	);

	// Get Contact Data
	// It will Upsert contact to GHL
	$contact = ghlconnect_get_location_contact_data($contact_data);

}
add_action('user_register', 'ghlconnect_wp_user_on_register_and_update', 10, 1);
add_action('profile_update', 'ghlconnect_wp_user_on_register_and_update', 10, 1);

