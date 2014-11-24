=== AWStats Report Viewer ===
Contributors: xpointer
Donate link: http://wp-arv.xptrdev.com
Tags: access logs, logs, analytics, awstats, report, statistics, visits
Requires at least: 3.0.1
Tested up to: 4.1
Stable tag: 0.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

View CPanel's AWStats report via Wordpress Dashboard page.

== Description ==

The Plugin is allowing Wordpress masters to view AWStats report via Wordpress Dashboard page. Its provide a basic 
funtions for Create, Delete and Update AWStats report.

== Installation ==

1. Upload `awstats-report-viewer.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'AWStats Report Installer' Dashboard page anf follow the instruction to complete the installation
4. After Installation completed, the awstats report is automatically created for the first time and you will prmopted to go to the report page.

== Requirements ==
* Linux Server with CPanel installed
* PHP >= 5.3

== Frequently Asked Questions ==

= Could I use ARV under Windows Server? =

No. It won't work. Its developed to use AWStats report that is mostely installed with the CPanel that comes with Linux servers.

= Should I change the installation Parameters? =

Only if aware of what you're doing. ARV Plugin is automatically discover installation parameters for you and it would work under most system ocnfiguration

= What is the purspose of the 'Regenerate' button? =

ARV Plugin is saving AWStats report under Wordpress wp-content folder. It creates a unique LONG number
for the report directory name (e.g: 954693ec2b66aa9f51876107bf1880ef54707a492c40c0.87758923), it then gives
every file a unique identifier. If you felt that, for some reason, the report is accessed from Public user you can change the unique identified by forcing
ARV to re-genarate all the exists Unique Ids.

== Screenshots ==

1. Installation Form
2. Report Screen

== Changelog ==

= 0.5 =
First release