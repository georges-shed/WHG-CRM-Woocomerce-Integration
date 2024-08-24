<?php
function create_ghl_invoices($order){
    $locationId = get_option('ghlconnect_locationId');
    $ghlconnect_access_token = get_option('ghlconnect_access_token' );
    $locationName=get_option('ghlconnect_loc_name');
    $woo_contact_id=get_option('woo_contact_id');
    $ghl_invoice_number=ghl_create_invoice_number();
    //fetch the items details
    if ($order) {
      $order_date = $order->get_date_created(); 
      // Format the order date to only include the date part
      $formatted_order_date = $order_date ? $order_date->date('Y-m-d') : '2024-01-01';
      //get the discount
      $discount_amount = intval($order->get_discount_total());
    }


    // Initialize an array to store item information
    $item_array = array();

    // Check if the order exists
    if ($order) {
        // Get the items in the order
        $items = $order->get_items();
        $product_name = '';
        $product_price='';
        $product_quantity='';
        $product_currency='';
        // Loop through each item
        foreach ($items as $item_id => $item) {
            // Get the product name
            $product_name = $item->get_name();

            // Get the product price
            $product_price = $item->get_total();

            // Get the product quantity
            $product_quantity = $item->get_quantity();

            // Get the product currency
            $product_currency = $order->get_currency();


            // Add item information to the array
            $item_array[] = array(
                "name"        => $product_name,
                "currency"    => $product_currency,
                "amount"      => $product_price,
                "qty"         => $product_quantity,
            );
        }
    }
  $body_data = [
    'altId' => $locationId,
    'altType' => 'location',
    'name' => $order->get_billing_first_name() . ' Invoice',
    'businessDetails' => [
        'name' => $locationName
    ],
    'currency' => $product_currency,
    'items' => array_map(function ($item) {
        return [
            'name' => $item['name'],
            'currency' => $item['currency'],
            'amount' => $item['amount'],
            'qty' => $item['qty']
        ];
    }, $item_array),
    'discount' => [
        'value' => $discount_amount,
        'type' => 'percentage'
    ],
    'title' => 'INVOICE',
    'contactDetails' => [
        'id' => $woo_contact_id,
        'name' => $order->get_billing_first_name(),
        'phoneNo' => $order->get_billing_phone(),
        'email' => $order->get_billing_email(),
        'address' => [
            'addressLine1' => $order->get_billing_address_1(),
            'addressLine2' => $order->get_billing_address_2(),
            'city' => $order->get_billing_city(),
            'state' => $order->get_billing_state(),
            'countryCode' => $order->get_billing_country(),
            'postalCode' => $order->get_billing_postcode()
        ]
    ],
    'invoiceNumber' => $ghl_invoice_number,
    'issueDate' => $formatted_order_date,
    'liveMode' => true
];

// Adding the JSON-encoded post fields to $body_data
$body_data['postfields'] = json_encode($body_data);

// Implement auth V2 GHL API
$ghlconnect_access_token = get_option('ghlconnect_access_token');
$endpoint = "https://services.leadconnectorhq.com/invoices/";
$ghl_version = '2021-07-28';

$request_args = [
    'body'    => $body_data['postfields'],
    'headers' => [
        'Authorization' => "Bearer {$ghlconnect_access_token}",
        'Version'        => $ghl_version,
        'Content-Type'   => 'application/json',
        'Accept'         => 'application/json',
    ],
];

$response = wp_remote_post($endpoint, $request_args);
$http_code = wp_remote_retrieve_response_code($response);
if (200 === $http_code || 201 === $http_code) {
    $body = json_decode(wp_remote_retrieve_body($response), true);

} else {
    // Handle errors
    echo "Error: HTTP Code $http_code";
}

  }
?>