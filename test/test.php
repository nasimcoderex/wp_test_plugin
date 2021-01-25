<?php 
/*
Plugin name: Test Project
Plugin URL:www.coderex.co
Description: Word Count from my wordpress post
Version:1.0.0
Author: CoderRex
Author URI: www.coderex.co
License:
Text Domain:test-project
Domain Path:
*/



/**
 * Project_CPT
 *
 * A class for create custom post type.
 *
 * @date	25/0/21
 * @since	1.0.0
 */
class Project_CPT{
    static $instance;
    private $wpdb;

        
    /**
     * __construct
     *
     * Constructor function of Project_CPT class
     */
    function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        add_action("plugin_loaded",array($this,'testproject_load_textdomain'));
        add_action('init',array($this,'my_first_post_type'));
        add_action('init',array($this,'my_first_taxonomy'));
        add_action('admin_menu',array($this,'project_add_metabox'));
        add_action("save_post",array($this,'project_save_metadata'));
        add_action( 'pre_get_posts', array($this,'project_per_get_posts' ));
        add_action( 'admin_init', array($this,'add_gallery_to_project' ));
        add_action( 'admin_head-post.php', array($this,'display_gallery_metabox_to_project' ));
        add_action( 'admin_head-post-new.php', array($this,'display_gallery_metabox_to_project' ));
        add_action( 'save_post', array($this,'save_gallery_image_to_project'), 10, 2 );
        add_action('admin_menu', array($this,'all_request_to_buy'));
    }

    /**
     * getInstance
     *
     * getInstance function for create single instance of Project_CPT class. No duplicate instance will be allowed
     */
    public static function getInstance(){
       
        if(!self::$instance){
            self::$instance = new Project_CPT();
        }else{
            echo "Using existing object <br>";
        }
    }

    /**
     * testproject_load_textdomain
     *
     * Load text domain of Test Project plugin 
     */
    function testproject_load_textdomain(){
        load_plugin_textdomain('test-project',false,dirname(__FILE__)."/languages");
    }

    /**
     * my_first_post_type
     *
     * Create custom post type named Projects 
     */
    function my_first_post_type(){

        $args = array(
            'labels' => array(
                'name' => 'Projects',
                'singular_name' => 'Project',
            ),
            'hierarchical' => true,
            'public' => true,
           
            'has_archive' =>true,
            'supports' => array('title','category','thumbnail','comments',),
            'menu_icon' => 'dashicons-align-none'
           
        );
        register_post_type( 'projects',$args );
    }

    /**
     * my_first_taxonomy
     *
     * Create category taxonomy of post type Projects 
     */
    function my_first_taxonomy(){
        $args = array(
            'labels' => array(
                'name' => 'Categories',
                'singular_name' => 'Category',
            ),
            'public' => true,
            'hierarchical' => true,
        
           
        );
        register_taxonomy( 'Categories', array('projects'),$args);
    }

    /**
     * project_add_metabox
     *
     * Add three metaboxes for Projects CPT named project_start_date,project_end_date,project_description
     */
    function project_add_metabox(){
        add_meta_box('project_start_date',__( 'Date', 'test' ),array($this,'project_metabox_display_start_date'),'projects','normal');     
        add_meta_box('project_end_date',__( 'Date', 'test' ),array($this,'project_metabox_display_end_date'),'projects','normal');     
        add_meta_box('project_description',__( 'Description', 'test' ),array($this,'project_metabox_display_description'),'projects','normal');     
    }

    /**
     * project_metabox_display_start_date
     *
     * Display start date metabox in CPT Projects.
     * @param	array $post The array of each post
     */
    function project_metabox_display_start_date($post){
        $date = get_post_meta($post->ID,'project_start_date',true);
        $label = __('Start date','test');
        wp_nonce_field('project_start_date','project_start_date_feild');
        $metabox_html = <<<EOD
        <p>
            <label for="project_start_date">{$label}</label>
            <input type="date" name="project_start_date" class=""form-control id="project_start_date" value="{$date}"/>
        </p>
    
        EOD;
        echo $metabox_html;
    }
    
    /**
     * project_metabox_display_end_date
     *
     * Display end date metabox in CPT Projects.
     * @param	array $post The array of each post
     */
    function project_metabox_display_end_date($post){
        $date = get_post_meta($post->ID,'project_end_date',true);
        $label = __('End date','test');
        wp_nonce_field('project_end_date','project_end_date_feild');
        $metabox_html = <<<EOD
        <p>
            <label for="project_end_date">{$label}</label>
            <input type="date" name="project_end_date" class=""form-control id="project_end_date" value="{$date}"/>
        </p>
    
        EOD;
        echo $metabox_html;
    }
    
    /**
     * project_metabox_display_description
     *
     * Display project description metabox in CPT Projects.
     * @param	array $post The array of each post
     */
    function project_metabox_display_description($post){
        $description = get_post_meta($post->ID,'project_description',true);
        $label = __('Project description','test');
        wp_nonce_field('project_description','project_description_feild');
        $metabox_html = <<<EOD
        <p>
            <label for="project_description">{$label}</label>
            <textarea type="date" name="project_description" row="10" col="30" style="height:200px;width:100%" class=""form-control id="project_description" >$description</textarea>
        </p>
    
        EOD;
        echo $metabox_html;
    }
    
    /**
     * project_save_metadata
     *
     * Save the value of all metaboxes which added in CPT Projects.
     * @param	int $post_id ID of post
     */
    function project_save_metadata($post_id){
        $nonce = isset($_POST['project_start_date_feild'])?$_POST['project_start_date_feild']:'';
        $start_date = isset($_POST['project_start_date'])?$_POST['project_start_date']:'';
        
        $nonce2 = isset($_POST['project_end_date_feild'])?$_POST['project_end_date_feild']:'';
        $end_date = isset($_POST['project_end_date'])?$_POST['project_end_date']:'';
        
        $nonce3 = isset($_POST['project_description_feild'])?$_POST['project_description_feild']:'';
        $description = isset($_POST['project_description'])?$_POST['project_description']:'';
        
        update_post_meta($post_id,'project_start_date',$start_date);
        update_post_meta($post_id,'project_end_date',$end_date);
        update_post_meta($post_id,'project_description',$description);
    }

    /**
     * projects_per_get_posts
     *
     * Display 10 row per page 
     * @param	int $q target the main query of a page request
     */
    function projects_per_get_posts($q){
        if( !is_admin() && $q->is_main_query() && $q->is_post_type_archive( 'projects' ) ) {
    
            $q->set( 'posts_per_page', 10 );
    
        }
    }

    /**
     * add_gallery_to_project
     *
     * Add a Image upload metaboxes as a gallery named Project Image Gallery in CPT Projects
     */
    function add_gallery_to_project()
    {
        add_meta_box(
        'post_gallery',
        'Project Image Gallery',
        array($this,'print_gallery_metabox_to_project'),
        'projects',
        'normal'
        
        );
    }
    

    /**
     * print_gallery_metabox_to_project
     *
     * Display a Image upload metaboxes as a gallery named Project Image Gallery in CPT Projects
     */
    function print_gallery_metabox_to_project()
    {
        global $post;
        $gallery_data = get_post_meta( $post->ID, 'gallery_data', true );
    
        wp_nonce_field( plugin_basename( __FILE__ ), 'noncename_so_14445904' );
        ?>
    
            <div id="dynamic_form">
    
                <div id="field_wrap">
                <?php 
                if ( isset( $gallery_data['image_url'] ) ) 
                {
                    for( $i = 0; $i < count( $gallery_data['image_url'] ); $i++ ) 
                    {
                    ?>
            
                    <div class="field_row">
            
                    <div class="field_left">
                        <div class="form_field">
                        <label>Image URL</label>
                        <input type="text"
                                class="meta_image_url"
                                name="gallery[image_url][]"
                                value="<?php esc_html_e( $gallery_data['image_url'][$i] ); ?>"
                        />
                        </div>
                    </div>
            
                    <div class="field_right image_wrap">
                        <img src="<?php esc_html_e( $gallery_data['image_url'][$i] ); ?>" height="48" width="48" />
                    </div>
            
                    <div class="field_right">
                        <input class="button" type="button" value="Choose File" onclick="add_image(this)" /><br />
                        <input class="button" type="button" value="Remove" onclick="remove_field(this)" />
                    </div>
            
                    <div class="clear" /></div> 
                    </div>
                    <?php
                    } 
                } 
                ?>
            </div>
            
            <div style="display:none" id="master-row">
                <div class="field_row">
                    <div class="field_left">
                        <div class="form_field">
                            <label>Image URL</label>
                            <input class="meta_image_url" value="" type="text" name="gallery[image_url][]" />
                        </div>
                    </div>
                    <div class="field_right image_wrap">
                    </div> 
                    <div class="field_right"> 
                        <input type="button" class="button" value="Choose File" onclick="add_image(this)" />
                        <br />
                        <input class="button" type="button" value="Remove" onclick="remove_field(this)" /> 
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            
            <div id="add_field_row">
                <input class="button" type="button" value="Add Field" onclick="add_field_row();" />
            </div>
            
            </div>
    
        <?php
    }
    

    /**
     * display_gallery_metabox_to_project
     *
     * Add or remove input field as you need for uploading image for gallery section of CPT Projects
     */
    function display_gallery_metabox_to_project()
    {
        
        global $post;
        if( 'projects' != $post->post_type )
            return;
        ?>  
        <style type="text/css">
        .field_left {
            float:left;
        }
    
        .field_right {
            float:left;
            margin-left:10px;
        }
    
        .clear {
            clear:both;
        }
    
        #dynamic_form {
            width:580px;
        }
    
        #dynamic_form input[type=text] {
            width:300px;
        }
    
        #dynamic_form .field_row {
            border:1px solid #999;
            margin-bottom:10px;
            padding:10px;
        }
    
        #dynamic_form label {
            padding:0 6px;
        }
        </style>
    
        <script type="text/javascript">
            function add_image(obj) {
                var parent=jQuery(obj).parent().parent('div.field_row');
                var inputField = jQuery(parent).find("input.meta_image_url");
    
                tb_show('', 'media-upload.php?TB_iframe=true');
    
                window.send_to_editor = function(html) {
                    var url = jQuery(html).find('img').attr('src');
                    inputField.val(url);
                    jQuery(parent)
                    .find("div.image_wrap")
                    .html('<img src="'+url+'" height="48" width="48" />');
    
    
                    tb_remove();
                };
    
                return false;  
            }
    
            function remove_field(obj) {
                var parent=jQuery(obj).parent().parent();
                parent.remove();
            }
    
            function add_field_row() {
                var row = jQuery('#master-row').html();
                jQuery(row).appendTo('#field_wrap');
            }
        </script>
        <?php
    }
    
  
    /**
     * save_gallery_image_to_project
     *
     * Save gallery images
     * @param int $post_id Id of post
     */
    function save_gallery_image_to_project( $post_id ) 
    {
        
        if ( $_POST['gallery'] ) 
        {
            
            $gallery_data = array();
            for ($i = 0; $i < count( $_POST['gallery']['image_url'] ); $i++ ) 
            {
                if ( '' != $_POST['gallery']['image_url'][ $i ] ) 
                {
                    $gallery_data['image_url'][]  = $_POST['gallery']['image_url'][ $i ];
                }
            }
    
            if ( $gallery_data ) 
                update_post_meta( $post_id, 'gallery_data', $gallery_data );
            else 
                delete_post_meta( $post_id, 'gallery_data' );
        } 
        
        else 
        {
            delete_post_meta( $post_id, 'gallery_data' );
        }
    }


    


    /**
     * all_request_to_buy
     * Create a submenu page  named 'All request' for showing all data whose are requested to buy project
     */
    function all_request_to_buy() {
        add_submenu_page(
            'edit.php?post_type=projects',
            'all request',
            'all request',
            'manage_options',
            'all_request_to_buy',array(
                $this,
            'all_request_to_buy_callback')
        );
    }
    /**
     * all_request_to_buy_callback
     * Retrive all date from 'wp_request' table and show in All request submenu page
     */
    function all_request_to_buy_callback() { 
       
        $result = $this->wpdb->get_results( "SELECT * FROM `wp_request`");

        ?>
        <div class="wrap">
            <h3>All requests :<h3>
            <div class="table-responsive">
                <table class="table request_table_admin" style="border: 1px solid black;padding:10px;">
                    <thead>
                        <tr >
                            <th class="text-center" style="border: 1px solid black;padding:10px;">Name</th>
                            <th class="text-center" style="border: 1px solid black;padding:10px;">Email</th>
                            <th class="text-center" style="border: 1px solid black;padding:10px;">Budget</th>
                            <th class="text-center" style="border: 1px solid black;padding:10px;">Message</th>
                            <th class="text-center" style="border: 1px solid black;padding:10px;">Post Id</th>
                        </tr>
                    </thead>
                    <tbody>
                
                    <?php  $i = 0; foreach($result as $r) {?>

                        <tr>
                            <td class="text-center" style="border: 1px solid black;padding:10px;"><?php echo $result[$i]->name;?></td>
                            <td class="text-center" style="border: 1px solid black;padding:10px;"><?php echo $result[$i]->email;?></td>
                            <td class="text-center" style="border: 1px solid black;padding:10px;"><?php echo $result[$i]->budget;?></td>
                            <td class="text-center" style="border: 1px solid black;padding:10px;"><?php echo $result[$i]->message;?></td>
                            <td class="text-center" style="border: 1px solid black;padding:10px;"><?php echo $result[$i]->post_id;?></td>
                        </tr>
                    <?php $i++; }?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }


    




}

Project_CPT::getInstance();


    