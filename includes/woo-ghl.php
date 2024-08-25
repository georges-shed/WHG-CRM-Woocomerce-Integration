<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function ghlconnect_create_contact_and_opportunity_on_new_order( $order_id ) {
    $order = wc_get_order($order_id); // Fetch the order

    // Get the current order status
    $order_status = $order->get_status();

    // Define statuses to check
    $valid_statuses = ['pending', 'processing', 'on-hold', 'ywraq-pending']; // Added ywraq-pending status

    // Check if the order status is valid
    if ( ! in_array( $order_status, $valid_statuses ) ) {
        return; // Exit if the order status is not valid
    }

    // Fetch the location ID
    $locationId = get_option( 'ghlconnect_locationId' );

    // Prepare contact data
    $contact_data = [
        "locationId"    => $locationId,
        "firstName"     => $order->get_billing_first_name(),
        "lastName"      => $order->get_billing_last_name(),
        "email"         => $order->get_billing_email(),
        "phone"         => $order->get_billing_phone(),
        "city"          => $order->get_billing_city(),
        "address1"      => $order->get_billing_address_1(),
        "state"         => $order->get_billing_state(),
        "postalCode"    => $order->get_billing_postcode(),
        "companyName"   => $order->get_billing_company(),
        "dateOfBirth"   => "2002-06-21",  // Example date of birth, replace with actual data if needed
        "source"        => "test contact source" // Example source, replace with actual data if needed
    ];
    $contactId = ghlconnect_get_location_contact_id($contact_data);

    // Collect product IDs
    $product_ids = array();
    foreach ( $order->get_items() as $item_id => $item ) {
        $product_ids[] = $item->get_product_id();
    }

    // Fetch meta data for all product IDs in one query
    $products_meta = array();
    foreach ( $product_ids as $product_id ) {
        $products_meta[ $product_id ] = array(
            'tags' => get_post_meta( $product_id, 'ghlconnect_location_tags', true ),
            'workflow' => get_post_meta( $product_id, 'ghlconnect_location_workflow', true ),
        );
    }

    // Process order items and apply tags/workflows
    foreach ( $order->get_items() as $item_id => $item ) {
        $product_id = $item->get_product_id();
        $product_meta = $products_meta[ $product_id ];

        if ( ! empty( $product_meta['tags'] ) ) {
            $tags = array( 'tags' => $product_meta['tags'] );
            ghlconnect_location_add_contact_tags( $contactId, $tags );
        }

        if ( ! empty( $product_meta['workflow'] ) ) {
            foreach ( $product_meta['workflow'] as $workflow_id ) {
                ghlconnect_location_add_contact_to_workflow( $contactId, $workflow_id );
            }
        }
    }

    // Add logic to create an opportunity in GHL
    ghlconnect_create_ghl_opportunity( $order, $contactId, $locationId );
}

function ghlconnect_create_contact_and_opportunity_on_order_status_change( $order_id, $old_status, $new_status ) {
    // Check if the new status is one of the valid statuses
    $valid_statuses = ['pending', 'processing', 'on-hold', 'ywraq-pending']; // Added ywraq-pending status
    if ( ! in_array( $new_status, $valid_statuses ) ) {
        return; // Exit if the new status is not valid
    }

    $order = wc_get_order($order_id); // Fetch the order

    // Fetch the location ID
    $locationId = get_option( 'ghlconnect_locationId' );

    // Prepare contact data
    $contact_data = [
        "locationId"    => $locationId,
        "firstName"     => $order->get_billing_first_name(),
        "lastName"      => $order->get_billing_last_name(),
        "email"         => $order->get_billing_email(),
        "phone"         => $order->get_billing_phone(),
        "city"          => $order->get_billing_city(),
        "address1"      => $order->get_billing_address_1(),
        "state"         => $order->get_billing_state(),
        "postalCode"    => $order->get_billing_postcode(),
        "companyName"   => $order->get_billing_company(),
        "dateOfBirth"   => "2002-06-21",  // Example date of birth, replace with actual data if needed
        "source"        => "test contact source" // Example source, replace with actual data if needed
    ];
    $contactId = ghlconnect_get_location_contact_id($contact_data);

    // Collect product IDs
    $product_ids = array();
    foreach ( $order->get_items() as $item_id => $item ) {
        $product_ids[] = $item->get_product_id();
    }

    // Fetch meta data for all product IDs in one query
    $products_meta = array();
    foreach ( $product_ids as $product_id ) {
        $products_meta[ $product_id ] = array(
            'tags' => get_post_meta( $product_id, 'ghlconnect_location_tags', true ),
            'workflow' => get_post_meta( $product_id, 'ghlconnect_location_workflow', true ),
        );
    }

    // Process order items and apply tags/workflows
    foreach ( $order->get_items() as $item_id => $item ) {
        $product_id = $item->get_product_id();
        $product_meta = $products_meta[ $product_id ];

        if ( ! empty( $product_meta['tags'] ) ) {
            $tags = array( 'tags' => $product_meta['tags'] );
            ghlconnect_location_add_contact_tags( $contactId, $tags );
        }

        if ( ! empty( $product_meta['workflow'] ) ) {
            foreach ( $product_meta['workflow'] as $workflow_id ) {
                ghlconnect_location_add_contact_to_workflow( $contactId, $workflow_id );
            }
        }
    }

    // Add logic to create an opportunity in GHL
    ghlconnect_create_ghl_opportunity( $order, $contactId, $locationId );
}

function ghlconnect_create_ghl_opportunity( $order, $contactId, $locationId ) {
    // Define the necessary IDs
    $pipelineId = 'UAYLwD4EEewOTHdn5d7t'; // Default pipeline ID
    $pipelineStageId = '72c55e28-5624-4885-a02f-210f065dace0'; // Default stage ID

    // Check if the order status is "wc-ywraq-pending"
    if ( $order->get_status() === 'ywraq-pending' ) {
        // Use a different pipeline and stage for "ywraq-pending"
        $pipelineId = 'VC0ypLig0hdqvLVqyBzG'; // Replace with your actual pipeline ID for ywraq-pending
        $pipelineStageId = 'ec61d8ff-63bd-48f4-b6bc-15171d9e62b4'; // Replace with your actual stage ID for ywraq-pending
    }

    // Set the source as "Website Order"
    $source = 'Website Order';

    $opportunity_data = [
        'pipelineId'      => $pipelineId,
        'locationId'      => $locationId,
        'name'            => 'Order ID: ' . $order->get_order_number(),
        'pipelineStageId' => $pipelineStageId,
        'status'          => 'open',
        'source'          => $source,
        'contactId'       => $contactId,
        'monetaryValue'   => $order->get_total(),
    ];

    ghlconnect_send_opportunity_to_ghl( $opportunity_data );
}

function ghlconnect_send_opportunity_to_ghl( $opportunity_data ) {
    $api_url = 'https://services.leadconnectorhq.com/opportunities/';
    
    $response = wp_remote_post( $api_url, [
        'headers' => [
            'Authorization' => 'Bearer pit-9f19fdee-d6dd-4883-a9f0-0e1e2a8b7a88', // Replace with your actual Bearer token
            'Version'       => '2021-07-28',
            'Content-Type'  => 'application/json',
        ],
        'body' => json_encode( $opportunity_data ),
    ]);

    if ( is_wp_error( $response ) ) {
        error_log( 'GHL Opportunity Creation Error: ' . $response->get_error_message() );
    } else {
        error_log( 'GHL Opportunity Created: ' . print_r( json_decode( $response['body'], true ), true ) );
    }
}

add_action( 'woocommerce_new_order', 'ghlconnect_create_contact_and_opportunity_on_new_order', 10, 1 );
add_action( 'woocommerce_order_status_changed', 'ghlconnect_create_contact_and_opportunity_on_order_status_change', 10, 3 );
