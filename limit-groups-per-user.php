<?php
/*
* Plugin name: Limit Groups per User
* description: Limit the number of groups a user can create.
* Author:Brajesh Singh
* Author URI: http://buddydev.com
* Plugin URI: http://buddydev.com/buddypress/limit-groups-per-user-plugin-for-buddypress/
* Version: 1.2
* Last Update: January 20, 2013
* License: GPL
*/

class BP_Limit_Groups_Per_User_Helper{
    private static $instance;
    
    private function __construct() {
        
        add_filter( 'bp_user_can_create_groups', array( $this, 'show_hide_create_btn' ) );//we only use to hide the create btn on group directory        
        add_action( 'wp', array( $this, 'check_group_create' ), 2 );  
        
    }

    
    /**
     * 
     * @return BP_Limit_Groups_Per_User_Helper
     */
    public static function get_instance(){
        
        if( !isset( self::$instance ) )
            self::$instance = new self();
        
        return self::$instance;
    }
    
    /**
     * Show hide the create group btn on groups directory
     * @param type $can_create
     * @return boolean
     */

    public function show_hide_create_btn( $can_create ) {

        //we only filter it on directory page for create button
        //on other pages(group create page), It is called too early and we don't have enough data to filter on this

        if( bp_is_groups_component() && !bp_current_action() ) { //we are on directory
          
            $user_id = get_current_user_id();  

            $groups = self::get_user_groups( $user_id );

            if( intval($groups['total'])>=  self::get_allowed_group_count( $user_id ) )
                return false;
            else
                return true;
            }   

        return $can_create;

    }   
    /**
     * Check if we should allow creating group or not
     * @global type $bp
     * @return type 
     */
    public function check_group_create() {
        global $bp;
        if( !function_exists( 'bp_is_active' ) || !bp_is_active( 'groups' ) )
            return; //do not cause headache

            $this->restrict_creation();
    }

    public function restrict_creation( $user_id = null ) {
       
        //no restriction to site admin
        if ( !bp_is_group_create() || is_super_admin() )
            return false;
        
        //if we are here,It is group creation step

        if( !$user_id )
            $user_id = get_current_user_id();

        if( !self::user_can_create_new_group( $user_id ) ) {
            bp_core_add_message( apply_filters( 'restrict_group_message', __( "Either You have exceeded the no. of groups you can create or you don't have permission to create group" ) ), 'error' );
            
            remove_action( 'bp_actions', 'groups_action_create_group' ); //priority changed from 3 to 10 in bp 1.9
            
            bp_core_redirect( bp_get_groups_directory_permalink() );
        }
    }
    

    /**
     * Can current use create new group?
     * 
     * @global type $bp
     * @param type $user_id
     * @return type 
     */


    function user_can_create_new_group( $user_id  ) {
       
        if( is_super_admin () )
            return true;
        $user_groups = self::get_user_groups( $user_id );
        //are we on group create page and is it not the first step?
        if( bp_is_group_create() && !bp_is_group_creation_step('group-details') ) {

            $group_id = $_COOKIE['bp_new_group_id'] ;
           
            
            $groups = $user_groups['groups'];
            
            $group_ids = wp_list_pluck( $groups, 'id');
          
            //print_r($bp->groups);
            if( in_array( $group_id, (array)$group_ids ) )
                    return true;

        }
        
         //return true;
        //if we are here, it is the first step of group creation
      
        if( intval( $user_groups['total'] ) >= self::get_allowed_group_count($user_id) )
            return false;
        return true;
    }    

  
    
    /**
     * Get the total allowed no. of groups for user
     * @param type $user_id
     * @return type
     */
    public static function get_allowed_group_count( $user_id ) {
       
        return apply_filters( 'limit_groups_get_allowed_group_count', bp_get_option( 'limit-groups-creation-per-user', 0 ), $user_id ) ;//default 0, unlimited;
        
    }
    
    /**
     * Get all the groups of which the user is admin
     * 
     * @param type $user_id
     * @return mixed array('groups'=> array of groups, 'total'=>count of groups)
     */
    public static function get_user_groups( $user_id= false ) {
        if( !$user_id )
            $user_id = get_current_user_id();

        return  BP_Groups_Member::get_is_admin_of( $user_id );// $wpdb->get_var( $wpdb->prepare( "SELECT count(group_id) as count FROM {$bp->groups->table_name_members} WHERE user_id = %d AND is_admin = 1 AND is_banned = 0", $user_id) );

    }

}
BP_Limit_Groups_Per_User_Helper::get_instance();


/**
 * Admin Helper
 */

class BPLimitGroupsPerUserAdminHelper{
    private static $instance;
    
    private function __construct() {
        add_action( 'bp_admin_init', array( $this, 'register_settings' ), 20 );
    }
    
     public static function get_instance(){
        if( !isset( self::$instance ) )
             self::$instance = new self();
     
        return self::$instance;
    }
    
    public function register_settings(){
        // Add the ajax Registration settings section
            add_settings_section( 'bp_limit_groups_per_user', __( 'Limit Groups Per User Settings',  'bp-limit-groups-per-user' ), array($this,'reg_section'),   'buddypress'              );
            // Allow loading form via jax or nt?
            add_settings_field( 'limit-groups-creation-per-user', __( 'How many Groups a user can Create?',   'bp-limit-groups-per-user' ), array($this,'settings_field'),   'buddypress', 'bp_limit_groups_per_user' );
            register_setting  ( 'buddypress', 'limit-groups-creation-per-user',   'intval' );
    }
    
    public function reg_section(){
        
    }
    
    public function settings_field(){
        $val = bp_get_option( 'limit-groups-creation-per-user', 0 );
        ?>
        <label>
            <input type="text" name="limit-groups-creation-per-user" id="limit-groups-creation-per-user" value="<?php echo $val;?>" />
        </label><br>
                    
   <?php }
    
}
BPLimitGroupsPerUserAdminHelper::get_instance();
