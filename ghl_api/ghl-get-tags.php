<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! function_exists( 'ghlconnect_get_location_tags' ) ) {
    
    function ghlconnect_get_location_tags() {

    	$key = 'ghlconnect_location_tags';
    	$expiry = 60  * 60 * 24; // 1 day

    	$tags = get_transient($key);

    	if ( !empty( $tags ) ) {
    		
    		return $tags;
    	}

		$ghlconnect_locationId = get_option( 'ghlconnect_locationId' );
		$ghlconnect_access_token = get_option( 'ghlconnect_access_token' );

		$endpoint = GHLCONNECT_GET_TAGS_API . "{$ghlconnect_locationId}/tags";
		$ghl_version = GHLCONNECT_GET_TAGS_VERSION;

		$request_args = array(
			'headers' => array(
				'Authorization' => "Bearer {$ghlconnect_access_token}",
				'Content-Type' => 'application/json',
				'Version' => $ghl_version,
			),
		);

		$response = wp_remote_get( $endpoint, $request_args );
		$http_code = wp_remote_retrieve_response_code( $response );

		if ( 200 === $http_code ) {

			$body = wp_remote_retrieve_body( $response );
			$tags = json_decode( $body )->tags;
			set_transient( $key, $tags, $expiry );
			return $tags;

		}elseif( 401 === $http_code ){
			ghlconnect_get_new_access_token();
		}
    }
}