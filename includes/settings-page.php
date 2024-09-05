<?php
 if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'GHLCONNECT_Settings_Page' ) ) {
	class GHLCONNECT_Settings_Page {

        public function __construct() {
			add_action( 'admin_menu', array( $this, 'ghlconnect_create_menu_page' ) );
			add_action( 'admin_post_ghlconnect_admin_settings', array( $this, 'ghlconnect_save_settings' ) );
			add_filter( 'plugin_action_links_' . GHLCONNECT_PLUGIN_BASENAME , array( $this , 'ghlconnect_add_settings_link' ) );
		
		}

        public function ghlconnect_create_menu_page() {
	    
			$page_title 	= __( 'GHL Connect for WooCommerce', 'ghl-connect' );
			$menu_title 	= __( 'GHL Connect for WooCommerce', 'ghl-connect' );
			$capability 	= 'manage_options';
			$menu_slug 		= 'ib-ghlconnect';
			$callback   	= array( $this, 'ghlconnect_page_content' );
			$icon_url   	= 'dashicons-admin-plugins';
			add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $callback, $icon_url );
	
		}	
		
        public function ghlconnect_page_content() {
            // check user capabilities to access the setting page.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			$default_tab = null;
			$tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : $default_tab;
			?>

<div class="wrap main-con">

    <div class="ghl-header">
        <!-- Logo -->
        <div class="logo">
            <img src="<?php echo esc_url(plugins_url('images/ghlconnect-logo.png', __DIR__)); ?>"
                alt="GHLCONNECT-Logo" />

        </div>

        <h1>GHL Connect for WooCommerce</h1>
    </div>
    <?php
    if (!is_plugin_active('woocommerce/woocommerce.php')) {
         ?>
    <div class="notice notice-error wooerror">
        <p>
            <strong>Error:</strong>
            <em>GHL Connect for WooCommerce</em> plugin won't execute
            because the required Woocommerce plugin is not active. Install <b>Woocommerce</b>.
        </p>
    </div>
    <?php
    }
	
	?>

<div class="ghl-container">
    <div class="ghl-content">
        <div class="ghl-tabs">
            <h2 class="nav-tab-wrapper-vertical">
                <a href="?page=ib-ghlconnect&tab=pro"
                    class="nav-tab <?php if($tab==='pro'):?>nav-tab-active<?php endif; ?>">Upgrade to PRO</a>
                <a href="?page=ib-ghlconnect"
                    class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>">Connect with
                    GHL</a>
                <a href="?page=ib-ghlconnect&tab=option"
                    class="nav-tab <?php if($tab==='option'):?>nav-tab-active<?php endif; ?>">Trigger Options</a>
                <a href="?page=ib-ghlconnect&tab=sync"
                    class="nav-tab <?php if($tab==='sync'):?>nav-tab-active<?php endif; ?>">Sync
                    Users</a>
                <a href="?page=ib-ghlconnect&tab=support"
                    class="nav-tab <?php if($tab==='support'):?>nav-tab-active<?php endif; ?>">Help</a>
                <a href="?page=ib-ghlconnect&tab=logging"
                    class="nav-tab <?php if($tab==='logging'):?>nav-tab-active<?php endif; ?>">Logging</a>
            </h2>
        </div>

        <div class="tab-content">
            <?php switch($tab) :
                case 'pro':
                    require_once plugin_dir_path( __FILE__ )."/ghl-connect-upgrade-to-premium.php"; 
                    break;
                case 'option':
                    require_once plugin_dir_path( __FILE__ )."/woo-trigger-form.php";
                    break;
                case 'sync':
                    require_once plugin_dir_path( __FILE__ )."/ghl-connect-sync-users.php";
                    break;
                case 'support':
                    require_once plugin_dir_path( __FILE__ )."/help-page.php";
                    break;
                case 'logging':
                    require_once plugin_dir_path( __FILE__ )."/logging.php";
                    break;
                default:
                    require_once plugin_dir_path( __FILE__ )."/settings-form.php"; 
                    break;
            endswitch; ?>
        </div>
    </div>
</div>

</div>

<?php	
	    		
		}
		public function ghlconnect_save_settings() {
			check_admin_referer( "ghl-connect" );
	        $ghlconnect_order_status 	= sanitize_text_field( $_POST['ghlconnect_order_status'] );
	        $referer = esc_url_raw(sanitize_text_field($_POST['_wp_http_referer']));

	       //save data from the trigger options.
	        update_option( 'ghlconnect_order_status', $ghlconnect_order_status );

			wp_redirect( $referer );
        	exit();
		}

		public function ghlconnect_add_settings_link( $links ) {
	        $newlink = sprintf( "<a href='%s'>%s</a>" , admin_url( 'admin.php?page=ib-ghlconnect&tab=pro' ) , __( 'Settings' , 'ghl-connect' ) );
	        $links[] = $newlink;
			$links[] = '<a href="https://www.ibsofts.com/cart/?add-to-cart=5818" target="_blank"><b>Upgrade to Premium</b></a>';
	        return $links;
	    }

    }
    new GHLCONNECT_Settings_Page();
}