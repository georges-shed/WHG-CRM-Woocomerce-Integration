<?php
if ( ! defined( 'ABSPATH' ) ) exit;
// Register the Tab inside woocommerce.
if ( ! function_exists( 'ghlconnect_product_data_tab' ) ) {
    
    function ghlconnect_product_data_tab( $tabs ) {
        $tabs['ghlconnect-tab'] = array(
            'label'     => __( 'GHL Connect', 'ghl-connect' ),
            'target'    => 'ghlconnect-tab',
            'class'     => array(),
        );
        return $tabs;
    }
    add_filter( 'woocommerce_product_data_tabs', 'ghlconnect_product_data_tab' );
}


if ( ! function_exists( 'ghlconnect_single_product_settings_fields' ) ) {
    
    function ghlconnect_single_product_settings_fields() {
        
        global $post;
        $post_id = $post->ID;
        $reload_url = esc_url( admin_url( sanitize_text_field( basename( $_SERVER['REQUEST_URI']))));

        if( ! strpos( $reload_url, 'ghl_reload=1' ) ) {
            $reload_url .= '&ghl_reload=1';
        }
        ?>

        <div id='ghlconnect-tab' class = 'panel woocommerce_options_panel'>
        	<div class = 'options_group' > 
                <div class="ghlconnect-tab-field">
                    <label>Add tags on GHL account after successful purchase</label>
                    <select name="ghlconnect_location_tags[]" id="ghlconnect-tag-box" multiple="multiple">
                    <?php
                        echo ghlconnect_get_location_tag_options( $post_id);
                    ?>
                    </select>
                </div>

               

                <div class="ghlconnect-tab-field">
                    <label>Add workflow on GHL account after successful purchase</label>

                    <select name="ghlconnect_location_workflow[]" id="ghlconnect-wokflow-box" multiple="multiple">
                        <?php
                        
                        echo ghlconnect_get_location_workflow_options($post_id);
                        ?>
                    </select>
                </div>

                <div>
                    <a class="ghl_connect_reload button" href="<?php echo esc_url($reload_url); ?>">Reload Data</a>
                    <p class="description">Before select the above field click the "Reload Data".</p>
                </div>

    		</div>
        </div><?php
    }
    add_action('woocommerce_product_data_panels', 'ghlconnect_single_product_settings_fields');
}

// Save data 
if (!function_exists('ghlconnect_woocom_save_data')) {

    function ghlconnect_woocom_save_data($post_id) {

        // Check if the current user has permission to save data
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Check nonce
        if (isset($_POST['ghlconnect_nonce']) && wp_verify_nonce(sanitize_text_field( wp_unslash ($_POST['ghlconnect_nonce']) ) , 'ghlconnect_nonce_action')) {

            $ghlconnect_location_tags = isset($_POST['ghlconnect_location_tags']) ? ghlconnect_recursive_sanitize_array($_POST['ghlconnect_location_tags']) : array();
            
            $ghlconnect_location_workflow = isset($_POST['ghlconnect_location_workflow']) ? ghlconnect_recursive_sanitize_array($_POST['ghlconnect_location_workflow']) : array();

            // Additional checks or processing as needed

            // Update post meta
            update_post_meta($post_id, 'ghlconnect_location_tags', $ghlconnect_location_tags);
           
            update_post_meta($post_id, 'ghlconnect_location_workflow', $ghlconnect_location_workflow);

        }
    }

    // Add actions for saving data
    add_action('woocommerce_process_product_meta_simple', 'ghlconnect_woocom_save_data');
    add_action('woocommerce_process_product_meta_variable', 'ghlconnect_woocom_save_data');

    // // Add nonce field to the WooCommerce product form
    add_action('woocommerce_product_options_general_product_data', 'ghlconnect_add_nonce_field');

    function ghlconnect_add_nonce_field() {
        // Output nonce field
        wp_nonce_field('ghlconnect_nonce_action', 'ghlconnect_nonce');
    }
}




// for tags
if (!function_exists('ghlconnect_get_location_tag_options')) {
    function ghlconnect_get_location_tag_options($post_id)
    {
        $tags = ghlconnect_get_location_tags();
        $options    = "";
        $ghlconnect_location_tags = get_post_meta( $post_id, 'ghlconnect_location_tags', true );

        $ghlconnect_location_tags = ( !empty($ghlconnect_location_tags) ) ? $ghlconnect_location_tags :  [];

        foreach ($tags as $tag ) {
            $tag_name = $tag->name;
            $selected = "";

            if ( in_array( $tag_name, $ghlconnect_location_tags )) {
                $selected = "selected";
            }

            $options .= "<option value='{$tag_name}' {$selected}>";
            $options .= $tag_name;
            $options .= "</option>";
        }

        return $options;
        
    }
}



//  for workflows
if (!function_exists('ghlconnect_get_location_workflow_options')) {
    function ghlconnect_get_location_workflow_options($post_id)
    {
        $workflows = ghlconnect_get_location_workflows();
        $options    = "";
        $ghlconnect_location_workflow = get_post_meta( $post_id, 'ghlconnect_location_workflow', true );

        $ghlconnect_location_workflow = ( !empty($ghlconnect_location_workflow) ) ? $ghlconnect_location_workflow :  [];

        foreach ($workflows as $workflow ) {
            $workflow_id        = $workflow->id;
            $workflow_name      = $workflow->name;
            $workflow_status    = $workflow->status;
            $selected           = "";
            $disabled           = "";

            if ( in_array( $workflow_id, $ghlconnect_location_workflow )) {
                $selected = "selected";
            }

            if ( 'draft' == $workflow_status ) {
                $disabled = "disabled";
            }

            $options .= "<option value='{$workflow_id}' {$selected} {$disabled}>";
            $options .= $workflow_name;
            $options .= "</option>";
        }

        return $options;

    }
}


// Sanitize Array
function ghlconnect_recursive_sanitize_array( $array ) {
    foreach ( $array as $key => &$value ) {
        if ( is_array( $value ) ) {
            $value = recursive_sanitize_text_field( $value );
        }
        else {
            $value = sanitize_text_field( $value );
        }
    }

    return $array;
}

// reload Data so that if it exist that will be deleted.
if ( isset( $_GET['ghl_reload'] ) && absint($_GET['ghl_reload']) == 1 ) {
    $key_tags       = 'ghlconnect_location_tags';
    $key_workflow   = 'ghlconnect_location_workflow';
    //delete the previous data if any.
    delete_transient($key_tags);
    delete_transient($key_workflow);
}