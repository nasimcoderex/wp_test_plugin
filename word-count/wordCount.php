<?php 
/*
Plugin name: Word Count
Plugin URL:www.coderex.co
Description: Word Count from my wordpress post
Version:1.0.0
Author: CoderRex
Author URI: www.coderex.co
License:
Text Domain:word-count
Domain Path:
*/
// function wordcount_activation_hook(){  
// }
// function wordcount_deactivation_hook(){
// }
// register_activation_hook(__FILE__,'wordcount_activation_hook');
// register_activation_hook(__FILE__,'wordcount_deactivation_hook');
function wordcount_load_textdomain(){
    load_plugin_textdomain('word-count',false,dirname(__FILE__)."/languages");
}
add_action("plugin_loaded",'wordcount_load_textdomain');

function wordcount_count_words($content){
    $stripped_content = strip_tags($content);
    $wordn = str_word_count($stripped_content);
    $label = __('Total Number of Words','word-count');
    $label = apply_filters("wordcount_heading",$label);
    $tag = apply_filters('wordcount_tag','h2');
    $content .= sprintf('<%s>%s: %s</%s>',$tag,$label,$wordn,$tag);
    return $content; 
}
add_filter('the_content','wordcount_count_words');


function wordcount_reading_time_count($content){
    $stripped_content = strip_tags($content);
    $wordn = str_word_count($stripped_content);
    $read_minute = floor($wordn/200);
    $read_seconds = floor($wordn%200 / (200/60));
    $is_visible = apply_filters('wordcount_display_readingtime',1);
    if($is_visible){
        $label = __('Total Reading Time','word-count');
        $label = apply_filters("wordcount_reading_heading",$label);
        $tag = apply_filters('wordcount_reading_tag','h2');
        $content .= sprintf('<%s>%s: %s minutes %s seconds</%s>',$tag,$label,$read_minute,$read_seconds,$tag);
    }
    return $content; 
}
add_filter('the_content','wordcount_reading_time_count');