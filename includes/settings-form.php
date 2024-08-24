<?php
	 if ( ! defined( 'ABSPATH' ) ) exit;
	if ( isset( $_GET['connection_status'] ) && sanitize_text_field($_GET['connection_status']) === 'success' ) {
		$ghlconnect_access_token 	= sanitize_text_field( $_GET['acctn'] );
		$ghlconnect_refresh_token 	= sanitize_text_field( $_GET['reftn'] );
		$ghlconnect_locationId 	    = sanitize_text_field( $_GET['locid'] );
		$ghlconnect_client_id 		= sanitize_text_field( $_GET['cntid'] );
		$ghlconnect_client_secret 	= sanitize_text_field( $_GET['cntst'] );
		// Save data
	    update_option( 'ghlconnect_access_token', $ghlconnect_access_token );
	    update_option( 'ghlconnect_refresh_token', $ghlconnect_refresh_token );
	    update_option( 'ghlconnect_locationId', $ghlconnect_locationId );
	    update_option( 'ghlconnect_client_id', $ghlconnect_client_id );
	    update_option( 'ghlconnect_client_secret', $ghlconnect_client_secret );
	    update_option( 'ghlconnect_location_connected', 1 );
        update_option( 'ghlconnect_loc_name', ghlconnect_location_name($ghlconnect_locationId)->name);
	    //  (delete if any old transient  exists )
	    delete_transient('ghlconnect_location_tags');
	    delete_transient('ghlconnect_location_wokflow');

	    wp_redirect('admin.php?page=ib-ghlconnect');
	}
    
	$ghlconnect_location_connected	= get_option( 'ghlconnect_location_connected', GHLCONNECT_LOCATION_CONNECTED );
	$ghlconnect_client_id 			= get_option( 'ghlconnect_client_id' );
	$ghlconnect_client_secret 		= get_option( 'ghlconnect_client_secret' );
	$ghlconnect_locationId 		    = get_option( 'ghlconnect_locationId' );
	$redirect_page 				    = get_site_url(null, '/wp-admin/admin.php?page=ib-ghlconnect');
	$redirect_uri 				    = get_site_url();
	$client_id_and_secret 		    = '';

	$auth_end_point = GHLCONNECT_AUTH_END_POINT;
	$scopes = "workflows.readonly contacts.readonly contacts.write campaigns.readonly conversations/message.readonly conversations/message.write forms.readonly locations.readonly locations/customValues.readonly locations/customValues.write locations/customFields.readonly locations/customFields.write opportunities.readonly opportunities.write users.readonly links.readonly links.write surveys.readonly users.write locations/tasks.readonly locations/tasks.write locations/tags.readonly locations/tags.write locations/templates.readonly calendars.write calendars/groups.readonly calendars/groups.write forms.write medias.readonly medias.write";

    $connect_url = GHLCONNECT_AUTH_URL . "?get_code=1&redirect_page={$redirect_page}";

	if ( ! empty( $ghlconnect_client_id ) && ! str_contains( $ghlconnect_client_id, 'lq4sb5tt' ) ) {
		
		$connect_url = $auth_end_point . "?response_type=code&redirect_uri={$redirect_uri}&client_id={$ghlconnect_client_id}&scope={$scopes}";
	}
	
?>

<div id="ib-ghlconnect">
    <h1> <?php esc_html_e('Connect With Your GHL Subaccount', 'ghl-connect'); ?> </h1>
    <hr />
    <table class="form-table" role="presentation">
		<tbody>
			<tr>
				<th scope="row">
					<label> <?php esc_html_e('Connect GHL Subaccount Location', 'ghl-connect'); ?> </label>
				</th>
				<td>
					<?php if ($ghlconnect_location_connected) { ?>
						<div class="connected-location">
							<button class="button button-connected" disabled>Connected</button>
							<!-- Show success message after connection -->
							<?php if (isset($_GET['connected']) && sanitize_text_field($_GET['connected']) === 'true') { ?>
							<p class="success-message">You have successfully connected to Subaccount Location ID: <?php echo esc_html($ghlconnect_locationId); ?></p>
						<?php } ?>
							<p class="description">To connect another subaccount location, click below:</p>
							<a class="ghl_connect button" href="<?php echo esc_url($connect_url); ?>">Connect Another Subaccount</a>	
						</div>
					<?php } else { ?>
						<div class="not-connected-location">
							<p class="description">You're not connected to any subaccount location yet.</p>
							<a class="ghl_connect button" href="<?php echo esc_url($connect_url); ?>">Connect GHL Subaccount</a>
						</div>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e('Your Connected GHL Subaccount LocationID', 'ghl-connect'); ?></label>
				</th>
				<td>
					<?php if ($ghlconnect_location_connected) { ?>
						<p class="description">Location ID: <?php echo esc_html($ghlconnect_locationId); ?></p>
					<?php } else { ?>
						<p class="description">You are not connected yet. Please connect by clicking the above button</p>
					<?php } ?>
				</td>
			</tr>

		</tbody>
    </table>
	
</div>