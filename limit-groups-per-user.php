<?php
/*
* Plugin name: Limit Groups per User
* description: Limit the number of groups a user can create.
* Author:Brajesh Singh
* Author URI: http://buddydev.com
* Plugin URI: http://buddydev.com/buddypress/limit-groups-per-user-plugin-for-buddypress/
* Version: 1.1.3
* Last Update: October 3, 2012
* License: GPL
*/


//bp1.5 has a hook to disable the create button, so let us remove that
add_filter('bp_user_can_create_groups','bpdev_can_current_user_create_new_groups');
function bpdev_can_current_user_create_new_groups($can_create){
    if(bpdev_can_create_new_groups(bp_loggedin_user_id()))
        return true;
    return false;
    
}

function bpdev_restrict_group_create($user_id=null){
	global $bp;

//no restriction to site admin
if (!bp_is_group_create() ||is_super_admin())
		return false;
//if we are here,It is group creation step

if(!$user_id)
	$user_id=  bp_loggedin_user_id();
//even in cae of zero, it will return true
if(!empty($_COOKIE['bp_new_group_id']))
    return;//this is intermediate step of group creation
if(!bpdev_can_create_new_groups($user_id)){

		bp_core_add_message(apply_filters("restrict_group_message",__("Either You have exceeded the no. of groups you can create or you don't have permission to create group")),"error");
		remove_action( 'bp_actions', 'groups_action_create_group', 3 );
		bp_core_redirect(bp_get_groups_directory_permalink());
}


}
/**
 * Check if we should allow creating group or not
 * @global type $bp
 * @return type 
 */
function bpdev_check_group_create(){
	global $bp;
	if(!function_exists('bp_is_active')||!bp_is_active('groups'))
		return; //do not cause headache
	
	bpdev_restrict_group_create();
}

add_action('wp','bpdev_check_group_create',2);

/**
 * Can current use create new group?
 * 
 * @global type $bp
 * @param type $user_id
 * @return type 
 */


function bpdev_can_create_new_groups($user_id=false){
    global $bp;
    
    if(is_super_admin ())
        return true;
    
    if(!$user_id)
        $user_id=  bp_loggedin_user_id();
    
    $allowed_count=bp_get_option( 'limit-groups-creation-per-user',0 );//default 0, unlimited;
    
    $user_has_admin_rights=BP_Groups_Member::get_is_admin_of($user_id);// $wpdb->get_var( $wpdb->prepare( "SELECT count(group_id) as count FROM {$bp->groups->table_name_members} WHERE user_id = %d AND is_admin = 1 AND is_banned = 0", $user_id) );
    $count=$user_has_admin_rights["total"]; 
    
    if(intval($count)>=$allowed_count)
        return false;
    return true;
}

/**
 * Admin Helper
 */

class BPLimitGroupsPerUserAdminHelper{
    private static $instance;
    
    function __construct() {
        add_action('bp_admin_init',array($this,'register_settings'),20);
    }
     function get_instance(){
     if(!isset (self::$instance))
             self::$instance=new self();
     return self::$instance;
    }
    function register_settings(){
        // Add the ajax Registration settings section
            add_settings_section( 'bp_limit_groups_per_user',        __( 'Limit Groups Per User Settings',  'bp-limit-groups-per-user' ), array($this,'reg_section'),   'buddypress'              );
            // Allow loading form via jax or nt?
            add_settings_field( 'limit-groups-creation-per-user', __( 'How many Groups a user can Create?',   'bp-limit-groups-per-user' ), array($this,'settings_field'),   'buddypress', 'bp_limit_groups_per_user' );
            register_setting  ( 'buddypress',         'limit-groups-creation-per-user',   'intval' );
    }
    
    function reg_section(){
        
    }
    
    function settings_field(){
            $val=bp_get_option('limit-groups-creation-per-user',0);?>

         
                   
                    <label>
                        <input type="text" name="limit-groups-creation-per-user" id="limit-groups-creation-per-user" value="<?php echo $val;?>" /></label><br>
                    
   <?php }
    
}
BPLimitGroupsPerUserAdminHelper::get_instance();
?>