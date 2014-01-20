=== Limit Groups Per User ===
Contributors:sbrajesh
Donate link: http://buddydev.com/donate/
Tags: buddypress, buddypress limit groups, groups
Requires at least: WordPress 3.8+BuddyPress 1.9.1
Tested up to:BuddyPress 1.9.1
Stable tag: 1.2

Limit Groups Per user plugin allows site admins to restrict the number of groups a user can create on a BuddyPress based Social network.
== Description ==
Limit Groups Per user plugin allows site admins to restrict the number of groups a user can create on a BuddyPress based Social network.

Features include:

* No restriction to Site Admin
* restrict Maximum number of Groups Created by a user

== Installation ==

The plugin is simple to install:

1. Download `limit-groups-per-user.zip`
1. Unzip It
1. Upload `limit-grouops-per-user` directory to your `/wp-content/plugins` directory
1. Go to the plugin management page and activate the plugin "Limit Groups Per User", Activate the plugin sitewide if you want to use it sitewide.
1. Enter the maximum no. of groups in Dashboard->Settings->BuddyPress->Settings(Or Network Admin->Settings->BuddyPress->Settings)
Otherwise, Use the Plugin browser, upload it and activate, you are done.
== Frequently Asked Questions ==
= How to Use =
Go to Dashboard->Settings->BuddyPress->Settings, enter the maximum no. of groups a user can create. Click save. That's it.
= It is not showing the error message to user =
Since BuddyPress does not include the action "template_notices"
in directory pages, please edit your theme/groups/index.php and put it somewhere below the padder div
`<?php do_action( 'template_notices' ) ?>`

== Changelog == 
 = Version 1.2 =
 * Completely Rewritten
 * Updated for BuddyPress 1.9+
 * Please only upgrade if you are using BuddyPress 1.9 or above
 = Version 1.1.3 =
 * More fine tuned
 * Create group button can be hidden now
 = Version 1.1.1 =
 * Updated for BuddyPress 1.5
 = Version 1.0 =
*Initial release


== Other ==

For more info, please visit us at [BuddyDev.com](http://buddydev.com/ "The best place for all BuddyPress based plugins, themes tutorials")