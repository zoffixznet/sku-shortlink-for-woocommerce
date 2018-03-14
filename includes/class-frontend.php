<?php
if ( ! defined( 'WPINC' ) ) { die; }
 
class SKU_Shortlink_For_WooCommerce_Frontend {
    
    public function __construct() {
        if(!is_admin()){
            add_action('init',array($this,'add_permalink'));
            add_action('init',array($this,'custom_rewrite_tag'));
            add_filter('query_vars', array($this,'products_plugin_query_vars'));
            add_action( 'parse_request', array($this,'change_post_per_page_wpent' ),10,99);
            
        }
        add_filter('post_type_link',array($this,'change_product_post_link'),10,10);
    }
    
    public function custom_rewrite_tag() {
        add_rewrite_tag('%sku%', '([^/]*)/?');
        add_rewrite_tag('%any%', '([^/]*)/?');
        flush_rewrite_rules();
    }
    
    public function add_permalink(){
        $custom_link = get_option(SKU_SF_WC_SLUG.'_url_type',true); 
        if($custom_link == 'custom'){ $custom_link = get_option(SKU_SF_WC_SLUG. '_custom_link',true); } 
        else {
            $links = SKUSFWC()->url_types;
            $custom_link = $links[$custom_link];
        }
        
        $custom_link = untrailingslashit($custom_link);
        $removeSLASH = ltrim($custom_link, "/");
        $removeAERO = ltrim($removeSLASH, "^");
        $custom_link =  '^'.$removeAERO;
        $allowed_strs = array('%sku%','%any%');
        $posOfSku=999;
        $posOfAny=999;

        if(strpos($custom_link ,'%sku%') !== false) { $posOfSku = strpos($custom_link ,'%sku%'); }
        if(strpos($custom_link ,'%any%') !== false) { $posOfAny= strpos($custom_link ,'%any%'); }
        if($posOfAny < $posOfSku){ $posOfSku = 2; $posOfAny=1; }
        else{ $posOfSku = 1; $posOfAny=2; }
        
        $custom_link = str_replace('%sku%','([^/]*)/?',$custom_link);
        $custom_link = str_replace('%any%','([^/]*)/?',$custom_link); 
        add_rewrite_rule($custom_link,'index.php?any_sk=$matches['.$posOfAny.']&product_sku=$matches['.$posOfSku.']','top');
        flush_rewrite_rules();
    }

    public function add_permalink_force(){ 
        return true;
    }
    
    public function products_plugin_query_vars($vars) {
      $vars[] = 'product_sku';
      $vars[] = 'any_sk';
      return $vars;
     }

    public function change_post_per_page_wpent( $query ) { 
        if(! $query->matched_query == 'product_sku=sku' ){return $query;}
        if(is_admin()){return $query;}
        
        if(isset($query->query_vars['product_sku'])){
            $id = wc_get_product_id_by_sku($query->query_vars['product_sku']);
            
            if($id != 0){ 
                $post = get_post($id);
                $query->query_vars['page'] = '';
                $query->query_vars['product'] = $post->post_name;
                $query->query_vars['post_type'] = 'product';
                $query->query_vars['name'] = $post->post_name;                
            } else {
                $query->query_vars['page'] = '';
                $query->query_vars['product'] = $query->query_vars['product_sku'];
                $query->query_vars['post_type'] = 'product';
                $query->query_vars['name'] = $query->query_vars['product_sku'];
            }
                      
            
        }
        return $query;
    }
    
    public function change_product_post_link($link,$post){ 
        
        if(get_option(SKU_SF_WC_SLUG.'_modify_product_url',true) !== 'yes'){return $link;}
        if(is_admin()){
            if(get_option(SKU_SF_WC_SLUG.'_admin_modify_product_url',true) !== 'yes'){return $link;}    
        }
        
        $sku = get_post_meta($post->ID,'_sku',true);
        $id = $post->ID;
        $postname = $post->post_name;
        $category = wp_get_post_terms($id, 'product_cat');
        
        if(!empty($category) && !is_wp_error($category)){
            $category = $category[0]->slug;    
        } else {
            $category = '';
        }
        
        
        if(empty($sku)){return $link;}
        $site_url = trailingslashit(home_url());
        
        $product_url = get_option(SKU_SF_WC_SLUG.'_product_url_format',true);
        if(empty($product_url)){ $product_url = get_option(SKU_SF_WC_SLUG.'_url_type',true); }

        if($product_url == 'custom'){
            $product_url = get_option(SKU_SF_WC_SLUG. '_custom_link',true);
        } else {
            $links = SKUSFWC()->url_types;
            $product_url = isset($links[$product_url]) ? $links[$product_url] : $product_url;
        }
    
        $product_url = untrailingslashit($product_url);
        $product_url = str_replace('%sku%',$sku,$product_url);
        $product_url = str_replace('%id%',$id,$product_url);
        $product_url = str_replace('%postname%',$postname,$product_url);
        $product_url = str_replace('%category%',$category,$product_url);
        return $site_url.$product_url;
    }
    
}
