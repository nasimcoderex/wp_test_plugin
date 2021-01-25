<?php 
/*
Plugin name: Asset ninja
Plugin URL:www.coderex.co
Description: Scan QR code and redirect to URL
Version:1.0.0
Author: CoderRex
Author URI: www.coderex.co
License:
Text Domain:assetsninja
Domain Path:
*/
class AssetsNinja{

    function __construct(){
        add_action("plugin_loaded",array($this,'load_textdomain'));
        add_action('wp_enqueue_scripts',array($this,'load_front_assets'));
    }

    function load_front_assets(){
        wp_enqueue_script('assetsninja-main',plugin_dir_url(__FILE__)."/assets/public/js/main.js",array('jquery'),'1.0.0',true);

        $data = array(
            'name' => 'demo',
            'type' => 'fun',
        );

        wp_localize_script('assetsninja-main','sitedata',$data);
    }
    function load_textdomain(){
        load_plugin_textdomain('assetsninja',false,dirname(__FILE__)."/languages");
    }
}
New AssetsNinja();

