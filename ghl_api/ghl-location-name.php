<?php

if ( ! function_exists( 'ghlconnect_location_name' ) ) {
    
    function ghlconnect_location_name($loc) {

    	$key = 'ghlconnect_location_name';
    	$expiry = 60  * 60 * 24; // 1 day

    	$name = get_transient($key);

    	// if ( !empty( $name ) ) {
    	// 	//delete_transient($key);
    	// 	return $name;
    	// }

		// $ghlconnect_locationId = get_option( 'ghlconnect_locationId' );
		$ghlconnect_access_token = get_option( 'ghlconnect_access_token' );

		$endpoint = "https://services.leadconnectorhq.com/locations/{$loc}";
		$ghl_version = '2021-07-28';

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
			$name = json_decode( $body )->location;
			set_transient( $key, $name, $expiry );
			return $name;

		}elseif( 401 === $http_code ){
			ghlconnect_get_new_access_token();
		}
    }
}