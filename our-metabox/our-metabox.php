<?php 
/*
Plugin name:Our metabox
Plugin URL:www.coderex.co
Description: Scan QR code and redirect to URL
Version:1.0.0
Author: CoderRex
Author URI: www.coderex.co
License:
Text Domain:omb-our-metabox
Domain Path:
*/

class OurMetabox{

    function __construct(){
        add_action("plugin_loaded",array($this,'omb_load_textdomain'));
        add_action("admin_menu",array($this,'omb_add_metabox'));
        add_action("save_post",array($this,'omb_save_location'));
    }

    function omb_load_textdomain(){
        load_plugin_textdomain('omb-our-metabox',false,dirname(__FILE__)."/languages");
    }

    function omb_save_location($post_id){
        $nonce = isset($_POST['omb_location_field'])?$_POST['omb_location_field']:'';
        $location = isset($_POST['omb_location'])?$_POST['omb_location']:'';
        
        $nonce2 = isset($_POST['omb_about_field'])?$_POST['omb_about_field']:'';
        $about = isset($_POST['omb_about'])?$_POST['omb_about']:'';
        
       
        if($location == '' ){
            return $post_id;
        }
        if($nonce == ''){
            return $post_id;
        }
        if(!wp_verify_nonce( $nonce, 'omb_location')){
            return $post_id;
        }
        if(!current_user_can( 'edit_post',$post_id)){
            return $post_id;
        }
        if(wp_is_post_autosave( $post_id )){
            return $post_id;
        }
        if(wp_is_post_revision( $post_id )){
            return $post_id;
        }

        if($about == '' ){
            return $post_id;
        }
        if($nonce2 == ''){
            return $post_id;
        }
        
      


        update_post_meta($post_id,'omb_location',$location);
        update_post_meta($post_id,'omb_about',$about);
    }
    function omb_add_metabox(){
            add_meta_box('omb_post_location',__( 'Location info', 'our-metabox' ),array($this,'omb_display_post'),'page','normal');  
            add_meta_box('omb_about_title',__( 'About title', 'our-metabox' ),array($this,'omb_display_about_title'),'page','normal');  
    }

    function omb_display_post($post){
        $location = get_post_meta($post->ID,'omb_location',true);
        $label = __('Location','our-metabox');
        wp_nonce_field('omb_location','omb_location_field');
        $metabox_html = <<<EOD
        <p>
            <label for="omb_location">{$label}</label>
            <input type="text" name="omb_location" col=""row="5"class=""form-control id="omb_location" value="{$location}"/>
        </p>

        EOD;
        echo $metabox_html;
    }

    function omb_display_about_title($post){
        $about_title = get_post_meta($post->ID,'omb_about',true);
        $label = __('About title','our-metabox');
        wp_nonce_field('omb_about','omb_about_field');
        $metabox_html = <<<EOD
        <p>
            <label for="omb_about">{$label}</label>
            <input type="text" name="omb_about" id="omb_about" value="{$about_title}"/>
        </p>

        EOD;
        echo $metabox_html;
    }
    
    
}

New OurMetabox();
