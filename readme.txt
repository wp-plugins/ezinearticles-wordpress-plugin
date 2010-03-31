=== EzineArticles WordPress Plugin ===
Contributors: EzineArticles.com
Donate link: http://EzineArticles.com/
Tags: article submission, articles, writers, writing, publishing, ezine, free articles, article directory, printable articles
Requires at least: 2.7
Tested up to: 2.9.2
Stable tag: tags/1.6

Submit your high quality, original WordPress posts directly to EzineArticles.com.

== Description ==

The EzineArticles WordPress Plugin allows you to submit your high quality, original WordPress posts to EzineArticles.com, 
as well as monitor their review status right from the WordPress administration interface!

== Installation ==
Prerequisites:
WordPress 2.7 or higher
PHP 5.1 or higher

1. Download the plugin. There is a download button to the right.  
2. Unzip the `wp_ezinearticles.zip` file.
3. Upload `wp_ezinearticles` directory (not just the files in it) to the `/wp-content/plugins/` directory on your WordPress blog.
4. Activate the plugin through the 'Plugins' menu in WordPress.
5. Once activated, go to the new EzineArticles menu in the sidebar and click 'Options' to enter your EzineArticles API Key, Username and Password.
6. Push the 'Save Settings' button to save your information.

Installation Is Complete!

== Frequently Asked Questions ==

= I have the plugin installed, how do I submit to EzineArticles? =

When you add or edit a post, you will see a box labeled "WP EzineArticles" with a checkbox in it.  Click it to expand the additional fields required to submit to EzineArticles.

= How can I resubmit an old post as an article on EzineArticles? =

You can edit a post, open the "WP EzineArticles" box, set your settings, and then "Validate" and "Submit".  If you do press "Publish", it will update your WordPress post.

= I have changed my resource boxes on Members.ezinearticles.com, but they do not show up in WordPress. =

In your WordPress admin page under "EzineArticles" click the "Account" tab, click "Refresh account status".

= The plugin triggered a fatal error =

The first thing to check is that you have a recent version of php.  PHP version 5.1 or above is required to use this plugin.

= I am unable to upload the plugin to Dreamhost =

You are not be able to upload the plugin to Dreamhost if you cannot write to the wp-content/plugins folder.  
The basic 'easy mode' setup does not allow this because Dreamhost does not give you the correct permission to do so.
If you setup in 'advanced mode', you will be able to write to your plugins folder, and should be able to install custom plugins.

== Screenshots ==

1. Activating the plugin.
2. Set your log in information and API key under Options.
3. Check the checkbox on the 'WP Ezinearticles' box to publish your post.

== ChangeLog ==

= 1.6 =

* Premium members can now schedule from WordPress.
* Help menu added to the plugin.
* Bugfix: Fixed dissappearing API key.
* Bugfix: Fixed dissappearing options by using cookies. 
* Bugfix: Fixed a date issue returning epoch time.
* Bugfix: Improved code structure.

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