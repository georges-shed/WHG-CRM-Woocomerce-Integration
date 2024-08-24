<?php
    if ( ! defined( 'ABSPATH' ) ) exit; 
    define('GHLCONNECT_AUTH_URL',"https://app.ibsofts.com/crm-connect/");
    define('GHLCONNECT_AUTH_END_POINT','https://marketplace.gohighlevel.com/oauth/chooselocation');
    //for Get Contact Data
    define('GHLCONNECT_CONTACT_DATA_API',"https://services.leadconnectorhq.com/contacts/upsert");
    define('GHLCONNECT_CONTACT_DATA_VERSION','2021-07-28');
    
    
    // Add Contact Tags
    define('GHLCONNECT_ADD_CONTACT_TAGS_API',"https://services.leadconnectorhq.com/contacts/");
    define('GHLCONNECT_ADD_CONTACT_TAGS_VERSION','2021-07-28');
    
    
    // Add Contact to Campaign
    define('GHLCONNECT_ADD_CONTACT_TO_CAMPAIGN_API',"https://services.leadconnectorhq.com/contacts/");
    define('GHLCONNECT_ADD_CONTACT_TO_CAMPAIGN_VERSION','2021-07-28');
    
    //Add Contact to Workflow
    define('GHLCONNECT_ADD_CONTACT_TO_WORKFLOW_API',"https://services.leadconnectorhq.com/contacts/");
    define('GHLCONNECT_ADD_CONTACT_TO_WORKFLOW_VERSION','2021-07-28');
    
    //ghl-get-campaigns.php
    define('GHLCONNECT_GET_CAMPAIGNS_API',"https://services.leadconnectorhq.com/campaigns/?locationId=");
    define('GHLCONNECT_GET_CAMPAIGNS_VERSION','2021-04-15');
    
    //ghl-get-tags.php
    define('GHLCONNECT_GET_TAGS_API',"https://services.leadconnectorhq.com/locations/");
    define('GHLCONNECT_GET_TAGS_VERSION','2021-07-28');
    
    //ghl-get-token.php
    define('GHLCONNECT_GET_TOKEN_API',"https://services.leadconnectorhq.com/oauth/token");
    
    //ghl-get-workflows.php
    define('GHLCONNECT_GET_WORKFLOWS_API',"https://services.leadconnectorhq.com/workflows/?locationId=");
    define('GHLCONNECT_GET_WORKFLOWS_VERSION','2021-07-28');

?>