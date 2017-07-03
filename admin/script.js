jQuery(document).ready(function(){

    jQuery('#sku_sf_wc_url_type').change(function(){
        var id = jQuery(this).val();
        var value = jQuery(this).find(':selected').text();
        jQuery('span#sku_url > #sku_url_adds').html(value);
    });
    
    
    jQuery('#sku_sf_wc_custom_link').keyup(function(){
        var value = jQuery(this).val();
        jQuery('span#sku_url > #sku_url_adds').html(value);
    });
    
     jQuery('#sku_sf_wc_product_url_format').keyup(function(){
        var value = jQuery(this).val();
        jQuery('span#product_sku_url > #product_sku_url_adds').html(value);
    });
    
    
});