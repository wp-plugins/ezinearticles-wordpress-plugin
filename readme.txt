=== EzineArticles WordPress Plugin ===
Contributors: EzineArticles.com
Donate link: http://EzineArticles.com/
Tags: submission, article marketing, ezine, ezinearticles
Requires at least: 2.7
Tested up to: 2.9.2
Stable tag: tags/1.5.2

Submit your high quality, original WordPress posts directly to EzineArticles.com.

== Description ==

The EzineArticles WordPress Plugin allows you to submit your high quality, original WordPress posts to EzineArticles.com, 
as well as monitor their review status right from the WordPress administration interface!

== Installation ==

1. Upload `wp_ezinearticles` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Once activate, go to the new EzineArticles menu in the sidebar and click 'Options' to enter your EzineArticles API Key, Username and Password.
4. Push the 'Save Settings' button to save your information.

1, 2, 3: Installation Is Complete!

== Frequently Asked Questions ==

= The plugin triggered a fatal error.

The first thing to check is that you have a recent version of php.  PHP version 5.1 or above is required to use this plugin.

= I am unable to upload the plugin to Dreamhost.

You are not be able to upload the plugin to Dreamhost if you cannot write to the wp-content/plugins folder.  
The basic 'easy mode' setup does not allow this because Dreamhost does not give you the correct permission to do so.
If you setup in 'advanced mode', you will be able to write to your plugins folder, and should be able to install it.

== ChangeLog ==

= 1.5.2 =

* Added instructions to save draft before validation in order to avoid losing tags.
* Support for users without a resource box.
* Bugfixes

= 1.5.1 =

* Bugfix release

= 1.5 =

* Added the ability to choose a resource box.  The text can be tweaked inside of WordPress before the article is submitted.
* Users can now validate their articles to check for content not allowed by EzineArticles before they submit.
* Users can submit without posting to WordPress by pushing the submit button on the EzineArticles tab.
* Added version checks to check for the correct php version and the correct WordPress version when installing.
* Bugfixes