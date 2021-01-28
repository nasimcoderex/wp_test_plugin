<?php 
/*
Plugin name: Custom Checkout
Plugin URL:www.coderex.co
Description: Add custom field in woocommerce checkout section
Version:1.0.0
Author: CoderRex
Author URI: www.coderex.co
License:
Text Domain:custom-checkout
Domain Path:
*/

/**
 * Custom_checkout
 *
 * A class for Add custom field in woocommerce checkout section.
 *
 * @date	27/01/21
 * @since	1.0.0
 */

class Custom_checkout{
    static $instance;
    private $wpdb;
    public $cf;
    
    /**
     * __construct
     *
     * Constructor function of Project_CPT class
     */
    function __construct(){

        global $wpdb;
        $this->wpdb = $wpdb;
        add_action("plugin_loaded",array($this,'testproject_load_textdomain'));
        add_action( "admin_enqueue_scripts",array($this,"custom_checkout_assets"));
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action( 'woocommerce_after_order_notes', array($this, 'add_custom_checkout_field' ));
        add_action( 'woocommerce_checkout_update_order_meta', array($this, 'custom_checkout_field_update_order_meta' ));
        add_action( 'woocommerce_admin_order_data_after_billing_address', array($this, 'test_custom_checkout_field_display_admin_order_meta'));
        add_action('woocommerce_checkout_process',array($this,  'my_custom_checkout_field_process'));

 

        
       
    }

    /**
     * custom_checkout_assets
     *
     * Support bootstrap and jquery in admin panel
     */
    function custom_checkout_assets(){
        wp_enqueue_style('custom-feild-bootstrap4','https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css');
        wp_enqueue_script( 'custom-feild-boot1','https://code.jquery.com/jquery-3.3.1.slim.min.js', array( 'jquery' ),'',true );
        wp_enqueue_script( 'custom-feild-boot2','https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', array( 'jquery' ),time(),true );
        wp_enqueue_script( 'custom-feild-boot3','https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js', array( 'jquery' ),time(),true );
        wp_enqueue_script('custom-feild-main-js',plugin_dir_url(__FILE__)."/assets/js/main.js",array('jquery'),time(),true);
    }

    /**
     * getInstance
     *
     * getInstance function for create single instance of Project_CPT class. No duplicate instance will be allowed
     */
    public static function getInstance(){
       
        if(!self::$instance){
            self::$instance = new Custom_checkout();
        }else{
            echo "Using existing object <br>";
        }
    }
    /**
     * admin_menu
     * add a sub page under woocommerce
     * 
    */
    public function admin_menu() {
		add_submenu_page(
            'woocommerce',
            'Custom Checkout',
            'Custom Checkout',
            'manage_options',
            'custom_checkout',array(
                $this,
            'display_custom_checkout_page')
        );
    }

    /**
     * display_custom_checkout_page
     * design of Custom Checkout subpage
     * 
    */
    function display_custom_checkout_page(){
        ?>
        <div class="wrap">
            <h3>Add Field :<h3>
            <button class="btn btn-success custom_checkout_add_field" id="custom_checkout_add_field" name="custom_checkout_add_field">Add new</button>
        </div>

        <div class="wrap"><?php
            $get_data = get_option( 'test_wc_custom_fields_billing');
          
            
            ?>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-center"> Label </th>
                            <th class="text-center"> Name </th>
                            <th class="text-center"> Type </th>
                            
                            <th class="text-center"> Placeholder </th>
                            <th class="text-center"> Action </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($get_data as $a => $value) {
                               
                                ?>
                                <tr class="tcf_tr">
                                    <td class="text-center"><?php echo $value['label'] ?></td>
                                    <td class="text-center"><?php echo $value['name'] ?></td>
                                    <td class="text-center"><?php echo $value['type'] ?></td>
                                    
                                    <td class="text-center"><?php echo $value['placeholder'] ?></td>
                                    <td class="text-center "><a href="csutom-checkout.php?id=<?=$a?>"> <?php echo 'delete' ?></a></td>
                                </tr>
                                
                                <?php
                            
                        }?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="CFaddField" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">ADD</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Type</label>
                                <select name="cf_type" id="cf_type" name="cf_type" class="form-control">
                                    <option > Select</option>
                                    <option value="text">Text</option>
                                    <option value="password">Password</option>
                                    <option value="email">Email</option>
                                    <option value="phone">Phone</option>
                                    <option value="radio">Radio</option>
                                    <option value="textarea">Textarea</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Name</label> 
                                <input type="text" class="form-control" name="cf_name" id="cf_name" value="tcf_">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Label</label>
                                <input type="text" class="form-control" name="cf_label"id="cf_label">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Placeholder</label>
                                <input type="text" class="form-control" name="cf_placeholder"id="cf_placeholder">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Id</label>
                                <input type="text" class="form-control"name="cf_id" id="cf_default_value" value="tcf_">
                            </div>
                            

                            <div class="form-group">
                                <label class="checkbox-inline">
                                    <input type="checkbox" value="1" name="cf_required">Required
                                </label>
                            </div>
                            
                            <button type="submit" id="cf_submit" name="cf_submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                   
                </div>
            </div>
        </div>
        <?php
         if(isset($_POST['cf_submit'])){
           
            $this->cf['type'] = $_POST['cf_type'];
            $this->cf['label'] = $_POST['cf_label'];
            $this->cf['name'] = $_POST['cf_name'];
            $this->cf['placeholder'] = $_POST['cf_placeholder'];
            $this->cf['id'] = $_POST['cf_id'];
            $this->cf['class'] = array('my-field-class form-row-wide');
            if(isset($_POST['cf_required'])){
                $this->cf['required'] = true;
            }else{
                $this->cf['required'] = false;
            }


            $get_data = get_option( 'test_wc_custom_fields_billing');
            if($get_data){
                if(!(in_array($_POST['cf_name'], $get_data))){
                    $get_data[$_POST['cf_name']] = $this->cf;
                    update_option( 'test_wc_custom_fields_billing', $get_data );
                }else{
                    ?>
                    <script>
                        alert("Allready add field with same name")
                    </script>
                    <?php
                }
            }else{
                $get_e_data[$_POST['cf_name']] = $this->cf;
                update_option( 'test_wc_custom_fields_billing', $get_e_data );
            }
            

            
            echo "<meta http-equiv='refresh' content='0'>";
            
         }
         
        
       
    }

    
   /**
     * add custom checkout field
    */
   
    
    function add_custom_checkout_field( $checkout ) {
        $get_custom_data = get_option( 'test_wc_custom_fields_billing');
        if($get_custom_data){
            foreach($get_custom_data as $gcd){
                echo '<div id="'.$gcd['id'].'"><h2>' . __($gcd['label']) . '</h2>';
    
                woocommerce_form_field( $gcd['name'], array(
                    'type'          => $gcd['type'],
                    'class'         => array('my-field-class form-row-wide'),
                    'label'         => __($gcd['label']),
                    'placeholder'   => __($gcd['placeholder']),
                    'required'      => $gcd['required'],
                    ), $checkout->get_value( $gcd['name'] ));
        
                echo '</div>';
            }
        }
        
      
       

    }

    /**
     * Update the order meta with field value
    */
   
    function custom_checkout_field_update_order_meta( $order_id ) {
        $get_custom_data = get_option( 'test_wc_custom_fields_billing');
        if ($get_custom_data) {
            foreach($get_custom_data as $get_custom_data){
                update_post_meta( $order_id, $get_custom_data['name'], sanitize_text_field( $_POST[$get_custom_data['name']] ) );
            }
            
        }
    }

    /**
     * Display field value on the order edit page
     */
    
    function test_custom_checkout_field_display_admin_order_meta($order){
        $get_custom_data = get_option( 'test_wc_custom_fields_billing');
        
        if ($get_custom_data) {
         
            foreach($get_custom_data as $gccd){
                echo '<p><strong>'.__($gccd['label']).':</strong> ' . get_post_meta( $order->id, $gccd['name'], true ) . '</p>';
        
            }
        }
    }

    /**
     * Process the checkout
     */
    

    function my_custom_checkout_field_process() {
        $get_custom_data = get_option( 'test_wc_custom_fields_billing');
        if ($get_custom_data) {
         
            foreach($get_custom_data as $gccd){
                if($gccd['required'] == true){
                    if ( ! $_POST[$gccd['name']] ){
                        wc_add_notice( __( 'Please enter something into this '.$gccd['label'].' field.' ), 'error' );
                    }
                }
            }
        }
    }


    
}
Custom_checkout::getInstance();
/**
    * Delete custom field
*/

if(isset($_GET['id'])){
    $get_data = get_option( 'test_wc_custom_fields_billing');
    $get_data_delete = get_option( 'test_wc_custom_fields_billing');
   
    foreach($get_data as $a => $value) {
        if($_GET['id'] == $a){
            unset($get_data_delete[$a]);
            update_option( 'test_wc_custom_fields_billing', $get_data_delete );
            ?>
            <script type="text/javascript">
                document.location.href="<?php echo admin_url().'/admin.php?page=custom_checkout';?>";
            </script>
            <?php
          
        }
    }
}
