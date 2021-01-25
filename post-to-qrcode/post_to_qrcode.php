<?php 
/*
Plugin name: Qr Code
Plugin URL:www.coderex.co
Description: Scan QR code and redirect to URL
Version:1.0.0
Author: CoderRex
Author URI: www.coderex.co
License:
Text Domain:post-to-qrcode
Domain Path:
*/
// function wordcount_activation_hook(){  
// }
// function wordcount_deactivation_hook(){
// }
// register_activation_hook(__FILE__,'wordcount_activation_hook');
// register_activation_hook(__FILE__,'wordcount_deactivation_hook');


$pqrc_countries = array(
    __( 'Afganistan', 'post-to-qrcode' ),
    __( 'Bangladesh', 'post-to-qrcode' ),
    __( 'Bhutan', 'post-to-qrcode' ),
    __( 'India', 'post-to-qrcode' ),
    __( 'Pakistan', 'post-to-qrcode' ),
    __( 'Nepal', 'post-to-qrcode' ),
    __( 'Srilanka', 'post-to-qrcode' ),
    __( 'Maldiv', 'post-to-qrcode' )
);

function pqrc_init(){
    global $pqrc_countries;
    $pqrc_countries = apply_filters( 'pqrc_countries', $pqrc_countries);
}
add_action("init",'pqrc_init');

function qrcode_load_textdomain(){
    load_plugin_textdomain('post-to-qrcode',false,dirname(__FILE__)."/languages");
}
add_action("plugin_loaded",'qrcode_load_textdomain');

function pqrc_display_qrcode($content){
    $current_post_id = get_the_ID();
    $current_post_title = get_the_title($current_post_id);
    $current_post_url = urlencode(get_the_permalink($current_post_id));
    $current_post_type = get_post_type($current_post_id);

    /**
     * Post type check
     */
    $excluded_post_type = apply_filters('pqrc_excluded_post_type',array());
    if(in_array($current_post_type,$excluded_post_type)){
        return $content;
    }

    /**
     * Dimension hook
     */

    $height = get_option('pqrc_height');
    $width = get_option('pqrc_width');

    $height = $height ? $height : 180;
    $width = $width ? $width : 180;
    $dimention = apply_filters('pqrc_qrcode_dimension',"{$width}x{$height}");
    $image_src = sprintf('https://api.qrserver.com/v1/create-qr-code/?size=%s&ecc=L&qzone=1&data=%s',$dimention,$current_post_url);
    $content .= sprintf("<div class='qrcode'><img src='%s'alt='%s'/></div>",$image_src,$current_post_title);
    return $content;
}
add_filter('the_content','pqrc_display_qrcode');

function pqrc_settings_init(){
    add_settings_section('pqrc_section',__( 'QR Code Dimension Settings', 'post-to-qrcode'),'pqrc_display_dimension','general');
    add_settings_field('pqrc_height',__( 'QR Code Height', 'post-to-qrcode'),'pqrc_display_field','general','pqrc_section',array('pqrc_height'));
    add_settings_field('pqrc_width',__( 'QR Code Width', 'post-to-qrcode'),'pqrc_display_field','general','pqrc_section',array('pqrc_width'));
    add_settings_field('pqrc_select',__( 'Select Country', 'post-to-qrcode'),'pqrc_display_select','general','pqrc_section');
    add_settings_field('pqrc_checkbox',__( 'Check Country', 'post-to-qrcode'),'pqrc_display_checkbox','general','pqrc_section');
    add_settings_field('pqrc_toggle',__( 'Simple Toggle', 'post-to-qrcode'),'pqrc_display_toggle','general','pqrc_section');

    register_setting('general','pqrc_height',array('sanitize_callback'=>'esc_attr'));
    register_setting('general','pqrc_width',array('sanitize_callback'=>'esc_attr'));
    register_setting('general','pqrc_select',array('sanitize_callback'=>'esc_attr'));
    register_setting('general','pqrc_checkbox');
    register_setting('general','pqrc_toggle');

}

function pqrc_display_dimension(){
    echo "<p>".__('Settings for Posts to QR Plugin','post-to-qrcode')."</p>";
}
function pqrc_display_field($args){
    $field = get_option($args[0]);
    printf("<input name='%s' id='%s' value='%s'/>",$args[0],$args[0],$field);
}

function pqrc_display_select(){
    global $pqrc_countries;
    $option = get_option('pqrc_select');
    
    printf("<select id='%s' name='%s'>",'pqrc_select','pqrc_select');
    foreach($pqrc_countries  as $country){
        $selected = '';
        if($option == $country)$selected='selected';
        
        printf('<option value="%s" %s>%s</option>',$country,$selected,$country);

    }
    echo "</select>";
}

function pqrc_display_checkbox(){
    global $pqrc_countries;
    $option = get_option('pqrc_checkbox');
    

    foreach($pqrc_countries  as $country){
        $selected = '';
        if(is_array($option) && in_array($country,$option)){
            $selected='checked';
        }
        
        printf('<input type="checkbox"name="pqrc_checkbox[]"" value="%s" %s/>%s<br>',$country,$selected,$country);

    }

}

function pqrc_display_toggle(){
    $option = get_option('pqrc_toggle');
    echo '<div id="toggle1"></div>';
    echo "<input type='hidden' name='pqrc_toggle' id='pqrc_toggle' value='".$option."'/>";
}
add_action("admin_init","pqrc_settings_init");

function pqrc_assets($screen){
    if('options-general.php' == $screen){

        wp_enqueue_script('pqrc-main-js',plugin_dir_url(__FILE__)."/assets/js/pqrc-main.js",array('jquery'),time(),true);
        wp_enqueue_script('pqrc-minitoggle-js',plugin_dir_url(__FILE__)."/assets/js/minitoggle.js",array('jquery'),'1.0.0',true);
        wp_enqueue_style('pqrc-minitoggle-css',plugin_dir_url(__FILE__)."/assets/css/minitoggle.css");
        
    }
}
add_action('admin_enqueue_scripts','pqrc_assets');


/** 
 * Short code
 * 
*/

function philosopy_pqrc_button($attibutes){
    wp_enqueue_script('pqrc-minitoggle-js',plugin_dir_url(__FILE__)."/assets/js/bootstrap.min.js",array('jquery'),'1.0.0',true);
    wp_enqueue_script('pqrc-minitoggle-js',plugin_dir_url(__FILE__)."/assets/js/bootstrap.bundle.min.js",array('jquery'),'1.0.0',true);
    wp_enqueue_style('pqrc-minitoggle-css',plugin_dir_url(__FILE__)."/assets/css/bootstrap.min.css");
    
    $default = array(
        'title' => 'Button',
        'type' => 'primary',
        'url' => 'https://google.com'
    );
    $button_attr = shortcode_atts($default,$attibutes);
    return sprintf('<a class="btn btn-%s full-width" target="_blank" href="%s">%s</a>',$button_attr['type'],$button_attr['url'],ucwords($button_attr['title']));
}
add_shortcode('button','philosopy_pqrc_button');

function philosopy_pqrc_button2($attibutes,$content=''){
    wp_enqueue_script('pqrc-minitoggle-js',plugin_dir_url(__FILE__)."/assets/js/bootstrap.min.js",array('jquery'),'1.0.0',true);
    wp_enqueue_script('pqrc-minitoggle-js',plugin_dir_url(__FILE__)."/assets/js/bootstrap.bundle.min.js",array('jquery'),'1.0.0',true);
    wp_enqueue_style('pqrc-minitoggle-css',plugin_dir_url(__FILE__)."/assets/css/bootstrap.min.css");
    $default = array(

        'type' => 'primary',
        'url' => 'https://google.com'
    );
    $button_attr = shortcode_atts($default,$attibutes);
    return sprintf('<a class="btn btn-%s full-width" target="_blank" href="%s">%s</a>',$button_attr['type'],$button_attr['url'],do_shortcode($content));
}
add_shortcode('button2','philosopy_pqrc_button2');

function pqrc_shortcode_uc($attibutes,$content=''){
    return strtoupper($content);
}
add_shortcode('uc','pqrc_shortcode_uc');

function pqrc_shortcode_google_map($attibutes){
    $default = array(
        'place' => 'Dhaka', 
        'width' => '800',
        'height' => '800',
        'zoom' => '14',
        'margin-top' => '80px'
    );
    $params = shortcode_atts($default,$attibutes);
    $map = <<<EOD
    <div>
        <div>
            <iframe width="{$params['width']}" height="{$params['height']}"
                    src="https://maps.google.com/maps?q={$params['place']}&t=&z={$params['zoom']}&ie=UTF8&iwloc=&output=embed"
                    frameborder="0" scrolling="no" marginheight="0" marginwidth="0" style="padding:80px">
            </iframe>
        </div>
    </div>
    EOD;
    
    return $map;
}
add_shortcode('gmap','pqrc_shortcode_google_map');
