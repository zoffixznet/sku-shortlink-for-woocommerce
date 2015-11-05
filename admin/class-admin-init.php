<?php
/**
 * The admin-specific functionality of the plugin.
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */
if ( ! defined( 'WPINC' ) ) { die; }

class SKU_Shortlink_For_WooCommerce_Admin extends SKU_Shortlink_For_WooCommerce {

    /**
	 * Initialize the class and set its properties.
	 * @since      0.1
	 */
	public function __construct() {
        $this->frontend = new SKU_Shortlink_For_WooCommerce_Frontend;
        add_filter( 'plugin_row_meta', array($this, 'plugin_row_links' ), 10, 2 );
        add_action( 'admin_init', array( $this, 'admin_init' ),100);
        add_action( 'plugins_loaded', array( $this, 'init' ) );
        add_filter( 'woocommerce_get_settings_pages',  array($this,'settings_page') );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

    
    /**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() { 
        if(in_array($this->current_screen() , $this->get_screen_ids())) {
            wp_enqueue_script(SKU_SF_WC_SLUG.'_core_script', plugins_url('script.js',__FILE__), array('jquery'), SKU_SF_WC_VERSION, false ); 
        }
 
	}    
	/**
	 * Add a new integration to WooCommerce.
	 */
	public function settings_page( $integrations ) {
        foreach(glob(SKU_SF_WC_PATH.'admin/woocommerce-settings.php' ) as $file){
            $integrations[] = require_once($file);
        }
		return $integrations;
	}
    
    public function admin_init(){
       

    }
    
    
    
    /**
     * Gets Current Screen ID from wordpress
     * @return string [Current Screen ID]
     */
    public function current_screen(){
       $screen =  get_current_screen();
       return $screen->id;
    }
    
    /**
     * Returns Predefined Screen IDS
     * @return [Array] 
     */
    public function get_screen_ids(){
        $screen_ids = array();
        $screen_ids[] = 'woocommerce_page_wc-settings';
        return $screen_ids;
    }
    
    
    /**
	 * Adds Some Plugin Options
	 * @param  array  $plugin_meta
	 * @param  string $plugin_file
	 * @since 0.11
	 * @return array
	 */
	public function plugin_row_links( $plugin_meta, $plugin_file ) {
		if ( SKU_SF_WC_FILE == $plugin_file ) {
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=wc-settings&tab=products&section=sku_sf_wc'), __('Settings', SKU_SF_WC_TEXT_DOMAIN) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'https://wordpress.org/plugins/sku-shortlink-for-woocommerce/faq/', __('F.A.Q', SKU_SF_WC_TEXT_DOMAIN) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'https://github.com/technofreaky/sku-shortlink-for-woocommerce', __('View On Github',SKU_SF_WC_TEXT_DOMAIN) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'https://github.com/technofreaky/sku-shortlink-for-woocommerce/issues', __('Report Issue', SKU_SF_WC_TEXT_DOMAIN) );
            $plugin_meta[] = sprintf('&hearts; <a href="%s">%s</a>', 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9L76L92SD8YAQ', __('Donate', SKU_SF_WC_TEXT_DOMAIN) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'http://varunsridharan.in/plugin-support/', __('Contact Author', SKU_SF_WC_TEXT_DOMAIN) );
		}
		return $plugin_meta;
	}	    
}

?>