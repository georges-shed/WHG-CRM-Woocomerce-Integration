<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if (isset($_POST['contact_register_btn'])) {
			// Check if the checkbox is checked
			update_option('ghlconnect_contact_register_choice', "yes");
				$users = get_users();

				// Loop through each user
				foreach ($users as $user) {
					// Get user details
					$user_info = get_userdata($user->ID);

					//send to ghl contact
					$locationId = get_option( 'ghlconnect_locationId' );

					$contact_data = array(
						"locationId"    => $locationId,
						"firstName"     => $user_info->first_name,
						"lastName"      => $user_info->last_name,
						"email"         => $user_info->user_email,
						"phone"         =>  $user_info->billing_phone
					);

					// Get Contact Data
					// It will Upsert contact to GHL
					$contact = ghlconnect_get_location_contact_data($contact_data);
				}

		}
		
	}
?>

<form method="post" class="form-table">
    <?php $register_data=get_option('ghlconnect_contact_register_choice');
        $ghlconnect_location_connected	= get_option('ghlconnect_location_connected', GHLCONNECT_LOCATION_CONNECTED );
		?>
    <table>
        <tbody>
            <tr>
                <th scope="row">
                    <label>Add All Users to GHL?</label>
                </th>
                <td>
                    <?php if ($register_data==='yes' ) { ?>
                    <button class="ghl_connect_sync button" type="submit" name="contact_register_btn">Sync
                        Again</button>
                    <p class="description"> ALL users are sync in GHL</p>
                    <?php } else { ?>
                    <?php if($ghlconnect_location_connected && is_plugin_active('woocommerce/woocommerce.php')) { ?>
                    <button class="ghl_connect_sync button" type="submit" name="contact_register_btn">Sync
                        Users</button>
                    <?php }else { ?>
                    <button class="ghl_connect_sync button" type="submit" name="contact_register_btn" disabled>Sync
                        Users</button>
                    <p class="syncp">First Connect Yout GHL Subaccount.</p>
                    <?php } ?>
                    <?php } ?>
                </td>
            </tr>
        </tbody>
    </table>

</form>