<?php
/*
Plugin Name: WP EzineArticles
Plugin URI: http://EzineArticles.com/
Description: The EzineArticles WordPress Plugin allows you to submit your high quality, original WordPress posts to EzineArticles.com, as well as monitor their review status right from the WordPress administration interface!
Version: 1.6
Author: EzineArticles.com
Author URI: http://EzineArticles.com/
*/


define('WP_EA', 'WP EzineArticles');
define('EA', 'EzineArticles');
define('WP_EA_FOLDER', dirname(plugin_basename(__FILE__)), TRUE);
define('WP_EA_PLUGIN_PATH', WP_CONTENT_URL . '/plugins/' . WP_EA_FOLDER);
define('EA_AJAX', WP_EA_PLUGIN_PATH . '/inc/ajax.php');
define('MIN_PHP_VERSION', '5.1');
define('MIN_WP_VERSION', '2.7');


if( is_admin())
{
	register_activation_hook( __FILE__, 'ea_install');

	include_once('inc/eaRemote.class.inc.php');
	include_once('inc/ViewLoader.class.inc.php');

	add_action('admin_head', 'ea_assets');
	add_action('admin_menu', 'ea_add_menu');
	add_action('admin_menu', 'ea_add_post_meta_box');
	add_action('admin_menu', 'ea_alerts');

}


function ea_add_post_meta_box()
{
	if(function_exists('add_meta_box'))
	{
		add_meta_box( 'id', WP_EA, 'wp_ea_post_meta_box_view', 'post', 'side', 'high');
	}
}

function ea_add_menu()
{
	add_submenu_page(WP_EA_FOLDER, __('', WP_EA), __('Articles', WP_EA), 8, 'wp_ezinearticles', 'wp_ea_articles_view');
	add_menu_page(EA,  EA, 8, WP_EA_FOLDER, 'wp_ezinearticles',  WP_CONTENT_URL . '/plugins/' . WP_EA_FOLDER . '/img/ea.png');
	add_submenu_page(WP_EA_FOLDER, __('', WP_EA), __('Options', WP_EA), 8, 'wp_ezinearticles_options', 'wp_ea_options_view');
	add_submenu_page(WP_EA_FOLDER, __('', WP_EA), __('Account', WP_EA), 8, 'wp_ezinearticles_account', 'wp_ea_account_view');
	add_submenu_page(WP_EA_FOLDER, __('', WP_EA), __('Help', WP_EA), 8, 'wp_ezinearticles_help', 'wp_ea_help_view');
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

	$results = $wpdb->get_results("SELECT * FROM `wp_ezinearticles` WHERE article_id!=0 ORDER BY date DESC");

	$articlelist = array();

	if($results)
	{
		foreach($results as $result)
		{
			$article_results = eaRemote::get('account.article.view', array('article_id' => $result->article_id));
			if($article_results[0])
			{
				$articlelist[$result->post_id] = $article_results[0];
			}
		}
	}
	ViewLoader::load("ArticleList", array("ea_articlelist"=>$articlelist));
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

	$account_status = ea_get_option('ea_account_status');
	ViewLoader::load("Account", array("ea_account_status"=>$account_status));

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

	ViewLoader::load("Options", array(
		"ea_api_key"=>ea_get_option('ea_api_key'),
		"ea_email"=>ea_get_option('ea_email'),
		"ea_password"=>ea_get_option("ea_password")
	));
}

function wp_ea_help_view()
{
	ViewLoader::load("Help", null);
}


function wp_ea_post_meta_box_view()
{
	global $post;

	ea_alerts();
	$account_status = ea_get_option('ea_account_status');

	$editedResourceBoxText = null;
	if (isset($_COOKIE['ea_resource_text']))
	{
		$editedResourceBoxText = $_COOKIE['ea_resource_text'];
	}

	ViewLoader::load("PostMetaBox", array("ea_account_status"=>$account_status, "edited_resource_text"=>$editedResourceBoxText));
}

function ea_message($message, $class = 'updated')
{
	global $post;
	//this switches the no excerpt message
	ViewLoader::load('EAMessage',array("ea_message"=>$message, "ea_class"=>$class, "ea_post"=>$post),"part/");
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

	$selectedAuthor = null;
	if (isset($_COOKIE['ea_author']))
		$selectedAuthor = $_COOKIE['ea_author'];


	if( !isset($account_status['account_author'])) return false;
	ViewLoader::load('AuthorDropdown',array("ea_account_status"=>$account_status, "selected_author"=>$selectedAuthor ),"part/");
	return true;
}

function getResourceBoxSelect()
{
	$account_status = ea_get_option('ea_account_status');
	if( !isset($account_status[0]['resource'])) return false;

	$selectedResource = null;
	if (isset($_COOKIE['ea_resource']))
        {
		$selectedResource = $_COOKIE['ea_resource'];
		$selectedResourceText = $_COOKIE['ea_resourcebox_text'];
        }

	ViewLoader::load('ResourceBoxDropdown',array("ea_account_status"=>$account_status,"selected_text"  => $selectedResourceText ,"selected_resource_box"=>$selectedResource),"part/");
	return true;
}

function getCategorySelect()
{
	$ea_categories = ea_get_option('ea_categories');
	if( !isset($ea_categories)) return false;

	$selectedCategory = null;
	if (isset($_COOKIE['ea_category']))
		$selectedCategory = $_COOKIE['ea_category'];

	ViewLoader::load('CategoriesDropdown',array("ea_categories"=>$ea_categories, "selected_category"=>$selectedCategory),"part/");
	return true;
}

function ea_current_options()
{
	$options = array();
	$options['ea_api_key'] = ea_get_option('ea_api_key');
	$options['ea_email'] = ea_get_option('ea_email');
	$options['ea_password'] = ea_get_option('ea_password');
	return $options;
}

function ea_assets()
{
	ViewLoader::load('js',array(),"js/");
	ViewLoader::load('css',array(),"css/");
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
