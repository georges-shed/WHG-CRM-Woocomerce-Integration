<?php
if ( ! defined( 'ABSPATH' ) ) exit;
add_action('init', function() {

    if ( isset( $_GET['code'] ) ) {
        $code = sanitize_text_field( $_GET['code'] );
        $ghlconnect_client_id           = get_option( 'ghlconnect_client_id' );
        $ghlconnect_client_secret       = get_option( 'ghlconnect_client_secret' );
        
        $result = ghlconnect_get_first_auth_code($code, $ghlconnect_client_id, $ghlconnect_client_secret);
        
        $ghlconnect_access_token = $result->access_token;
        $ghlconnect_refresh_token = $result->refresh_token;
        $ghlconnect_locationId = $result->locationId;
        // Save data
        update_option( 'ghlconnect_access_token', $ghlconnect_access_token );
        update_option( 'ghlconnect_refresh_token', $ghlconnect_refresh_token );
        update_option( 'ghlconnect_locationId', $ghlconnect_locationId );
        update_option( 'ghlconnect_location_connected', 1 );

        // delete old transient (if exists any)
        delete_transient('ghlconnect_location_tags');
        delete_transient('ghlconnect_location_workflow');

        wp_redirect( admin_url( 'admin.php?page=ib-ghlconnect-settings' ) );
        exit();
    }
});

add_action('init', function() {

    $ghlconnect_locationId = get_option( 'ghlconnect_locationId' );
    $is_access_token_valid = get_transient('is_access_token_valid');

    if ( ! empty( $ghlconnect_locationId ) && ! $is_access_token_valid ) {
        
        // renew the access token
        ghlconnect_get_new_access_token();
    }

});

function ghlconnect_get_new_access_token()
{
	$key = 'is_access_token_valid';
    $expiry = 59  * 60 * 24; // almost 1 day

	$ghlconnect_client_id 		= get_option( 'ghlconnect_client_id' );
	$ghlconnect_client_secret 	= get_option( 'ghlconnect_client_secret' );
	$refreshToken 			= get_option( 'ghlconnect_refresh_token' );
	
	$endpoint = GHLCONNECT_GET_TOKEN_API;
	$body = array(
		'client_id' 	=> $ghlconnect_client_id,
		'client_secret' => $ghlconnect_client_secret,
		'grant_type' 	=> 'refresh_token',
		'refresh_token' => $refreshToken
	);

	$request_args = array(
		'body' 		=> $body,
		'headers' 	=> array(
			'Content-Type' => 'application/x-www-form-urlencoded',
		),
	);

	$response = wp_remote_post( $endpoint, $request_args );
	$http_code = wp_remote_retrieve_response_code( $response );

	if ( 200 === $http_code ) {

		$body = json_decode( wp_remote_retrieve_body( $response ) );
		$new_ghlconnect_access_token = $body->access_token;
		$new_ghlconnect_refresh_token = $body->refresh_token;

		update_option( 'ghlconnect_access_token', $new_ghlconnect_access_token );
		update_option( 'ghlconnect_refresh_token', $new_ghlconnect_refresh_token );

	
		set_transient( $key, true, $expiry );
	}

	return null;
}

function ghlconnect_get_first_auth_code($code, $client_id, $client_secret){

    $endpoint = GHLCONNECT_GET_TOKEN_API;
    $body = array(
        'client_id'     => $client_id,
        'client_secret' => $client_secret,
        'grant_type'    => 'authorization_code',
        'code'          => $code
    );

    $request_args = array(
        'body'      => $body,
        'headers'   => array(
            'Content-Type' => 'application/x-www-form-urlencoded',
        ),
    );

    $response = wp_remote_post( $endpoint, $request_args );
    $http_code = wp_remote_retrieve_response_code( $response );

    if ( 200 === $http_code ) {

        $body = json_decode( wp_remote_retrieve_body( $response ) );
        return $body;
    }    
}