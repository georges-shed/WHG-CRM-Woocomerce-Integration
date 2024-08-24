<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! function_exists( 'ghlconnect_get_location_workflows' ) ) {
    
    function ghlconnect_get_location_workflows() {

    	$key = 'ghlconnect_location_workflow';
    	$expiry = 60  * 60 * 24; // 1 day

    	$workflows = get_transient($key);

    	if ( !empty( $workflows ) ) {    		
    		//delete_transient($key);
    		return $workflows;
    	}

		$ghlconnect_locationId = get_option( 'ghlconnect_locationId' );
		$ghlconnect_access_token = get_option( 'ghlconnect_access_token' );

		$endpoint = GHLCONNECT_GET_WORKFLOWS_API . "{$ghlconnect_locationId}";
		$ghl_version = GHLCONNECT_GET_WORKFLOWS_VERSION;

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
			$workflows = json_decode( $body )->workflows;
			set_transient( $key, $workflows, $expiry );
			return $workflows;

		}elseif( 401 === $http_code ){

			ghlconnect_get_new_access_token();
			
		}
    }
}