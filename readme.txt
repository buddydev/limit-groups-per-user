=== Limit BuddyPress Groups Per User ===
Contributors: buddydev,sbrajesh,raviousprime
Tags: buddypress, buddypress limit groups, groups
Requires at least: 4.5
Tested up to: 5.4.1
Stable tag: 2.0.2

Limit Groups Per user plugin allows site admins to restrict the number of groups a user can create on a BuddyPress based Social network.
== Description ==
Limit Groups Per user plugin allows site admins to restrict the number of groups a user can create on a BuddyPress based Social network.

Features include:

* Role based restrictions for BuddyPress group creation
* Restrict Maximum number of Groups Created by a user with a a specific role( Role based limit)

== Installation ==

The plugin is simple to install:

1. Download `limit-groups-per-user.zip`
1. Unzip It
1. Upload `limit-groups-per-user` directory to your `/wp-content/plugins` directory
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

== Upgrade ==
After Installation/Upgrade, Please visit Dashboard->settings->Limit Groups Per User and update settings.

== Changelog ==

 = Version 2.0.2 =
  * Exclude settings for bbPress roles.
  * Change the strategy for checking role based restricitons.

 = Version 2.0.0 =
  * Add settings to support role based limits.
  * Not backward compatible. Please visit Dashboard->Settings->LImit Groups Per User to enable the restrictions.

 = Version 1.2.1 =
  * Minor changes

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