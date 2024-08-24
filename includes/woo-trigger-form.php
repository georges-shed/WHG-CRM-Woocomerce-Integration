<?php
  if ( ! defined( 'ABSPATH' ) ) exit; 
?>

<div id="ghlconnect-options">
    <h1> <?php esc_html_e('Customize Your Woocommerce Order Status', 'ghl-connect'); ?> </h1>
    <hr />

    <form id="ghlconnect-settings-form" method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">

        <?php wp_nonce_field('ghl-connect'); ?>

        <input type="hidden" name="action" value="ghlconnect_admin_settings">

        <table class="form-table" role="presentation">

            <tbody>

                <tr>
                    <th scope="row">
                        <label> <?php esc_html_e( 'Select WooCommerce order status to trigger:', 'ghl-connect' ); ?>
                        </label>

                    </th>
                    <td>
                        <?php
						if (!is_plugin_active('woocommerce/woocommerce.php')) {
							?>
                        <select name='ghlconnect_order_status'>
                            <option value="">Select option</option>
                        </select>
                        <?php
						     
                        }
						else{
							?>
                        <select name='ghlconnect_order_status'>
                            <?php 
							echo wp_kses(
                            ghlconnect_fetch_all_order_statuses(),
                            array(
                                'option'      => array(
                                    'value'  => array(),
									'selected'=>array()
                                )
                            )
                        ); ?>
                        </select>
                        <?php
                        }
                        ?>


                    </td>
                </tr>
            </tbody>
        </table>

        <div>
            <button class="ghl_connect button" type="submit" name="ghl_trigger">Update Settings</button>
        </div>

    </form>
</div>

<?php

//fetch the order status.
function ghlconnect_fetch_all_order_statuses() {

	$order_statuses = wc_get_order_statuses();
	$ghlconnect_order_status = get_option('ghlconnect_order_status');
	$selected = !empty($ghlconnect_order_status) ? $ghlconnect_order_status : 'wc-processing';

	$statuses = "";
	foreach ( $order_statuses as $key => $status ) {

		$selected_status = ( $selected == $key ) ? 'selected' : '';
		$statuses .= "<option value='{$key}' {$selected_status}> {$status} </option>";
	}

	return $statuses;
}