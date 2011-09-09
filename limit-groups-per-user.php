<?php
/*
* Plugin name: Limit Groups per User
* description: Limit the number of groups a user can create.
*Author:Brajesh Singh
*Author URI: http://buddydev.com
*Plugin URI: http://buddydev.com/buddypress/buddypress-sitewide-activity-widget-for-buddypress-1-2-and-above
* Version: 1.1.1
* Last Update: September 9, 2011
* License: GPL
*/



function bpdev_restrict_group_create($user_id=null){
	global $bp;

//no restriction to site admin
if (!bp_is_group_create() ||is_super_admin())
		return false;
//if we are here,It is group creation step

if(!$user_id)
	$user_id=$bp->loggedin_user->id;
//even in cae of zero, it will return true
if(!empty($_COOKIE['bp_new_group_id']))
    return;//this is intermediate step of group creation
if(!bpdev_can_create_new_groups($user_id)){

		bp_core_add_message(apply_filters("restrict_group_message",__("Either You have exceeded the no. of groups you can create or you don't have permission to create group")),"error");
		remove_action( 'wp', 'groups_action_create_group', 3 );
		bp_core_redirect(bp_get_root_domain().'/'.  bp_get_groups_slug());
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
 * Show the option on BuddyPress settings page to Limit the group
 */
function bpdev_limit_groups_admin_screen(){
?>
<table class="form-table">
<tbody>
<tr>
	<th scope="row"><?php _e( 'Limit Groups Per User' ) ?></th>
		<td>
                    <p><?php _e( 'How many Groups a user can create?') ?></p>
                    <label><input type="text" name="bp-admin[limit-groups-creation-per-user]" id="limit-groups-creation-per-user" value="<?php echo bp_get_option( 'limit-groups-creation-per-user',0 );?>" /></label><br>
                </td>
	</tr>
</tbody>
</table>				
<?php
}

function bpdev_can_create_new_groups($user_id=false){
    global $bp;
    
    if(is_super_admin ())
        return true;
    
    if(!$user_id)
        $user_id=$bp->loggedin_user->id;
    
    $allowed_count=bp_get_option( 'limit-groups-creation-per-user',0 );//default 0, unlimited;
    $user_has_admin_rights=BP_Groups_Member::get_is_admin_of($user_id);// $wpdb->get_var( $wpdb->prepare( "SELECT count(group_id) as count FROM {$bp->groups->table_name_members} WHERE user_id = %d AND is_admin = 1 AND is_banned = 0", $user_id) );
    $count=$user_has_admin_rights["total"];    
    if($count>=$allowed_count)
        return false;
    return true;
}

add_action('bp_core_admin_screen','bpdev_limit_groups_admin_screen');

//bp1.5 has a hook to disable the create button, so let us remove that
add_filter('bp_user_can_create_groups','bpdev_can_create_new_groups');
?>