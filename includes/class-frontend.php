<?php
if ( ! defined( 'WPINC' ) ) { die; }
 
class SKU_Shortlink_For_WooCommerce_Frontend {

    /**
     * Class Constructor
     */
    public function __construct() {
        add_action('init',array($this,'add_permalink'));
        add_filter('query_vars', array($this,'products_plugin_query_vars'));
        add_action( 'parse_request', array($this,'change_post_per_page_wpent' ));
    }
    
    public function add_permalink($permalink = ''){
        if(!empty($permalink)){ $custom_link = $permalink; }
        //untrailingslashit
        $custom_link = get_option(SKU_SF_WC_SLUG.'_url_type',true);
        
        if($custom_link == 'custom'){
            $custom_link = get_option(SKU_SF_WC_SLUG. '_custom_link',true);
            
        } else {
            $links = SKUSFWC()->url_types;
            $custom_link = $links[$custom_link];
        }
        
        $custom_link = untrailingslashit($custom_link);
        $removeSLASH = ltrim($custom_link, "/");
        $removeAERO = ltrim($removeSLASH, "^");
        $custom_link = '^'.$removeAERO;
        $custom_link = str_replace('%sku%','([^/]*)/?',$custom_link);
        add_rewrite_rule($custom_link,'index.php?product_sku=$matches[1]','top');
        flush_rewrite_rules();
        return true;
    }
    



    public function add_permalink_force($permalink = ''){
        $this->add_permalink($permalink);        
        flush_rewrite_rules();
    }
    

    public function products_plugin_query_vars($vars) {
      $vars[] = 'product_sku';
      return $vars;
     }


    public function change_post_per_page_wpent( $query ) {
        if(is_admin()){return $query;}
        if(isset($query->query_vars['product_sku'])){
            $id = wc_get_product_id_by_sku($query->query_vars['product_sku']);
            $post = get_post($id);

            $query->query_vars['page'] = '';
            $query->query_vars['product'] = $post->post_name;
            $query->query_vars['post_type'] = 'product';
            $query->query_vars['name'] = $post->post_name    ;
        }
        return $query;
    }    
    
}
?>