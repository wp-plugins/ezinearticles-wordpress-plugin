<?php
/*
Plugin Name: WP EzineArticles
Plugin URI: http://EzineArticles.com/
Description: The EzineArticles WordPress Plugin allows you to submit your high quality, original WordPress posts to EzineArticles.com, as well as monitor their review status right from the WordPress administration interface!
Version: 2.0.5
Author: EzineArticles.com
Author URI: http://EzineArticles.com/
*/


define('WP_EZINEARTICLES_PLUGIN_NAME', 'WP EzineArticles');
define('WP_EZINEARTICLES_NAME', 'EzineArticles');
define('WP_EZINEARTICLES_GENERAL_OPTION_NAME', 'ezinearticles_options');

define('WP_EZINEARTICLES_PLUGIN_VERSION', '2.0.5');
define('WP_EZINEARTICLES_MIN_PHP_VERSION', '4.3');
define('WP_EZINEARTICLES_MIN_WP_VERSION', '2.7');


define('WP_EZINEARTICLES_FOLDER', dirname(plugin_basename(__FILE__)), TRUE);
define('WP_EZINEARTICLES_PLUGIN_PATH', WP_CONTENT_URL . '/plugins/' . WP_EZINEARTICLES_FOLDER);
define('WP_EZINEARTICLES_PLUGIN_ROOT',  ABSPATH . 'wp-content/plugins/' . WP_EZINEARTICLES_FOLDER);
define('WP_EZINEARTICLES_LOG_FILE', WP_EZINEARTICLES_PLUGIN_ROOT . '/log.txt');


if(file_exists(ABSPATH.'wp-includes/class-snoopy.php') && !class_exists("Snoopy"))
{
	include_once(ABSPATH.'wp-includes/class-snoopy.php');
}

if(is_admin())
{
	register_activation_hook(__FILE__, 'wp_ezinearticles_install');
	register_deactivation_hook(__FILE__, 'wp_ezinearticles_deactivate');

	add_action('admin_menu', 'wp_ezinearticles_add_menu');
	add_action('admin_head', 'wp_ezinearticles_assets');
	add_action('admin_menu', 'wp_ezinearticles_add_post_meta_box');
	add_action('wp_ajax_ezinearticles_submit', 'wp_ezinearticles_submit');
	add_action('wp_ajax_ezinearticles_author_resources', 'wp_ezinearticles_get_author_resources');
}

//This adds the metabox
function wp_ezinearticles_add_post_meta_box()
{
	if(function_exists('add_meta_box'))
	{
		add_meta_box( 'wp-ezinearticles', WP_EZINEARTICLES_PLUGIN_NAME, 'wp_ezinearticles_post_meta_box', 'post', 'side', 'high');
	}
}

function wp_ezinearticles_add_menu()
{
	add_menu_page(WP_EZINEARTICLES_NAME,  WP_EZINEARTICLES_NAME, 6, 'wp_ezinearticles', 'wp_ezinearticles',  WP_CONTENT_URL . '/plugins/' . WP_EZINEARTICLES_FOLDER . '/img/ea.png');
	add_submenu_page('wp_ezinearticles', __('', WP_EZINEARTICLES_PLUGIN_NAME), __('Articles', WP_EZINEARTICLES_PLUGIN_NAME), 6, 'wp_ezinearticles', 'wp_ezinearticles');
	add_submenu_page('wp_ezinearticles', __('', WP_EZINEARTICLES_PLUGIN_NAME), __('Account', WP_EZINEARTICLES_PLUGIN_NAME), 6, 'wp_ezinearticles_account', 'wp_ezinearticles_account_view');
	add_submenu_page('wp_ezinearticles', __('', WP_EZINEARTICLES_PLUGIN_NAME), __('Help', WP_EZINEARTICLES_PLUGIN_NAME), 8, 'wp_ezinearticles_help', 'wp_ezinearticles_help_view');
}

function wp_ezinearticles_get_option($option_name)
{
	$options = get_option(WP_EZINEARTICLES_GENERAL_OPTION_NAME);

	if(isset($options[$option_name]))
	{
		return $options[$option_name];
	}

	return false;
}

function wp_ezinearticles_update_option($option_name, $option_value)
{
	$ezinearticles_options = get_option(WP_EZINEARTICLES_GENERAL_OPTION_NAME);
	$ezinearticles_options[$option_name] = $option_value;
	update_option(WP_EZINEARTICLES_GENERAL_OPTION_NAME, $ezinearticles_options);
}

// Display Lists of Blog Posts Submitted to EzineArticles.com
function wp_ezinearticles()
{
	$article_list = wp_ezinearticles_get_article_list();

	?>

<div class="wrap">
	<h2><?php _e(WP_EZINEARTICLES_PLUGIN_NAME) ?></h2>
	<p><?php _e('This page is displaying most recent blog posts you have submitted to EzineArticles.com.') ?></p>
	<table class="widefat post fixed" cellpadding="0">
	<thead>
		<tr>
			<th><?php _e('Post') ?></th>
			<th><?php _e('Category') ?></th>
			<th><?php _e('Status') ?></th>
			<th><?php _e('Submitted') ?></th>
		</tr>
	</thead>
	<tbody>

	<?php if(!count($article_list)): ?>

		<tr>
			<td colspan="4"><?php _e('Sorry, you do not have any blog posts submitted to EzineArticles.') ?></td>
		</tr>

	<?php else: ?>
		<?php foreach($article_list as $article): ?>

		<tr>
			<td class="post-title">
				<strong><a class="row-title" href="post.php?action=edit&amp;&post=<?php _e($article['post_id']) ?>"><?php _e($article['title']) ?></a></strong>
			</td>
			<td><?php

				$category = __( preg_replace('/[^a-zA-Z]/',' ', $article['main_category']) );
				if(isset($article['sub_category']) && $article['sub_category'])
					$category .= ' &rsaquo; ' . __( preg_replace('/[^a-zA-Z]/',' ', $article['sub_category']) );

				echo $category;

			?></td>
			<td>
<?php			$api_article_results = wp_ezinearticles_post_search('account.article.view', array('article_id' => $article['article_id'], "status" => "live"));
				if(isset($api_article_results[0]['article']['id']) && $api_article_results[0]['article']['id'] && $api_article_results[0]['article']['status'] == "live"):	?>
				<a href="http://ezinearticles.com/<?php echo $article['article_id'] ?>"><?php _e( ucwords($article['status']) ) ?></a>
<?php 			else:	?>
				<?php _e( ucwords($article['status']) ) ?>
<?php			endif;	?>
			</td>
			<td><?php _e( mysql2date( get_option("date_format"), $article['submit_date']) ) ?></td>
		</tr>

		<?php endforeach; ?>
	<?php endif; ?>

	</tbody>
	</table>
</div>

	<?php
}

//This returns a list of articles you have submitted to EzineArticles through this plugin
function wp_ezinearticles_get_article_list()
{
	global $wpdb;

	$article_list = array();

	if($submitted_posts = $wpdb->get_results('SELECT * FROM `'.$wpdb->prefix.'ezinearticles_posts_to_articles`'))
	{

		foreach($submitted_posts as $submitted_post)
		{
			$api_article_results = wp_ezinearticles_post_search('account.article.view', array('article_id' => $submitted_post->article_id));

			if(isset($api_article_results[0]['article']['id']) && $api_article_results[0]['article']['id'])
			{
				$api_article_result = $api_article_results[0]['article'];
				$article_list[] = array(
					'post_id' => $submitted_post->post_id,
					'article_id' => $api_article_result['id'],
					'title' => $api_article_result['title'],
					'main_category' => $api_article_result['category'],
					'sub_category' => $api_article_result['subcategory'],
					'status' => $api_article_result['status'],
					'submit_date' => $api_article_result['date_submitted']
				);

			}
		}
	}
	return $article_list;
}

//This displays an Account page
function wp_ezinearticles_account_view()
{
	global $current_user;

	$ea_options = wp_ezinearticles_current_options();
	if(isset($_POST['save_settings']) || isset($_POST['get_key']))
	{
		$ea_options['ea_email'] = $_POST['ea_email'];
		$ea_options['ea_password'] = $_POST['ea_password'];

		$has_api_key = true;
		if (isset($ea_options['ea_email']) && isset($ea_options['ea_password']))
		{
			$data['search']= 'api.key';
			$data['email']= $ea_options['ea_email'];
			$data['pass']= $ea_options['ea_password'];
			$data['name']= $current_user->display_name;
			$data['url']= get_bloginfo('siteurl');
			$data['details']=
				"Created by WordPress Plugin\n".
				'Version:'.WP_EZINEARTICLES_PLUGIN_VERSION."\n".
				'Blog Name:'.get_bloginfo('blogname');
			$key = wp_ezinearticles_post_search('api.key', $data, true);
			if (isset($key['error']))
			{
				$errorPlainText = $key['error'];
				$has_api_key = false;
				wp_ezinearticles_log_event("email: ".$data['email']." passwordHash: ".md5($data['pass']) ." Error: ".$errorPlainText ,'error');
			}
			else
			{
				$key=$key['api.key'][0]['key'];
				$ea_options['ea_api_key'] = $key;
				wp_ezinearticles_log_event('Api Key Obtained: '.$key, 'getkey');
			}
		}
		else
		{	//You need a password and user or you will have no api key. print an error
			$has_api_key = false;
			$errorPlainText = "Please check that both username and password are entered.";
			$ea_options['pass'] = '';
			$ea_options['email'] = '';
		}

		$ea_options = array_merge(wp_ezinearticles_current_options(), $ea_options);
		update_option(WP_EZINEARTICLES_GENERAL_OPTION_NAME, $ea_options);
		if($has_api_key)
		{
			$accStat= wp_ezinearticles_post_search('account.status', true,true);

			if(isset($accStat["error"]))
			{
				$errorPlainText = $accStat["error"];
				wp_ezinearticles_log_event("Obtaining account status from the api returned an error: ".$errorPlainText);

			}
			else $errorPlainText = null;
			$ea_options['ea_error'] = $errorPlainText;

			if (isset($accStat['account.status']))
			{
				$ea_options['ea_account_status'] = $accStat['account.status'];
			}
			else
			{
				wp_ezinearticles_log_event("The account status was not received by the plugin.");
			}
			$ea_options = array_merge(wp_ezinearticles_current_options(), $ea_options);
			update_option(WP_EZINEARTICLES_GENERAL_OPTION_NAME, $ea_options);

			if(!$accStat)
			{
				wp_ezinearticles_log_event('No response from api.');
				wp_ezinearticles_message('Settings saved. However, a connection could not be made to ezinearticles.');
			}
			else if(!isset($errorPlainText)||$errorPlainText=='')
			{
				wp_ezinearticles_message('Settings saved and validated.');
			}
			else
			{
				wp_ezinearticles_message('Setting saved locally. However, the server returned an error message:<br>'.$errorPlainText);
			}
		}
		else
		{
			wp_ezinearticles_message('Setting saved locally. However, the server returned an error message:<br>'.$errorPlainText);
		}
	}
	else if(isset($_POST['refresh_account_status']))
	{
		$status = wp_ezinearticles_post_search('account.status', false, true);
		$ea_options['ea_account_status'] = $status['account.status'];
		$ea_options = array_merge(wp_ezinearticles_current_options(), $ea_options);
		update_option(WP_EZINEARTICLES_GENERAL_OPTION_NAME, $ea_options);
		if (isset($status['error']))
		{
			$errorReason = $status['error'];
			wp_ezinearticles_log_event("Error returned while refreshing AccountStatus: ".$errorReason);
		}
	}
	else
	{//redisplay old error
		$errorReason = wp_ezinearticles_get_option('ea_error');
	}

	$account_status = wp_ezinearticles_get_option('ea_account_status');

	?>
<div class="wrap">
<h2><?php echo WP_EZINEARTICLES_PLUGIN_NAME ?> - Account</h2>

	<form method="post">

		<h3><?php _e('Your EzineArticles Account Information') ?></h3>
		<table width="100%" cellpadding="2" cellspacing="5" class="editform">

			<tr>
				<td align="left" valign="top" width="25%" nowrap><?php _e('Your EzineArticles Username:')?></td>
				<td><input type="text" name="ea_email" id="ea_email" size="30" value="<?php echo wp_ezinearticles_get_option('ea_email')?>" tabindex="2">
				<small>
					<br />
					<?php _e('Enter your EzineArticles Membership Account username (email).') ?><br />
					<?php _e(' If you are not yet a member of EzineArticles.com, ') ?>
					<a target="_blank" href="http://ezinearticles.com/submit/"><?php _e('Click Here') ?></a>
					<?php _e('for') ?><br />
					<?php _e('your FREE  Basic Membership Account.') ?>
				</small>
				</td>
			</tr>

			<tr>
				<td align="left" valign="top" width="25%" nowrap><?php _e('Your EzineArticles Password:')?></td>
				<td><input type="password" name="ea_password" id="ea_password" size="30" value="<?php echo wp_ezinearticles_get_option("ea_password")?>" tabindex="3">
				<small>
					<br /><?php _e('Enter your EzineArticles Membership Account password.') ?>
				</small>
				</td>
			</tr>
		</table>

		<div class="submit">
			<input type="submit" name="save_settings" value="<?php _e('Save Settings')?>">
		</div>
	</form>


<?php if(isset($account_status) && $account_status != null): ?>

	<?php $account_status_zero = $account_status[0]; ?>

	<h3><?php _e('Your EzineArticles Account Status') ?></h3>
	<form action="" method="post">

	<table width="100%" cellpadding="2" cellspacing="5" class="editform">
	<tr>
		<td align="left" valign="top" width="25%" nowrap><?php _e('Primary Author:')?></td>
		<td><?php _e( wp_ezinearticles_get_primary_author() );?></td>
	</tr>
		<tr>
		<td align="left" valign="top" width="25%" nowrap><?php _e('Membership Level:')?></td>
		<td><?php _e($account_status_zero['membership_level']) ?></td>
	</tr>
	<?php if(isset($account_status_zero['membership_status']) && $account_status_zero['membership_status'] == 'Premium') { ?>
	<tr>
		<td align="left" valign="top" widtd="25%" nowrap><?php _e('Membership Status:')?></td>
		<td><span class="ea-premium">$ <?php _e('Premium Member') ?></span></td>
	</tr>
	<?php }?>
	<tr>
		<td align="left" valign="top" widtd="25%" nowrap><?php _e('Submissions Left:')?></td>
		<td><?php _e($account_status_zero['submissions_left']) ?></td>
	</tr>
	</table>
		<div class="submit">
		<input type="submit" name="refresh_account_status" value="<?php _e('Refresh account status')?>">
	</div>

	<?php endif; ?>
</div>
	<?php
}

//This displays the help tab
function wp_ezinearticles_help_view()
{

	$logged_events = wp_ezinearticles_get_logged_events();

	$debug_message_sent = false;
	if(isset($_POST['ezinearticles_debug_message'] ))
	{
		if (trim($_POST['ezinearticles_debug_message']) != '')
		{
			$message = trim($_POST['ezinearticles_debug_message']);

			if(isset($_POST['ezinearticles_send_event_log']))
			{
				$message .= "\n\n------------\n Event Log:\n------------\n";

				foreach($logged_events as $event)
				{
					$message .= "Date: {$event['date']}\nType: {$event['type']}\nDetails: {$event['details']}\n\n";
				}
			}

			wp_ezinearticles_send_debug_email($message);
			$debug_message_sent = true;
		}
		else
		{
			wp_ezinearticles_message('Please enter a description of your problem.', 'error');
		}
	}



	?>
<div class="wrap">
	<h2><?php echo WP_EZINEARTICLES_PLUGIN_NAME; ?> - <?php _e('Help') ?></h2>
	<?php _e('This describes the basic steps for publishing to EzineArticles through this WordPress Plugin.') ?>

	<h3><?php _e('1) Write an Article') ?></h3>
	<p>
		<?php _e('The first step in submitting a high quality article is writing one. Expand \'Posts\' and either add a new post or edit one you already have.') ?><br>
		<?php _e('Once your post is to your liking, you can fill in the information that EzineArticles will need to publish it as an article.') ?>
	</p>
	<hr>
	<h3><?php _e('2) Fill EzineArticles related fields.') ?></h3>
	<p>
		<?php _e('There are several fields that must be filled to allow you to post to EzineArticles.com. They are located in the window labeled \'WP EzineArticles\'.') ?>
	</p>
	<ul>
		<li>
			<b><?php _e('Title') ?></b> -
			<?php _e('You must choose a unique title.  Validation will tell you if it has been used before.') ?>
		</li>
		<li>
			<b><?php _e('Body') ?></b> -
			<?php _e('Your body will be taken from the body of your post.  You will most likely need to alter this to pass validation.') ?>
		</li>
		<li>
			<b><?php _e('Category') ?></b> -
			<?php _e('You must select a category for the article from the drop down list.') ?>
		</li>
		<li>
			<b><?php _e('Authors') ?></b> -
			<?php _e('The author drop down should have a list of authors under your EzineArticles account.  Select the one you want to use.') ?>
		</li>
		<li>
			<b><?php _e('Resource Box') ?></b> -
			<?php _e('The resource box drop down will contain a list of resource boxes you have saved in your EzineArticles account.') ?>
			<?php _e('Selecting one and pushing edit will allow you to edit it for the next submit.') ?>
			<?php _e('This box is meant for tweaking for a specific article and will not replace the one stored in your EzineArticles account.') ?>
			<?php _e('There is currently no way to permanently overwrite your resource box from WordPress, you will need to log in to your account at http://Members.EzineArticles.com/ to do that.') ?>
		</li>
		<li>
			<b><?php _e('Summary') ?></b> -
			<?php _e('Allows you to choose whether the excerpt or the first two sentences of the body is used for the summary.') ?>
		</li>
		<li>
			<b><?php _e('Keywords') ?></b> -
			<?php _e('Allows you to choose whether you want Custom Keywords or to use the Post tags. Post Tags are converted into keywords for EzineArticles.  You will need to add tags in the Post Tags Field.') ?>
			<?php _e('By using Custom Keywords, you can enter different keywords for your EzineArticle by entering them with commas separating each keyword.') ?>
		</li>
		<li>
			<b><?php _e('Schedule') ?></b> -
			<?php _e('If you have a premium account you can schedule the release date on EzineArticles. You can schedule the release on the wordpress blog separately in the Publish window.') ?>
		</li>
	</ul>

	<hr>
	<h3><?php _e('3) Validate your Article') ?></h3>
	<p>
		<b><?php _e('Validate') ?></b> -
		<?php _e('Validates your article against the EzineArticles Editorial Guidelines. It will tell you if there are any parts of your article that would cause it to be automatically rejected.') ?>
		<?php _e('You will see a box appear at the bottom of the WP EzineArticles tab with the details of any problems that were encountered.  If you still want these in your WordPress version, ') ?>
		<?php _e('you should publish for WordPress, edit it, and then submit it to EzineArticles.') ?>
	</p>
	<hr>
	<h3><?php _e('4) Publish') ?></h3>
	<p>
		<?php _e('You are now able to publish to EzineArticles and WordPress separately.  This allows you to tweak your article for EzineArticles while having the freedom to publish as you wish on your WordPress blog.') ?>
	</p>
	<ul>
		<li>
			<b><?php _e('Submit') ?></b>  -
			<?php _e('This submits your post to EzineArticles for review.  If there are any automatically detected issues with submission it will report them to you in the same way as validate.') ?>
		</li>
		<li>
			<b><?php _e('Publish') ?></b> -
			<?php _e('This publishes the post to your WordPress blog.') ?>
		</li>
	</ul>
	<hr>
	<h3><?php _e('Having Problems?') ?></h3>
	<?php if($debug_message_sent): ?>
		<?php wp_ezinearticles_message( __('An email has been sent to the EzineArticles.com Support team.') ); ?>
	<?php endif; ?>
	<form action="" method="POST">
		<p><?php _e('If you are having problems with the plugin you can send an email to our Member Support Team at EzineArticles.com.') ?></p>
		<p>
			<fieldset>
				<legend><?php _e('Description') ?>:</legend>
				<textarea  rows="5" cols="100" name="ezinearticles_debug_message"></textarea>
			</fieldset>
		</p>
		<?php if(count($logged_events)): ?>
		<div id="ezinearticles-send-event-log-details-wrap">
			<input id="ezinearticles-send-event-log" name="ezinearticles_send_event_log" type="checkbox" checked="checked"> <b><?php _e('Send Event Log Information') ?></b> <a href="#" id="ezinearticles-send-event-log-detail-link"><?php _e('Show Details') ?></a><br>

			<div id="ezinearticles-send-event-log-details" style="display:none">
				<p>
					<?php _e('Below are some details we record while you are using the EzineArticles plugin. This information can help us investigate') ?>
					<?php _e('problems that you may be having. If you do not wish to send along this information, simply uncheck the box next to the') ?>
					<?php _e('"Send Event Log Information" checkbox.') ?>
				</p>
				<table class="widefat post fixed" cellpadding="0">
				<thead>
					<tr>
						<th style="width:150px"><?php _e('Date') ?></th>
						<th style="width:150px"><?php _e('Type') ?></th>
						<th><?php _e('Event Details') ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($logged_events as $event): ?>
					<tr>
						<td><?php _e( mysql2date( get_option('date_format') . ' ' . get_option('time_format'), $event['date']) ) ?></td>
						<td><?php _e($event['type']) ?></td>
						<td><pre><?php _e($event['details']) ?></pre></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
				</table>
			</div>
		</div>
		<?php endif; ?>
		<input id="ea-send-dbug" class="button button-highlighted" type="submit" value="Send Email" name="submit" title="Send Email">
	</form>
</div>
<?php
}

//this sets up the metabox
function wp_ezinearticles_post_meta_box()
{
	global $post, $wpdb;

	$article = null;
	$excerpt = get_the_excerpt();

	$accountStatus = wp_ezinearticles_get_option('ea_account_status');
	if ($post->ID)
	{
		$isEzineArticle = wp_ezinearticles_is_article($post->ID);
		if($isEzineArticle)
		{
			$EzineArticle = wp_ezinearticles_post_search('account.article.view', array('article_id' => $isEzineArticle), true);
			if(isset($EzineArticle['account.article.view'][0]['article']['id']) && $EzineArticle['account.article.view'][0]['article']['id'])
			{
				$article = $EzineArticle['account.article.view'][0]['article'];
				$article['resource'] = $article['resource_box'];
				//If the article is scheduled show that date
				if(isset($article['date_scheduled']))
				{
					$article['schedule'] = $article['date_scheduled'];
				}
				$article['summary'] = ($article['summary'] == $excerpt) ? 1 : 0;
				$article['category'] = str_replace("-", " ", $article['category']);

				if(isset($article['subcategory']) && $article['subcategory'] != "" )
				{
					$article['subcategory'] = str_replace("-", " ", $article['subcategory']);
					$article['category'] = $article['category'].':'.$article['subcategory'];
				}
				$article = (object) $article;
			}
			elseif (isset($EzineArticle['error']) &&
				(isset($EzineArticle['response_info']['status'])
				&& $EzineArticle['response_info']['status'] == 200))
			{
				$wpdb->query("DELETE FROM {$wpdb->prefix}ezinearticles_posts_to_articles WHERE post_id = '" . $post->ID . "'");
				wp_ezinearticles_log_event( "API could not find article id $isEzineArticle. Deleted.", 'delete');
			}
			$status = wp_ezinearticles_post_search('account.status', false, true);
			$accountStatus = $status['account.status'];
		}
		else
		{
			$article = wp_ezinearticles_get_unsubmitted_article($post->ID);
		}
	}

	wp_ezinearticles_post_meta_box_view( $accountStatus, wp_ezinearticles_get_option('ea_error'),$article);

}

//This will print a message to the screen.
function wp_ezinearticles_message($message, $class = 'updated')
{
	global $post;
	?>
<div id="ezinearticles-message" class="<?php echo $class?>">
	<p><?php if($post) echo '<b>'.WP_EZINEARTICLES_PLUGIN_NAME.'</b>:'?> <?php echo $message?></p>
</div>
	<?php
}

//This will display a dropdown list of the primary and alternate authors
function wp_ezinearticles_get_author_select($article = null)
{
	$ea_account_status = wp_ezinearticles_get_option('ea_account_status');
	$ea_account_status = $ea_account_status[0];

	if( !isset($ea_account_status['account_author'])) return false;

	$selected = (isset($article)) ? stripslashes($article->author) : null;

	?>
<select name="ea_author" id="ea-author">

<?php
if ($selected == $ea_account_status['account_author']) : ?>
	<option selected="selected" value="<?php echo $ea_account_status['account_author']?>"><?php echo $ea_account_status['account_author']?></option>
<?php else: ?>
	<option value="<?php echo $ea_account_status['account_author']?>"><?php echo $ea_account_status['account_author']?></option>
<?php endif; ?>

<?php if(isset($ea_account_status['alternate']['author'])) : ?>
	<?php foreach($ea_account_status['alternate']['author'] as $ea_alternate_author) : ?>
		<?php if($selected == $ea_alternate_author) : ?>
			<option selected="selected" value="<?php echo $ea_alternate_author?>"><?php echo $ea_alternate_author?></option>
		<?php else : ?>
			<option value="<?php echo $ea_alternate_author?>"><?php echo $ea_alternate_author?></option>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>

</select>
	<?php
	return true;
}

//This echos the primary author name
function wp_ezinearticles_get_primary_author()
{
	$ea_account_status = wp_ezinearticles_get_option('ea_account_status');
	$ea_account_status = $ea_account_status[0];

	if( !isset($ea_account_status['account_author'])) return false;
	echo($ea_account_status['account_author']);
}

function wp_ezinearticles_get_logged_events()
{
	global $wpdb;

	$results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}ezinearticles_diagnostic_log` WHERE `type` <> 'activate' ORDER BY `date` DESC LIMIT 10", ARRAY_A);
	$install_version = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}ezinearticles_diagnostic_log` WHERE `type` = 'activate' ORDER BY `date` DESC LIMIT 4", ARRAY_A);

	$results = array_merge($install_version,$results);

	if(!is_array($results))
		return array();

	return $results;
}

//This gets the resource boxes for an author
function wp_ezinearticles_get_author_resources()
{
	$author = stripslashes($_POST['author']);

	$status = wp_ezinearticles_post_search('account.status', false, true);
	$options = $status['account.status'];

	$author_resources = array();

	foreach($options as $option)
	{
		if (isset($option['resource']))
			if ($option['resource'][0]['author'] == $author)
				$author_resources[] = array(htmlentities($option['resource'][0]['name']), htmlentities($option['resource'][0]['body']));
	}
	header('Content-Type: text/xml');
	echo "<?xml version='1.0' standalone='yes'?><wp_ajax><author_resources>";

	foreach($author_resources as $resource)
	{
		echo "<resource><name><![CDATA[{$resource[0]}]]></name><body><![CDATA[{$resource[1]}]]></body></resource>";
	}

	echo "</author_resources></wp_ajax>";
	exit;
}

//This returns an array of categories
function wp_ezinearticles_get_categories()
{
	$categories_option_value = wp_ezinearticles_get_option('ezinearticles_categories');

	if(isset($categories_option_value['category_list']) && isset($categories_option_value['last_updated']))
	{
		if( (time() - $categories_option_value['last_updated']) < 86400 )
		{
			return $categories_option_value['category_list'];
		}
	}

	$categories = wp_ezinearticles_update_categories();

	if(is_array($categories))
	{
		return $categories;
	}

	return false;
}

//This updates the categories
function wp_ezinearticles_update_categories()
{
	$categories = array();

	$api_categories = wp_ezinearticles_post_search('categories');

	//simplify results from api call
	foreach($api_categories as $api_category)
	{
		if(isset($api_category['category']))
		{
			//Add main category to final category list array
			$main_category = $api_category['category']['name'];
			$categories[] = $main_category;

			if(isset($api_category['category']['subcategory']) && $api_category['category']['subcategory'][0] != '')
			{
				foreach($api_category['category']['subcategory'] as $sub_category)
				{
					$categories[] = $main_category . ':' . $sub_category;
				}
			}
		}
	}

	//add an update timestamp so categories stay up to date
	$ezinearticles_categories = array(
		'category_list' => $categories,
		'last_updated' => time()
	);

	wp_ezinearticles_update_option('ezinearticles_categories', $ezinearticles_categories);

	return $categories;
}

//This displays a dropdown that has the categories in it.
function wp_ezinearticles_print_category_select($article = null)
{
	$categories = wp_ezinearticles_get_categories();
	if(!$categories)
	{
		return false;
	}

	$selected_category = (isset($article)) ? $article->category : false;

	$select_options = array();

	foreach($categories as $category)
	{
		if(strpos($category, ':') !== FALSE)
		{
			//category is a sub category
			$select_options[] = array(
				'value' => $category,
				'text' => '&rsaquo; ' . __( substr($category, (strpos($category, ':') + 1)) ),
				'style' => '',
				'selected' => (($selected_category == $category) ? 1 : 0)
			);
		}
		else
		{
			//category is a main category
			$select_options[] = array(
				'value' => $category,
				'text' => __($category),
				'style' => 'font-weight:bold',
				'selected' => (($selected_category == $category) ? 1 : 0)
			);
		}
	}

	?>

<select name="ezinearticles_category" id="ezinearticles-category">
	<?php foreach($select_options as $select_option): ?>
	<option value="<?php echo $select_option['value']?>"<?php echo ( ($select_option['style']) ? ' style="'.$select_option['style'].'"' : '' ); ?><?php echo ( ($select_option['selected'] == 1) ? ' selected="selected"' : '' ); ?>><?php echo $select_option['text'] ?></option>
	<?php endforeach; ?>
</select>

	<?php

	return true;
}

//This gets the credentials for contacting the server.
function wp_ezinearticles_current_options()
{
	$options = array();
	$options['ea_api_key'] = wp_ezinearticles_get_option('ea_api_key');
	$options['ea_email'] = wp_ezinearticles_get_option('ea_email');
	$options['ea_password'] = wp_ezinearticles_get_option('ea_password');
	return $options;
}

function wp_ezinearticles_assets()
{
	wp_ezinearticles_css();
	wp_ezinearticles_js();
}

//This creates the neccessary tables, as well as migrates old data and removes old tables.
function wp_ezinearticles_install()
{
	global $wpdb;
	wp_ezinearticles_version_check();

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$create_sql = "CREATE TABLE `{$wpdb->prefix}ezinearticles_diagnostic_log` (
	`id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`type` VARCHAR(50),
	`details` TEXT,
	`date` DATETIME,
	KEY `date` (`date`),
	KEY `type` (`type`)
	)";

	dbDelta($create_sql);

	$create_sql = "CREATE TABLE `{$wpdb->prefix}ezinearticles_posts_to_articles` (
	`post_id` BIGINT(20) unsigned NOT NULL,
	`article_id` BIGINT(20) unsigned NOT NULL,
	UNIQUE KEY `post_id` (`post_id`),
	UNIQUE KEY `article_id` (`article_id`)
	)";

	dbDelta($create_sql);

	$create_sql = "CREATE TABLE `{$wpdb->prefix}ezinearticles_post_settings` (
	`post_id` BIGINT(20) unsigned NOT NULL,
	`author` VARCHAR(100),
	`category` VARCHAR(100),
	`keywords` VARCHAR(100),
	`summary` TINYINT(1) unsigned NOT NULL DEFAULT '0',
	`resource` TEXT,
	`schedule` DATETIME,
	PRIMARY KEY  (`post_id`),
	KEY `author` (`author`)
	)";

	dbDelta($create_sql);

	if($oldLogs = $wpdb->get_results('SELECT * FROM `wp_ezinearticles`'))
	{
		foreach($oldLogs as $oldLogEntry)
		{
			if($oldLogEntry->post_id && $oldLogEntry->article_id)
				wp_ezinearticles_post_article_log(array("post_id" => $oldLogEntry->post_id, "article_id" => $oldLogEntry->article_id));
		}
		$wpdb->query("DROP TABLE IF EXISTS wp_ezinearticles");
	}
	$connectionMessage = wp_ezinearticles_connection_test();
	if ($connectionMessage != null)
	{
		wp_ezinearticles_log_event($connectionMessage, "activate");
		wp_ezinearticles_message($connectionMessage, 'error');
		//echo($connectionMessage);
		exit();
	}
	wp_ezinearticles_log_event( "Setup at time of activation:\n\n" . wp_ezinearticles_get_setup_info(), 'activate');
}

/**
 * returns null if all is ok, else return an error.
 */
function wp_ezinearticles_connection_test()
{
	$s = new Snoopy();
	$s->read_timeout = 5;
	$s->submit('http://api.ezinearticles.com/api.php');
	$results = print_r($s->results,true);
	if(isset($results) && strlen($results))
	{
		return null;
	}
	else
		return "<b>Could not contact the EzineArticles.com submission interface.  Possible causes include:<br>
		* A firewall may be blocking the server from making outbound requests to http://api.ezinearticles.com/.<br>
		* PHP may not be allowed to open connections on your server.<br>
		* http://api.ezinearticles.com/ may be experiencing downtime.";
}


function wp_ezinearticles_deactivate()
{
	wp_ezinearticles_log_event("Plugin Deactivated", 'deactivate');
}

function wp_ezinearticles_get_setup_info()
{
	$user_info = wp_ezinearticles_current_options();

	$output = array();
	$output[] = "PHP Version: " . PHP_VERSION;
	$output[] = " | Ezinearticles Plugin Version: " . WP_EZINEARTICLES_PLUGIN_VERSION;
	$output[] = " | WordPress Version: " . get_bloginfo('version');
	$output[] = " | WordPress URL: " . get_bloginfo('url');
	$output[] = " | Email: " . $user_info['ea_email'];
	$output[] = " | API Key: ". $user_info['ea_api_key'];

	if(isset($_SERVER['SERVER_ADDR']))
	{
		$output[] = " | Server IP: " . $_SERVER['SERVER_ADDR'];
	}

	if(isset($_SERVER['SCRIPT_FILENAME']))
	{
		$output[] = " | Form: " . $_SERVER['SCRIPT_FILENAME'];
	}

	if(isset($_SERVER['HTTP_USER_AGENT']))
	{
		$output[] = " | Browser: " . $_SERVER['HTTP_USER_AGENT'];
	}

	if(isset($_ENV['HOSTNAME']))
	{
		$output[] = " | Host: " . $_ENV['HOSTNAME'];
	}

	if(function_exists('get_plugins'))
	{
		$plugins =  array();
		$all_plugins = get_plugins();

		if(is_array($all_plugins) && count($all_plugins))
		{
			foreach($all_plugins as $plugin)
			{
				$plugins[] = $plugin['Name'] . ' (' . $plugin['Version'] . ')';
			}
		}

		if(count($plugins))
		{
			$output[] = ' | Plugins Detected: ' . implode('; ', $plugins);
		}
	}

	return implode("\n", $output);
}

//This warns the user if they try to manually install a plugin on the wrong version of wordpress.
function wp_ezinearticles_version_check()
{
	if(PHP_VERSION <= WP_EZINEARTICLES_MIN_PHP_VERSION)
	{
		wp_ezinearticles_message("The EzineArticles WordPress Plugin requires PHP version <strong>".WP_EZINEARTICLES_MIN_PHP_VERSION."</strong> or higher. Your current PHP version is: <strong>".PHP_VERSION."</strong>. <br /><br />Please contact your webhosting provider and ask to update your site to PHP version <strong>".WP_EZINEARTICLES_MIN_PHP_VERSION."</strong> or higher.", 'error');
	}
	if(get_bloginfo('version') <= WP_EZINEARTICLES_MIN_WP_VERSION)
	{
		wp_ezinearticles_message("The EzineArticles WordPress Plugin requires WordPress version <strong>".WP_EZINEARTICLES_MIN_WP_VERSION."</strong> or higher. Your current WordPress version is: <strong>".get_bloginfo('version')."</strong>. <br /><br />Please update your WordPress to <strong>".WP_EZINEARTICLES_MIN_WP_VERSION."</strong> or higher.", 'error');
	}
}

//This logs an error
function wp_ezinearticles_log_event($message, $type = 'error')
{
	global $wpdb;

	$wpdb->insert( $wpdb->prefix.'ezinearticles_diagnostic_log', array('type' => $type, 'details' => $message, 'date' => date('Y-m-d H:i:s')) );
}

//This sends us an email with information required to debug
function wp_ezinearticles_send_debug_email($additional_message = '')
{
	$to = "EzineArticles Wordpress Plugin <wordpress-debugging@ezinearticles.com>";
	$subject = "EzineArticles Wordpress Plugin Debug Report";
	$message = "Message From User:\n\n".trim($additional_message)."\n\n-----------------------\n Current Plugin Setup:\n-----------------------\n\n".wp_ezinearticles_get_setup_info();

	$user_info = wp_ezinearticles_current_options();
	$header = $user_info['ea_email'];

	wp_mail($to, $subject, $message, $header);
}

// This handles the submit logic
function wp_ezinearticles_submit()
{
	$post_id = $_REQUEST['post_ID'];

	if(!$post_id) die();

	global $wpdb;

	$content = strip_shortcodes($_REQUEST['content']);

	$post_id = $_REQUEST['post_ID'];

	$summary = ($_REQUEST['ea_summary'] == 'use_excerpt') ? $_REQUEST['excerpt'] : wp_ezinearticles_get_summary_from_post_content($content);

	if($_REQUEST['ezinearticles_keyword'] == 'use_post_tags')
	{
		$keywords = (!empty($_REQUEST['newtag']['post_tag'])) ? $_REQUEST['newtag']['post_tag'] : $_REQUEST['tax_input']['post_tag'];
	}
	else
	{
		$keywords = trim( strip_tags($_REQUEST['ezinearticles_keyword_text']) );
	}

	$submit['title'] = stripslashes($_REQUEST['post_title']);
	$submit['body']= stripslashes($content);
	$submit['author']= stripslashes($_REQUEST['ea_author']);
	$submit['category']= $_REQUEST['ezinearticles_category'];
	$submit['summary']= stripslashes($summary);
	$submit['keywords']= stripslashes($keywords);
	$submit['signature']= stripslashes($_REQUEST['ea_resourcebox_text']);

	$account_status = wp_ezinearticles_get_option('ea_account_status');
	$account_status = $account_status[0];
	if($account_status['membership_status'] == 'Premium' && $_REQUEST['ea_schedule_switch'])
	{
		$date = explode(':', $_REQUEST['schedule_month']);
		$month = $date[0];
		$year = $date[1];
		$publishDate = $month.'-'.$_REQUEST['schedule_date'].'-'.$year.':'.$_REQUEST['schedule_hour'];
		$submit['publish_date']  = $publishDate;
	}

	$submit['body'] = strip_tags($submit['body'], '<strong><b><p><a><blockquote><ul><li><ol><i><em><xmp><u><br><pre>');

	if( $_REQUEST['ea_do_validate'] )
		$submit['validate_only']= 1;

	if(wp_ezinearticles_is_article($post_id))
	{
		$method = 'account.article.update';
		$submit['article_id'] = wp_ezinearticles_is_article($post_id);
	}
	else
		$method = 'account.article.new';


	if (!isset($_REQUEST['save_only']))
	{
		$submit_result = wp_ezinearticles_post($method, $submit);

		$data = array(
			'what' => 'validate',
			'id' => $post_id,
		);

		if($submit_result['success'])
		{

			if( isset($_REQUEST['ea_do_validate']))
			{
				$data['data'] = 'Validation was successful. You can submit your post to EzineArticles.com now by clicking on "Submit" button.';
			}
			else
			{
				$data['data'] = 'Successfully submitted for review.';
				wp_ezinearticles_post_article_log(array('article_id' => $submit_result['article_id'], 'post_id' => $post_id));
			}

		}
		elseif($submit_result['error'])
		{
			if( isset($_REQUEST['ea_do_validate']) )
			{
				$data['data'] = $submit_result['error'];
				$data['supplemental'] = array('message_type' => 'error');
			}
			else
			{
				$data['data'] = $submit_result['error'];
				$data['supplemental'] = array('message_type' => 'error');
			}
		}
		else
		{
			$data['data'] = 'Error. Could not communicate with EzineArticles.com.';
			$data['supplemental'] = array('message_type' => 'error');
			wp_ezinearticles_log_event("wp_ezinearticles_submit: Could not communicate with EzineArticles.com.");
		}
	}
	if($post_id && !wp_ezinearticles_is_article($post_id))
	{
		$details = array();
		$details['post_id'] = $post_id;
		$details['author']= $_REQUEST['ea_author'];
		$details['category']= $_REQUEST['ezinearticles_category'];
		$details['summary']= ($_REQUEST['ea_summary'] == 'use_excerpt') ? 1 : 0;
		$details['keywords']= $keywords;
		$details['resource']= $_REQUEST['ea_resourcebox_text'];
		$details['schedule']  = (isset($publishDate)) ? "$year-$month-" . $_REQUEST['schedule_date'] . " " . ($_REQUEST['schedule_hour'] % 24) . ":00:00" : "0000-00-00 00:00:00";
		wp_ezinearticles_post_settings_log($details);
	}

	$x = new WP_Ajax_Response($data);
	$x->send();
}

//This determines whether this post has an associated article id
function wp_ezinearticles_is_article($post_id)
{
	global $wpdb;

	$row = $wpdb->get_row("SELECT article_id FROM `{$wpdb->prefix}ezinearticles_posts_to_articles` WHERE post_id='{$post_id}' LIMIT 1");

	if($row && $row->article_id)
		return $row->article_id;
	else
		return false;
}

function wp_ezinearticles_get_unsubmitted_article($post_id)
{
	global $wpdb;

	$row = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}ezinearticles_post_settings` WHERE post_id='{$post_id}' LIMIT 1");

	if($row)
		return $row;
}

//This attempts to get the first few sentences from an article
function wp_ezinearticles_get_summary_from_post_content($content)
{
	preg_match('/^([^.!?]*[\.!?]+){0,2}/', strip_tags($content), $summary);
	return $summary[0];
}

function wp_ezinearticles_post_article_log($details)
{
	global $wpdb;

	if(!wp_ezinearticles_is_article($details['post_id']))
		$wpdb->insert($wpdb->prefix.'ezinearticles_posts_to_articles', array('post_id' => $details['post_id'], 'article_id' => $details['article_id']));

	$wpdb->query("DELETE FROM {$wpdb->prefix}ezinearticles_post_settings WHERE post_id = '" . intval($details['post_id']) . "'");
}

function wp_ezinearticles_post_settings_log($details)
{
	global $wpdb;
	if(wp_ezinearticles_get_unsubmitted_article($details['post_id']))
		$wpdb->update($wpdb->prefix.'ezinearticles_post_settings', array('author' => $details['author'], 'category' => $details['category'], 'keywords' => $details['keywords'], 'summary' => $details['summary'], 'resource' => $details['resource'], 'schedule' => $details['schedule'] ), array('post_id' => $details['post_id']));
	else
		$wpdb->insert($wpdb->prefix.'ezinearticles_post_settings', array('post_id' => $details['post_id'], 'author' => $details['author'], 'category' => $details['category'], 'keywords' => $details['keywords'], 'summary' => $details['summary'], 'resource' => $details['resource'], 'schedule' => $details['schedule'] ));
}

// This displays the box where you edit an articles settings
function wp_ezinearticles_post_meta_box_view($ea_account_status, $error, $article)
{
	?>
	<div id="ea-publish-wrap">
<?php

if(!$ea_account_status)
{
	if(isset($error))
	{
		?>
	<div class="misc-pub-section misc-pub-section-last">
		<p><b><?php _e('Please verify your EzineArticles account information under the <a href="admin.php?page=wp_ezinearticles_account">Account tab</a>.') ?></b></p>
		<p><?php _e($error) ?></p>
	</div>
		<?php
	}
	else
	{
		?>
	<div class="misc-pub-section misc-pub-section-last">
		<p><b><?php _e('Sorry, there was an error.') ?></b></p>
		<p><?php _e('Please verify your EzineArticles account information. <a href="admin.php?page=wp_ezinearticles_account">Go to Account tab</a>') ?></p>
	</div>
		<?php
	}
	?>
</div>
	<?php
	return false;
}
?>

<div class="misc-pub-section">
	<span><?php _e('Category') ?>:</span><br />
	<?php wp_ezinearticles_print_category_select($article); ?>
</div>

<div class="misc-pub-section">
	<span><?php _e('Author') ?>:</span><br />
	<?php wp_ezinearticles_get_author_select($article); ?>
</div>

<div class="misc-pub-section">
	<span><?php _e('Resource Box') ?>:</span>
	<br/><select name="ea_resourcebox" id="ea-resourcebox"></select>
	<a id="ea-resourcebox-options-edit" href="#ea-resourcebox-options"><?php _e('Customize') ?></a>
	<div id="ea-resourcebox-options-wrap" style="display:none;">
		<p class="howto"><?php _e('Enter Your Custom Resource Here') ?>:</p>
		<textarea name="ea_resourcebox_text" id="ea-resourcebox-text"><?php
			if(isset($article))
			{
				_e( stripslashes($article->resource) );
			}
		?></textarea><br />
		<a id="ea-resourcebox-options-cancel" href="#"><?php _e('Close') ?></a>
	</div>
</div>

<div class="misc-pub-section">
	<span><?php _e('Summary') ?>:</span> <span id="ea-summary-options-display"><?php _e('First 2 Sentences of Post') ?></span> <a id="ea-summary-options-edit" href="#ea-summary-options"><?php _e('Edit') ?></a>
	<div id="ea-summary-options-wrap" style="display:none">
		<input type="radio" id="ea-summary-excerpt" name="ea_summary" value="use_excerpt" <?php if(isset($article) && $article->summary == 1) echo 'checked="checked"' ?> />
		<label for="ea-summary-excerpt"><?php _e('Use Excerpt') ?></label><br>
		<input type="radio" id="ea-summary-first" name="ea_summary" value="use_first" <?php if(isset($article) && $article->summary == 0 || !isset($article)) echo 'checked="checked"' ?> />
		<label for="ea-summary-first"><?php _e('First 2 Sentences of Post') ?></label>
		<p>
			<a id="ea-summary-options-accept" class="button" href="#"><?php _e('OK') ?></a>
			<a id="ea-summary-options-cancel" href="#"><?php _e('Cancel') ?></a>
		</p>
	</div>
</div>

<div class="misc-pub-section">
	<span><?php _e('Keywords') ?>:</span> <span id="ezinearticles-keyword-options-display"><?php _e('Use Post Tags') ?></span> <a id="ezinearticles-keyword-options-edit" href="#ezinearticles-keyword-options"><?php _e('Edit') ?></a>

	<div id="ezinearticles-keyword-options-wrap" style="display:none">
		<input type="radio" id="ea-keywords-post-tags" name="ezinearticles_keyword" value="use_post_tags" <?php if(isset($article) && empty($article->keywords) || !isset($article)) echo 'checked="checked"'; ?>/>
		<label for="ea-keywords-post-tags"><?php _e('Use Post Tags') ?></label><br>
		<input type="radio" id="ea-keywords-custom" name="ezinearticles_keyword" value="use_custom" <?php if(isset($article) && !empty($article->keywords)) echo 'checked="checked"'; ?>/>
		<label for="ea-keywords-custom"><?php _e('Use Custom Keywords') ?></label>
		<br>
		<div id="ezinearticles-custom-keywords-wrap" <?php if(isset($article) && empty($article->keywords) || !isset($article)) echo 'style="display:none;"'; ?>>
			<?php _e('Enter Keywords (comma separated)') ?>:
			<input type="text" name="ezinearticles_keyword_text" id="ezinearticles-keyword-text" value="<?php if(isset($article)) echo stripslashes($article->keywords); ?>"><br>
		</div>
		<p>
			<a id="ezinearticles-keyword-options-accept" class="button" href="#"><?php _e('OK') ?></a>
			<a id="ezinearticles-keyword-options-cancel" href="#"><?php _e('Cancel') ?></a>
		</p>
	</div>
</div>
<?php
$account_status = wp_ezinearticles_get_option('ea_account_status');
$account_status = $account_status[0];
if(isset($account_status['membership_status']) &&($account_status['membership_status'] == 'Premium')) : ?>
<div class="misc-pub-section"><input type="checkbox"
		name="ea_schedule_switch" id="ea-schedule-switch"> <label
		for="ea-schedule-switch"><?php _e('Schedule Release of this Post') ?></label>
	<div id="ea-schedule-options-wrap" style="display: none">
		<select name="schedule_month" id="ea-schedule-month">
		<?php
		$month = date("n");
		$year = date("Y");
		//Only show next three months for scheduling
		for($i = 0; $i <= 3; $i++)
		{
			if($month > 12)
			{
				$month = 1;
				$year++;
			}
			$value = $month.':'.$year;
			$date = date('M', strtotime("+".$i." month"));
			?>
			<option value="<?php echo $value; ?>"><?php _e($date) ?></option>
			<?php $month++;
		} ?>
		</select>
		<input
			type="text" name="schedule_date" id="ea-schedule-day" size="2"
			maxlength="2" value="<?php echo date('j', strtotime("tomorrow")); ?>">,
		<span id="ea-schedule-year"><?php echo date('Y'); ?></span>
		@ <select name="schedule_hour" id="schedule-hour-list">
			<option value="24"><?php _e('12 AM') ?></option>
			<option value="1"><?php _e('1 AM') ?></option>
			<option value="2"><?php _e('2 AM') ?></option>
			<option value="3"><?php _e('3 AM') ?></option>
			<option value="4"><?php _e('4 AM') ?></option>
			<option value="5"><?php _e('5 AM') ?></option>
			<option value="6"><?php _e('6 AM') ?></option>
			<option value="7"><?php _e('7 AM') ?></option>
			<option selected="selected" value="8"><?php _e('8 AM') ?></option>
			<option value="9"><?php _e('9 AM') ?></option>
			<option value="10"><?php _e('10 AM') ?></option>
			<option value="11"><?php _e('11 AM') ?></option>
			<option value="12"><?php _e('12 PM') ?></option>
			<option value="13"><?php _e('1 PM') ?></option>
			<option value="14"><?php _e('2 PM') ?></option>
			<option value="15"><?php _e('3 PM') ?></option>
			<option value="16"><?php _e('4 PM') ?></option>
			<option value="17"><?php _e('5 PM') ?></option>
			<option value="18"><?php _e('6 PM') ?></option>
			<option value="19"><?php _e('7 PM') ?></option>
			<option value="20"><?php _e('8 PM') ?></option>
			<option value="21"><?php _e('9 PM') ?></option>
			<option value="22"><?php _e('10 PM') ?></option>
			<option value="23"><?php _e('11 PM') ?></option>
		</select>
	</div>
	<?php if(isset($article->schedule) && $article->schedule != '0000-00-00 00:00:00') : ?>
		<input type="hidden" id="ezinearticles-schedule-old" value="<?php echo date('F j, Y G:i', strtotime($article->schedule)); ?>:00" />
	<?php endif; ?>
</div>
<?php else : ?>
<div class="misc-pub-section">
	<?php _e('Scheduled Release') ?>
	<a href="http://EzineArticles.com/premium/" title="Premium Feature: Schedule release of this post. Click to find out how to become a Premium Member." target="_blank"><img src="<?php echo WP_EZINEARTICLES_PLUGIN_PATH?>/img/premium.png" border="0" align="center" alt="Premium Feature: Schedule Release."></a>
	<div align="center"><small><a href="http://EzineArticles.com/premium/" title="Click to find out how to become a Premium Member." target="_blank"><?php _e('This feature is available to Premium Members') ?></a></small></div>
</div>
<?php endif; ?>
<div class="misc-pub-section-last">
	<div id="ea-actions">
		<input id="ea-validate-post" class="button button-highlighted" type="button" value="Validate" name="ea_do_validate" title="Checks the article for rejectable content">
		<div id="ea-publish-post-wrap">
			<img alt="" style="visibility: hidden;" id="ea-ajax-loading" src="images/wpspin_light.gif">&nbsp;
			<input id="ea-publish-post" class="button-primary" type="button" value="<?php if(isset($article->date_submitted) && $article->date_submitted != '0000-00-00 00:00:00') echo 'Re-Submit'; else  echo 'Submit'; ?>" name="ea_do_publish" title="Submits to EzineArticles.com, does not publish to WordPress">
		</div>
		<div style="clear:both;"></div>
	</div>
</div>

</div>
	<?php
}

function wp_ezinearticles_css()
{
	?>
<style type="text/css">
#ea-actions { padding:6px;clear:both; }
#ea-resourcebox-text { width:100%;height:100px; }
#ea-publish-switch-wrap { padding:5px; cursor:pointer; }
#ea-summary-options-wrap { margin-top: 3px; }
#ea-summary-options-display, #ezinearticles-keyword-options-display, #ea-link-display { font-weight:bold; }
#ezinearticles-keyword-options-wrap, #ezinearticles-custom-keywords-wrap, #ezinearticles-send-event-log-details-wrap{margin:4px 0 4px 4px}
#ezinearticles-send-event-log-details-wrap{margin-bottom:10px}
#ezinearticles-keyword-text { width:100% }
#ea-link-display{display:block;margin:6px 0 0 10px}
#ea-validate-post { float:left; }
#ea-publish-post-wrap { float:right;line-height:23px;text-align:right; }
#ea-ajax-loading { vertical-align:middle; }
.ea-premium { color: #CC0000; font-weight:bold;}
</style>
	<?php
}

function wp_ezinearticles_js()
{
	?>
<script type="text/javascript">
	jQuery(document).ready(function($) {

		var $old_summary_option = null;
		$('#ea-summary-options-edit').click(function() {
			$old_summary_option = $('#ea-summary-options-wrap input:checked');
			$('#ea-summary-options-wrap').slideDown("normal");
			$(this).hide();
			return false;
		});

		$('#ea-resourcebox-options-edit').click(function(){

			if ($('#ea-resourcebox-options-wrap').is(":hidden")){
				$('#ea-resourcebox-options-wrap').slideDown("normal");
				$(this).hide();
			}
			return false;
		});

		if ($('#ea-resourcebox-text').val() != '')
		{
			$('#ea-resourcebox-options-edit').click();
			var no_update_resource = true;
		}

		$('#ea-author').change(function(){
			var data = {
				action: 'ezinearticles_author_resources',
				author: $(this).val()
			};

			$.ajax({
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				data: data,
				dataType: 'xml',
				type:'post',
				success: function(response)
				{
					var response = $(response);
					if (response.find('author_resources')[0])
					{
						var resources = response.find('resource'), html = '';

						resources.each(function(){
							html += '<option value="'+$(this).find('body').text()+'">'+$(this).find('name').text()+'</option>';
						});

						if (html == '')
						{
							$('#ea-resourcebox').hide();
							$('#ea-resourcebox-options-edit').click();
						}
						else
						{
							$('#ea-resourcebox').html(html).show();
						}

						update_resource();
					}
				}
			});
		}).trigger('change');

		var $old_keyword_option = null;
		$('#ezinearticles-keyword-options-edit').click(function(){
			$old_keyword_option = $('#ezinearticles-keyword-options-wrap input:checked');
			if ($('#ezinearticles-keyword-options-wrap').is(":hidden")){
				$('#ezinearticles-keyword-options-wrap').slideDown("normal");
				$(this).hide();
			}
			return false;
		});

		$('#ea-summary-options-accept').click(function(){
			$('#ea-summary-options-display').text($('#ea-summary-options-wrap input:checked').next().text());
			$('#ea-summary-options-wrap').slideUp("normal");
			$('#ea-summary-options-edit').show();
			return false;
		}).trigger('click');

		$('#ezinearticles-keyword-options-accept').click(function(){
			$('#ezinearticles-keyword-options-display').text($('#ezinearticles-keyword-options-wrap input:checked').next().text());
			$('#ezinearticles-keyword-options-wrap').slideUp("normal");
			$('#ezinearticles-keyword-options-edit').show();
			return false;
		}).trigger('click');

		$('#ea-keywords-custom').click(function(){
			if ($(this).is(':checked'))
			{
				$('#ezinearticles-custom-keywords-wrap').show();
			}
		})
		$('#ea-keywords-post-tags').click(function(){
			if ($(this).is(':checked'))
			{
				$('#ezinearticles-custom-keywords-wrap').hide();
			}
		});

		$('#ea-resourcebox').change(function(){
			update_resource();
		});

		$('#ea-summary-options-cancel').click(function() {
			$('#ea-summary-options-wrap').slideUp("normal", function(){
				$old_summary_option.attr('checked', 'checked');
			});
			$('#ea-summary-options-edit').show();
			return false;
		});

		$('#ezinearticles-keyword-options-cancel').click(function() {
			$('#ezinearticles-keyword-options-wrap').slideUp("normal", function(){
				$old_keyword_option.attr('checked', 'checked').click();
			});
			$('#ezinearticles-keyword-options-edit').show();
			return false;
		});

		$('#ea-resourcebox-options-cancel').click(function(){
			$('#ea-resourcebox-options-wrap').slideUp("normal");
			$('#ea-resourcebox-options-edit').show();
			return false;
		});

		$('#ea-keywords-options-add').click(function() {

            $("#new-tag-post_tag").focus();
			$("#new-tag-post_tag").animate({backgroundColor: '#FFB6C1'},500).animate({backgroundColor: '#ffffff'},500).animate({backgroundColor: '#FFB6C1'},500).animate({backgroundColor: '#ffffff'},500);

		});

		$('#ea-publish-post').click(function() {
			submit_form();
		});

		$('#ea-validate-post').click(function(){
			submit_form(1);
		});


		$('#ea-schedule-switch').click(function(){
			toggle_schedule();
		});

		if ($('#ezinearticles-schedule-old')[0])
		{
			var date = new Date($('#ezinearticles-schedule-old').val());
			$('#ea-schedule-day').val(date.getDate());
			$('#ea-schedule-month').val((date.getMonth() + 1) + ':' + date.getFullYear());
			$('#schedule-hour-list').val(date.getHours());
			$('#ea-schedule-switch').attr('checked', 'checked');
		}

		$('#post').submit(function(){
			submit_form(2);
		});

		$("#ezinearticles-send-event-log-detail-link").click(function() {
			if($("#ezinearticles-send-event-log-detail-link").html() == 'Show Details') {
				$("#ezinearticles-send-event-log-details").slideDown();
				$("#ezinearticles-send-event-log-detail-link").html('Hide Details');
			}else {
				$("#ezinearticles-send-event-log-details").slideUp();
				$("#ezinearticles-send-event-log-detail-link").html('Show Details');
			}
			return false;
		});

		toggle_schedule();

		// 0 = submit, 1 = validate only, 2 = save only
		function submit_form(flag)
		{
			if ($('#post_ID').attr('name') == 'temp_ID')
			{
				show_message('Please save a draft of your article before continuing.', 'error');
				return;
			}

			if (flag != 2)
				autosave();

			toggle_buttons();

			if (!flag)
				flag = 0;

			var data = $('#post').serialize();

			var pattern = /action=([^&]*)/i;

			if (data.match(pattern) == null)
			{
				data += '&action=ezinearticles_submit';
			}
			else
			{
				data = data.replace(pattern, 'action=ezinearticles_submit');
			}

			data += '&cookie='+encodeURIComponent(document.cookie);

			if (flag === 2)
				data += '&save_only=1';
			else if (flag === 1)
				data += '&ea_do_validate=1';

			$.ajax({
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				data: data,
				dataType: 'xml',
				type:'post',
				async: (flag === 2) ? false : true,
				success: function(response)
				{
					if (flag === 2) return;
					response = $(response);
					if (!response.find('response_data')[0])
					{
						show_message('Error. Could not communicate with EzineArticles.com.', 'error');
					}
					else
					{
						show_message(response.find('response_data').text(), response.find('message_type').text());
					}
				},
				error: function()
				{
					if (flag === 2) return;
					show_message('Error. Could not communicate with EzineArticles.com.', 'error');
				},
				complete: function()
				{
					toggle_buttons(true);
				}
			});
		}

		function toggle_schedule()
		{
			if ($('#ea-schedule-switch').is(":checked")) {
				$('#ea-schedule-options-wrap').slideDown("normal");
				$(this).parent().find('label').css('fontWeight', 'bold');
			}
			else
			{
				$('#ea-schedule-options-wrap').slideUp("normal");
				$(this).next().css('fontWeight', 'normal');
			}
		}

		function show_message(message, type)
		{
			var $message_alt = $('#ezinearticles-message-alt').fadeTo('medium', 0);

			message = '<p>' + message + '</p>';

			if (!type)
				type = 'updated';

			if ($message_alt[0])
			{
				$message_alt.html(message).attr('className', type);
			}
			else
			{
				$message_alt = $('<div id="ezinearticles-message-alt"></div>').html(message).addClass(type);
				$message_alt.appendTo('#wp-ezinearticles .misc-pub-section-last');
			}
			$message_alt.fadeTo('medium', 1);
		}

		var $buttons = $('#ea-publish-post, #ea-validate-post');
		var $loading = $('#ea-ajax-loading');
		function toggle_buttons(enable)
		{
			if (enable)
			{
				$buttons.attr('disabled', '');
				$loading.css('visibility', 'hidden');
			}
			else
			{
				$buttons.attr('disabled', 'disabled');
				$loading.css('visibility', 'visible');
			}
		}

		function update_resource()
		{
			if (no_update_resource)
			{
				no_update_resource = false;
				return;
			}
			var val = ($('#ea-resourcebox').is(':visible')) ? $('#ea-resourcebox option:selected').val() : '';
			$('#ea-resourcebox-text').val(val);
		}
	});
</script>
	<?php
}

//These functions communicate with EzineArticles api
function wp_ezinearticles_get($method, $with = null)
{
	$that = array_merge( array('search'=>$method), (array)$with, wp_ezinearticles_common_vars($method) );
	array_walk($that, create_function('&$i,$k','$i="$k=$i";'));
	$get_query =  implode('&', $that);
	$s = new Snoopy();
	$s->fetch('http://api.ezinearticles.com/api.php?' . $get_query);
	$results = unserialize($s->results);
	return $results[$method];
}

/* a different post method for the posts that are searching the api */
function wp_ezinearticles_post_search($method, $with = null, $unfiltered = false)
{
	// post as array
	$post_query = array_merge( array('search'=>$method), (array)$with, wp_ezinearticles_common_vars($method));
	$s = new Snoopy();
	$s->submit('http://api.ezinearticles.com/api.php?', $post_query);
	$results = unserialize($s->results);
	if ($unfiltered) return $results;
	return (isset($results[$method])) ? $results[$method] : false;
}

function wp_ezinearticles_post($method, $with = null)
{
	// post as array
	$post_query = array_merge( array('submit'=>$method), (array)$with, wp_ezinearticles_common_vars($method));
	$s = new Snoopy();
	$s->submit('http://api.ezinearticles.com/api.php?', $post_query);
	$results = unserialize($s->results);
	return ($results[$method][0]) ? $results[$method][0] : $results;
}

function wp_ezinearticles_common_vars($method)
{
	$common_vars = array();
	$required = array('account.status', 'account.article.new', 'account.article.update', 'account.article.view');

	if(in_array($method, $required))
	{
		$common_vars['email'] = wp_ezinearticles_get_option('ea_email');
		$common_vars['pass'] = wp_ezinearticles_get_option('ea_password');
	}

	$common_vars['response_format'] = 'phpserial';
	$common_vars['data_amount'] = 'extended';
	$common_vars['key'] = wp_ezinearticles_get_option('ea_api_key');

	return $common_vars;
}

?>
