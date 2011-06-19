=== Limit Groups Per User ===
Contributors:sbrajesh
Donate link: http://buddydev.com/donate/
Tags: buddypress,buddypress activity,sitewide activity, sitewide activity widget,buddypress sitewide activity widget
Requires at least: wordpress 1.9.2+buddypress 1.2
Tested up to:buddypress 1.2.3
Stable tag: 1.0

Limit Groups Per user plugin allows site admins to restrict the number og groups a user can create on a buddypress based Social network.
== Description ==
Limit Groups Per user plugin allows site admins to restrict the number og groups a user can create on a buddypress based Social network.

Features include:

* No restriction to Site Admin
* restrict Maximum number of Groups Created by a user

== Installation ==

The plugin is simple to install:

1. Download `limit-groups-per-user.zip`
1. Unzip It
1. Upload `limit-grouops-per-user` directory to your `/wp-content/plugins` directory
1. Go to the plugin management page and activate the plugin "Limit Groups Per User", Activate the plugin sitewide if you want to use it sitewide.
1. Enter the maximum no. of groups in Dashboard->BuddyPress->Seneral Settings
Otherwise, Use the Plugin browser, upload it and activate, you are done.
== Frequently Asked Questions ==
= How to Use =
Go to Dashboard->Buddypress->General settings, eneter the maximum no. of groups a user can create. Click save. That's it.
= It is not showing the error message to user =
Since BuddyPress does not include the action "template_notices"
in directory pages, please edit your theme/groups/index.php and put it somewhere below the padder div
`<?php do_action( 'template_notices' ) ?>`

== Changelog == 
 = Version 1.0 =
*Initial release


== Other ==

For more info visit us at [BuddyDev.com](http://buddydev.com/ "The best place for all buddypress based plugins,themes tutorials")