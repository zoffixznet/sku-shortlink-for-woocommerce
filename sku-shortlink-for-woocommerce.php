<?php
/**
 * Plugin Name:       SKU Shortlink For WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/SKU-Shortlink-For-WooCommerce/
 * Description:       SKU Shortlink For WooCommerce
 * Version:           0.2
 * Author:            Varun Sridharan
 * Author URI:        http://varunsridharan.in
 * Text Domain:       sku-shortlink-for-woocommerce
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt 
 * GitHub Plugin URI: @TODO
 */

if ( ! defined( 'WPINC' ) ) { die; }
 
class SKU_Shortlink_For_WooCommerce {
	/**
	 * @var string
	 */
	public $version = '0.2';

	/**
	 * @var WooCommerce The single instance of the class
	 * @since 2.1
	 */
	protected static $_instance = null;
    
    protected static $functions = null;
    
    public $url_types = null;

    /**
     * Creates or returns an instance of this class.
     */
    public static function get_instance() {
        if ( null == self::$_instance ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
    
    /**
     * Class Constructor
     */
    public function __construct() {
        $link = get_option('woocommerce_permalinks',true);
        $plink = trailingslashit($link['product_base']);
        $this->url_types = array(0 => $plink.'sku/%sku%/', 1 => $plink.'%sku%/', 2 => '%sku%/', 'custom' => 'custom link');
        
        $this->define_constant();
        $this->load_required_files();
        $this->init_class();

		add_action('plugins_loaded', array( $this, 'after_plugins_loaded' ));
        add_filter('load_textdomain_mofile',  array( $this, 'load_plugin_mo_files' ), 10, 2);
    }
    
    /**
     * Loads Required Plugins For Plugin
     */
    private function load_required_files(){
       $this->load_files(SKU_SF_WC_PATH.'includes/class-frontend.php');
       if($this->is_request('admin')){
           $this->load_files(SKU_SF_WC_PATH.'admin/class-*.php');
       } 
    }
    
    /**
     * Inits loaded Class
     */
    private function init_class(){
        $this->frontend = new SKU_Shortlink_For_WooCommerce_Frontend;
        if($this->is_request('admin')){
            $this->admin = new SKU_Shortlink_For_WooCommerce_Admin;
        } 
    }
    

    protected function load_files($path,$type = 'require'){
        foreach( glob( $path ) as $files ){
            if($type == 'require'){
                require_once( $files );
            } else if($type == 'include'){
                include_once( $files );
            }
            
        } 
    }
    
    /**
     * Set Plugin Text Domain
     */
    public function after_plugins_loaded(){
        load_plugin_textdomain(SKU_SF_WC_TEXT_DOMAIN, false, SKU_SF_WC_LANGUAGE_PATH );
    }
    
    /**
     * load translated mo file based on wp settings
     */
    public function load_plugin_mo_files($mofile, $domain) {
        if (SKU_SF_WC_TEXT_DOMAIN === $domain)
            return SKU_SF_WC_LANGUAGE_PATH.'/'.get_locale().'.mo';

        return $mofile;
    }
    
    /**
     * Define Required Constant
     */
    private function define_constant(){
        $this->define('SKU_SF_WC_NAME','SKU Shortlink For WooCommerce'); # Plugin Name
        $this->define('SKU_SF_WC_SLUG','sku_sf_wc'); # Plugin Slug
        $this->define('SKU_SF_WC_PATH',plugin_dir_path( __FILE__ )); # Plugin DIR
        $this->define('SKU_SF_WC_LANGUAGE_PATH',SKU_SF_WC_PATH.'languages');
        $this->define('SKU_SF_WC_TEXT_DOMAIN','sku-shortlink-for-woocommerce'); #plugin lang Domain
        $this->define('SKU_SF_WC_URL',plugins_url('', __FILE__ )); 
        $this->define('SKU_SF_WC_FILE',plugin_basename( __FILE__ ));
        $this->define('SKU_SF_WC_VERSION', $this->version);
    }
    
    /**
	 * Define constant if not already set
	 * @param  string $name
	 * @param  string|bool $value
	 */
    protected function define($key,$value){
        if(!defined($key)){
            define($key,$value);
        }
    }
    

    /**
     * Adds Filter / Action
     */
    protected function add_filter_action($key,$value,$type = 'action' , $priority = 10, $variable = 1){
        if($type == 'action'){
            add_action($key,$value,$priority,$variable);        
        } else if($type == 'filter'){
            add_filter($key,$value,$priority,$variable);        
        } else {
            return false;
        }
    }

    
	/**
	 * What type of request is this?
	 * string $type ajax, frontend or admin
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}
    
    
    
}

SKU_Shortlink_For_WooCommerce::get_instance();
function SKUSFWC(){
    return SKU_Shortlink_For_WooCommerce::get_instance();
}
?>