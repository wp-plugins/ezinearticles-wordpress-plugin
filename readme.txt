=== EzineArticles WordPress Plugin ===
Contributors: EzineArticles.com
Donate link: http://EzineArticles.com/
Tags: article submission, articles, writers, writing, publishing, ezine, free articles, article directory, printable articles
Requires at least: 2.7
Tested up to: 2.9.2
Stable tag: tags/2.0.1

Submit your high quality, original WordPress posts directly to EzineArticles.com.

== Description ==

The EzineArticles WordPress Plugin allows you to submit your high quality, original WordPress posts to EzineArticles.com, 
as well as monitor their review status right from the WordPress administration interface!

== Installation ==
Prerequisites:
WordPress 2.7 or higher
PHP 4.3 or higher

FTP install directions

1. Log into your WordPress admin area.  It should be at yourblogaddress/wp-admin/

2. Expand on the "Plugins" entry under your dashboard and click on "add new".

3. Search the plugins for "ezinearticles".

4. You will see a list of plugins, look for "EzineArticles WordPress Plugin" and click the install link.

5. If wordpress requires information for ftp, it will request it from you.  If not, it will install our plugin and link you to page where you can activate the plugin.

6. Activate the plugin.

7. Once activated, go to the new EzineArticles menu in the sidebar and click 'Account' to enter your EzineArticles Username and Password.

8. Push the 'Save Settings' button to save your information.

Alternate install directions

1. Download the plugin. There is a download button to the right.  

2. Unzip the `wp_ezinearticles.zip` file.

3. Upload `wp_ezinearticles` directory (not just the files in it) to the `/wp-content/plugins/` directory on your WordPress blog.

4. Activate the plugin through the 'Plugins' menu in WordPress.

5. Once activated, go to the new EzineArticles menu in the sidebar and click 'Account' to enter your EzineArticles Username and Password.

6. Push the 'Save Settings' button to save your information.

Installation Is Complete!

== Frequently Asked Questions ==

= I have the plugin installed, how do I submit to EzineArticles? =

When you add or edit a post, you will see a box labeled "WP EzineArticles" in it. It contains the additional fields required to submit to EzineArticles. Make sure they are filled out and then push the validate button.  Fix any errors that appear and when you are ready push the submit button.

= How can I resubmit an old post as an article on EzineArticles? =

You can edit a post, set your EzineArticles settings, and then "Validate" and "Submit".  If you do press "Publish", it will update your WordPress post.

= I can't see any categories. =

Try refreshing the page. Categories are now pulled from EzineArticles rather than stored locally, and it may need to refresh.

= The plugin triggered an error =

First check your php version. PHP version 4.3 or above is required, with newer being better.  If that is up to date, see if you can figure out what the error was by looking at the log for your webserver.  Any feedback on bugs will help us develop the next version.

= Wordpress MU =

Multiuser WordPress is not compatable with this plugin.

= I am unable to upload the plugin to Dreamhost =

You are not be able to upload the plugin to Dreamhost if you cannot write to the wp-content/plugins folder.  
The basic 'easy mode' setup does not allow this because Dreamhost does not give you the correct permission to do so.
If you setup in 'advanced mode', you will be able to write to your plugins folder, and should be able to install custom plugins.

== Screenshots ==

1. To add a new plugin, search for ezinearticles.
2. Find "EzineArticles WordPress Plugin" and click "install".
3. Click the "Install Now" button.
4. Activate the plugin after install.
5. Or activate in the plugin menu.
6. Set your EzineArticles Username and Password under Account.
7. When you have everything working, you will be able to submit an article.

== ChangeLog ==

= 2.0.1 =

* Fixed javascript error preventing publishing to user's blog.

= 2.0.0 =

* Rewrote code to support PHP 4.3
* New AJAX code should preserve settings more reliably.
* Added more detailed user messages for password and user name validation.
* Added ability to automatically request an api key with your username and password.
* Merged Accounts and Options pages.
* Condensed php for more reliable distribution.
* Changed naming scheme to avoid conflicts with other plugins.
* Help now allows the user to send an email with details of their problem.
* Bugfixes

= 1.6.5 =

* No longer need to click 'Save Draft' before validation.
* Updated help section.
* Bugfix: Was not displaying customized Resource Box text after validation.

= 1.6.2 =

* Improved instructions.
* Bugfix: Found an extra div tag that would prevent the page from rendering on the left side when invalid account information was used.
* Bugfix: Webkit based browsers would show the text of the Signature box where the name was.
* Bugfix: Non alphanumeric passwords now supported.

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
