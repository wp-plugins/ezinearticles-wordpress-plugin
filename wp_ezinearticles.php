<?php
/*
Plugin Name: WP EzineArticles
Plugin URI: http://EzineArticles.com/
Description: The EzineArticles WordPress Plugin allows you to submit your high quality, original WordPress posts to EzineArticles.com, as well as monitor their review status right from the WordPress administration interface!
Version: 1.5.2
Author: EzineArticles.com
Author URI: http://EzineArticles.com/
*/


define('WP_EA', 'WP EzineArticles');
define('EA', 'EzineArticles');
define('WP_EA_FOLDER', dirname(plugin_basename(__FILE__)), TRUE);
define('EA_AJAX', WP_CONTENT_URL . '/plugins/' . WP_EA_FOLDER . '/inc/ajax.php');
define('MIN_PHP_VERSION', '5.1');
define('MIN_WP_VERSION', '2.7');

if( is_admin())
{
	register_activation_hook( __FILE__, 'ea_install');

	include_once('inc/ea_remote.class.inc.php');

	add_action('admin_head', 'printAdminJSAndCSS');
	add_action('admin_menu', 'addMenu');
	add_action('admin_menu', 'addMetaBox');
	add_action('admin_menu', 'ea_alerts');

}


function addMetaBox()
{
	if(function_exists('add_meta_box'))
	{
		add_meta_box( 'id', WP_EA, 'eaPostMetaBox', 'post', 'side', 'high');
	}

}

function addMenu()
{
	add_menu_page(EA,  EA, 6, 'wp_ezinearticles', 'wp_ezinearticles',  WP_CONTENT_URL . '/plugins/' . WP_EA_FOLDER . '/img/ea.png');
	add_submenu_page('wp_ezinearticles', __('', WP_EA), __('Articles', WP_EA), 6, 'wp_ezinearticles', 'wp_ea_articles_view');
	add_submenu_page('wp_ezinearticles', __('', WP_EA), __('Options', WP_EA), 6, 'wp_ezinearticles_options', 'wp_ea_options_view');
	add_submenu_page('wp_ezinearticles', __('', WP_EA), __('Account', WP_EA), 6, 'wp_ezinearticles_account', 'wp_ea_account_view');

}




function ea_get_option($option_name)
{
	$ea_options = get_option('ezinearticles_options');

	if($ea_options)
	{
		return $ea_options[$option_name];
	}

	return false;

}

function wp_ezinearticles()
{
	global $wpdb;

	?>
	<div class="wrap">

		<h2><?php echo WP_EA?></h2>
		<p>This page is displaying most recent blog posts you have submitted to EzineArticles.com.</p>
		<table class="widefat post fixed" cellpadding="0">
		<thead>
		<tr>
		<th>Post</th>
		<th>Category</th>
		<th>Status</th>
		<th>Submitted</th>
		</tr>
		</thead>
		<tbody>
		<?php

			$results = $wpdb->get_results("SELECT * FROM `wp_ezinearticles` WHERE article_id!=0 ORDER BY date DESC");

			if($results)
			{
				foreach($results as $result)
				{
					$article_results = eaRemote::get('account.article.view', array('article_id' => $result->article_id));

					if($article_results)
					{
						foreach($article_results as $article_result)
						{
							$article = (object)$article_result['article'];

							$edit_link = "<a class='row-title' href='post.php?action=edit&amp&post={$result->post_id}'>{$article->title}</a>";

							$category = preg_replace('/[^a-zA-Z]/',' ', $article->category);

							if($article->subcategory)
								$category.= '&rsaquo;' . preg_replace('/[^a-zA-Z]/',' ', $article->subcategory);


							?>
							<tr>
							<td class="post-title"><strong><?php echo $edit_link?></strong></td>
							<td><?php echo $category?></td>
							<td><?php echo ucwords($article->status)?></td>
							<td><?php echo date('F j, Y, g:i a', strtotime($result->date))?></td>
							</tr>
							<?php

						}

					}


				}

			}

		?>
		</tbody>
	</table>
	</div>


	<?php
}

function wp_ea_account_view()
{

	if($_POST['refresh_account_status'])
	{

		$ea_options['ea_categories'] = eaRemote::get('categories');

		$ea_options['ea_account_status'] = eaRemote::get('account.status');

		$ea_options = array_merge(ea_current_options(), $ea_options);

		update_option('ezinearticles_options', $ea_options);
	}



	?>
	<div class="wrap">
	<h2><?php echo WP_EA?> - Account</h2>
	<?php

		if($account_status = ea_get_option('ea_account_status'))
		{

			$account_status = $account_status[0];
			?>
			<form action="" method="post">
			<h3><?php _e('Your EzineArticles Account Status', 'ea') ?></h3>
			<table width="100%" cellpadding="2" cellspacing="5" class="editform">
			<tr>
				<td align="left" valign="top" width="25%" nowrap><?php _e('Author Names:', 'ea')?></td>
				<td><?php echo getAuthorSelect() ?></td>
			</tr>

			<tr>
				<td align="left" valign="top" width="25%" nowrap><?php _e('Membership Level:', 'ea')?></td>
				<td><?php echo $account_status['membership_level']; ?></td>
			</tr>

			<tr>
				<td align="left" valign="top" widtd="25%" nowrap><?php _e('Submissions Left:', 'ea')?></td>
				<td><?php echo $account_status['submissions_left']; ?></td>
			</tr>

			</table>

			<div class="submit">
				<input type="submit" name="refresh_account_status" value="<?php _e('Refresh account status', 'ea')?>">
			</div>

			<?php
		}
		else
		{

			?>
			<p><b>Could not connect to EzineArticles.com</b></p>
			<p>Please verify your EzineArticles API Key, Username and Password are correct.</p>
			<p><b><a href="admin.php?page=wp_ezinearticles_options">Go to Options</a></b></p>
			<?php


		}

	?>
	</div>
	<?php



}

function wp_ea_options_view()
{
	if(isset($_POST['save_settings']))
	{


		$ea_options['ea_api_key'] = $_POST['ea_api_key'];
		$ea_options['ea_email'] = $_POST['ea_email'];
		$ea_options['ea_password'] = $_POST['ea_password'];

		$ea_options['ea_categories'] = eaRemote::get('categories');
		$ea_options['ea_account_status'] = eaRemote::get('account.status', true);

		$ea_options = array_merge(ea_current_options(), $ea_options);

		update_option('ezinearticles_options', $ea_options);

		ea_message('Settings saved.');

	}
	elseif(isset($_POST['reset_settings']))
	{

		update_option('ezinearticles_options', ea_current_options());
	}

	?>

	<div class="wrap">

		<h2><?php echo WP_EA?> - Options</h2>


	<form method="post">

			<h3><?php _e('General settings', 'ea') ?></h3>
			<table width="100%" cellpadding="2" cellspacing="5" class="editform">
			<tr>
				<td align="left" valign="top" width="25%" nowrap><?php _e('Your EzineArticles API Key:', 'ea')?></td>
				<td><input type="text" name="ea_api_key" id="ea_api_key" size="55" value="<?php echo ea_get_option('ea_api_key')?>" tabindex="1">
				<small>
				<br /> In order to use Publish on EzineArticles plugin you must have
				<br />your unique EzineArticles API Key. <a target="_blank" href="http://api.ezinearticles.com/?get_api_key">Click Here</a> if you do not have one yet.
				</small>
				</td>
			</tr>

			<tr>
				<td align="left" valign="top" width="25%" nowrap><?php _e('Your EzineArticles Username:', 'ea')?></td>
				<td><input type="text" name="ea_email" id="ea_email" size="30" value="<?php echo ea_get_option('ea_email')?>" tabindex="2">
				<small>
				<br />Enter your EzineArticles Member's Username(Email). If you are not yet <br />a member of EzineArticles,
				<a target="_blank" href="http://ezinearticles.com/submit/">Click Here</a> for your FREE  Basic Membership Account.
				</small>
				</td>
			</tr>

			<tr>
				<td align="left" valign="top" width="25%" nowrap><?php _e('Your EzineArticles Password:', 'ea')?></td>
				<td><input type="password" name="ea_password" id="ea_password" size="30" value="<?php echo ea_get_option('ea_password')?>" tabindex="3">
				<small>
				<br />Enter your EzineArticles Member's Password associated with your username.
				</small>
				</td>
			</tr>
			<table>

			<div class="submit">
				<input type="submit" name="save_settings" value="<?php _e('Save Settings', 'ea')?>">
				<input type="submit" name="reset_settings" value="<?php _e('Reset Settings', 'ea')?>">
			</div>

			</table>


		</form>

	</div>

	<?php
}


function eaPostMetaBox()
{
	global $post;

	ea_alerts();


  ?>

	<div id="ea-publish-switch-wrap">
		<input type="checkbox" name="ea_publish_switch" id="ea-publish-switch" onClick="javascript:switchTrigger()">
			<span id="ea-publish-switch-display" onClick="javascript:switchTrigger()">
				Publish Post on EzineArticles
			</span>
	</div>

	<div id="ea-publish-wrap" style="display:none">


		<?php
		if( !ea_get_option('ea_account_status'))
		{
			?><div class="misc-pub-section"><p><b>Sorry, there was an error.</b></p> <p>Please verify your EzineArticles API Key, Username and Password are correct.</p></div></div><?php
			return false;
		}
		?>

				<div class="misc-pub-section">
					<span>Category:</span><br />
					<?php getCategorySelect() ?>
				</div>

				<div class="misc-pub-section">
					<span>Author:</span><br />
					<?php getAuthorSelect() ?>
				</div>

				<div class="misc-pub-section">
					<span>Resource Box:</span>
					<?php $hasResourceBoxes = getResourceBoxSelect() ?><a id="ea-resourcebox-options-edit" href="#ea-resourcebox-options">Edit</a>
					<div id="ea-resourcebox-options-wrap" style="display:none">
						<?php if ($hasResourceBoxes):?>
						Resource Body:<br />
						<?php else:?>
						<p class="howto">Enter Your New Resource Box Here:</p>
						<?php endif;?>
						<textarea name="ea_resourcebox_text" id="ea-resourcebox-text"></textarea><br />
						<a id="ea-resourcebox-options-cancel" href="#">Close</a>
					</div>
				</div>


				<div class="misc-pub-section">
					<span>Summary:</span> <span id="ea-summary-options-display">First 2 Sentences of Post</span> <a id="ea-summary-options-edit" href="#ea-summary-options">Edit</a>
					<div id="ea-summary-options-wrap" style="display:none">
						<select name="ea_summary" id="ea-summary">
							<option value="use_excerpt">Use Excerpt</option>
							<option value="use_first" selected="1">First 2 Sentences of Post</option>
						</select>
						<a id="ea-summary-options-cancel" href="#">Cancel</a>
					</div>
				</div>

				<div class="misc-pub-section">
					<span>Keywords:</span> <span id="ea-keywords-options-display">Use Post Tags</span> <a id="ea-keywords-options-add" href="#ea-keywords-options">Add</a>
				</div>

                <div class="misc-pub-section misc-pub-section-last">
	                <div id="minor-publishing-actions">
	                    <input id="ea-validate-post" class="button button-highlighted" type="button" value="Validate" name="ea_do_validate">
	                    <input id="ea-publish-post" class="button-primary" type="button" value="Submit" name="ea_do_publish">
					</div>
				</div>
				<div class="misc-pub-section clear center">Please save as a draft before validating.</div>


	</div>


  <?php

}

function ea_message($message, $class = 'updated')
{
	global $post;
	?>
	<div id="message" class="<?php echo $class?>">
		<p><?php if($post) echo '<b>'.WP_EA.'</b>:'?> <?php echo $message?></p>
	</div>
	<?php

}


function ea_alerts()
{
	global $wpdb, $post;


	if($post->ID)
	{
		$row = $wpdb->get_row("SELECT * FROM `wp_ezinearticles` WHERE post_id={$post->ID} ORDER BY date DESC LIMIT 1");
		if(!$row) return;

		if($row->viewed==0)
		{
			ea_message($row->details);
			ea_alert_viewed($row->id);

		}
		?><script>switchTrigger();</script><?php

	}

}

function ea_alert_viewed($id)
{
	global $wpdb;

	$wpdb->update("wp_ezinearticles", array('viewed' => 1), array('id' => $id));
}

function getAuthorSelect()
{
	$account_status = ea_get_option('ea_account_status');
	$account_status = $account_status[0];

	if( !isset($account_status['account_author'])) return false;

	?><select name="ea_author" id="ea-author"><?php

	?><option value="<?php echo $account_status['account_author']?>"><?php echo $account_status['account_author']?></option><?php

	foreach($account_status['alternate']['author'] as $alternate_author)
	{
		?><option value="<?php echo $alternate_author?>"><?php echo $alternate_author?></option><?php
	}


	?></select><?php

}

function getResourceBoxSelect()
{
	$account_status = ea_get_option('ea_account_status');

	if( !isset($account_status[0]['resource'])) return false;

	?><br/><select name="ea_resourcebox" id="ea-resourcebox"><?php
	foreach($account_status as $index => $temp_status)
	{
		foreach($temp_status as $resource_box_key => $resource_box_value)
		{
			if($resource_box_key == "resource")
			{
				?><option value="<?php echo $resource_box_value['body']?>"><?php echo $resource_box_value['name']?></option><?php
			}
		}
	}
	?></select><?php
	return true;
}

function getCategorySelect()
{
	$ea_categories = ea_get_option('ea_categories');


	if( !isset($ea_categories)) return false;

	?><select name="ea_category" id="ea-category"><?php

		foreach($ea_categories as $category)
		{

			?><option value="<?php echo $category['category']['name']; ?>" style="font-weight:bold;"><?php echo $category['category']['name']; ?></option><?

			foreach($category['category']['subcategory'] as $subcategory)
			{
				if(!isset($subcategory)) continue;

				?><option value="<?php echo $category['category']['name'].':'.$subcategory; ?>">&rsaquo; <?php echo $subcategory; ?></option><?
			}
		}

	?></select><?php

}



function ea_current_options()
{
	$options = array();

	$options['ea_email'] = ea_get_option('ea_email');
	$options['ea_password'] = ea_get_option('ea_password');


	return $options;

}

function printAdminJSAndCSS()
{
	printAdminJS();
	printAdminCSS();
}

function printAdminJS()
{
 ?>
<script type="text/javascript">

var $ = jQuery.noConflict();

$(document).ready(function() {

		var url = '<?php echo EA_AJAX?>';

		$('#ea-resourcebox-text').text($('#ea-resourcebox option:selected').val());

		$('#ea-summary-options-edit').click(function() {

			if ($('#ea-summary-options-wrap').is(":hidden")) {
				$('#ea-summary-options-wrap').slideDown("normal");
				$(this).hide();
			}
			return false;
		});

		$('#ea-resourcebox-options-edit').click(function(){

			if ($('#ea-resourcebox-options-wrap').is(":hidden")){
				$('#ea-resourcebox-options-wrap').slideDown("normal");
				$(this).hide();
			}
			return false;
		});

		$('#ea-summary').change(function(){

			$('#ea-summary option:selected').val();

			$('#ea-summary-options-display').text($('#ea-summary option:selected').text());

			$('#ea-summary-options-wrap').slideUp("normal");
			$('#ea-summary-options-edit').show();

			return false;

		});

		$('#ea-resourcebox').change(function(){
			$('#ea-resourcebox-text').text($('#ea-resourcebox option:selected').val());
			return false;
		});

		$('#ea-summary-options-cancel').click(function() {
			$('#ea-summary-options-wrap').slideUp("normal");
			$('#ea-summary-options-edit').show();
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
			autosave();
			$.ajax({ url: url, data: $('#post').serialize(), success: function(response)
			{
				$(location).attr('href', 'post.php?action=edit&post=' + $('#post_ID').val());
			}
			});
		});

		$('#ea-validate-post').click(function() {
			autosave();
			$.ajax({ url: url, data: $('#post').serialize() + '&ea_do_validate=1', success: function(response)
			{
				$(location).attr('href', 'post.php?action=edit&post=' + $('#post_ID').val());
			}
			});
		});


});

function switchTrigger()
{
	$(document).ready(function() {
		if ($('#ea-publish-wrap').is(":hidden")) {

			$('#ea-publish-wrap').slideDown("normal");

			$('#ea-publish-switch').attr('checked', true);
			$('#ea-publish-switch-display').css('fontWeight', 'bold');

		}
		else
		{

			$('#ea-publish-wrap').slideUp("normal");
			$('#ea-publish-switch').attr('checked', false);
			$('#ea-publish-switch-display').css('fontWeight', 'normal');
		}
		return false;
	});
}

</script>
 <?php

}

function printAdminCSS()
{
?>
<style type="text/css">
#ea-resourcebox-text { width:100%; }
#ea-publish-switch-wrap { padding:5px; cursor:pointer; }
#ea-summary-options-wrap { margin-top: 3px; }
#ea-summary-options-display, #ea-keywords-options-display { font-weight:bold; }
#ea-validate-post { float:left; }
#ea-submit-post { float:right; }
.center {text-align:center}
</style>

<?php
}


function ea_install()
{
	ea_version_check();

	require_once( ABSPATH . '/wp-admin/install-helper.php' );

	$create_sql = "CREATE TABLE IF NOT EXISTS `wp_ezinearticles` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`post_id` INT(15) NOT NULL ,
	`article_id` INT(15) NOT NULL ,
	`details` TEXT NOT NULL,
	`viewed` INT(1) DEFAULT 0 NOT NULL,
	`date` DATETIME NOT NULL)";

	maybe_create_table('wp_ezinearticles', $create_sql);

}

function ea_version_check()
{
	if(PHP_VERSION <= MIN_PHP_VERSION)
	{
		ea_message("The EzineArticles WordPress Plugin requires PHP version <strong>".MIN_PHP_VERSION."</strong> or higher. Your current PHP version is: <strong>".PHP_VERSION."</strong>. <br /><br />Please contact your webhosting provider and ask to update your site to PHP version <strong>".MIN_PHP_VERSION."</strong> or higher.", 'error');
	}
	if(get_bloginfo('version') <= MIN_WP_VERSION)
	{
		ea_message("The EzineArticles WordPress Plugin requires WordPress version <strong>".MIN_WP_VERSION."</strong> or higher. Your current WordPress version is: <strong>".get_bloginfo('version')."</strong>. <br /><br />Please update your WordPress to <strong>".MIN_WP_VERSION."</strong> or higher.", 'error');
	}
}
?>
