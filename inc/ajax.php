<?php

	$post_id = $_REQUEST['post_ID'];

	if(!$post_id) die();

	define(WP_INSTALLING, '1');

	include_once( '../../../../wp-load.php' );
	include_once('ea_remote.class.inc.php');

	global $wpdb;

	$post_id = $_REQUEST['post_ID'];

	$summary = ($_REQUEST['ea_summary'] == 'use_excerpt') ? $_REQUEST['excerpt'] : getSummaryFromPostContent($_REQUEST['content']);
	$keywords = (!empty($_REQUEST['newtag']['post_tag'])) ? $_REQUEST['newtag']['post_tag'] : $_REQUEST['tax_input']['post_tag'];

	$submit['title'] = $_REQUEST['post_title'];
	$submit['body']= $_REQUEST['content'];
	$submit['author']= $_REQUEST['ea_author'];
	$submit['category']= $_REQUEST['ea_category'];
	$submit['summary']= $summary;
	$submit['keywords']= $keywords;
	$submit['signature']= $_REQUEST['ea_resourcebox_text'];

    if( $_REQUEST['ea_do_validate'] )
		$submit['validate_only']= 1;

	$method = (ea_is_article($post_id)) ? 'account.article.update' : 'account.article.new';

	$submit_result = eaRemote::post($method, $submit);


	if($submit_result['success'])
	{

		if( isset($_REQUEST['ea_do_validate']))
			ea_log(array(null, 'details' => 'Validation was successful. You can submit your post to EzineArticles.com now by clicking on "Submit" button.', 'post_id' => $post_id));
		else
			ea_log(array('article_id' => $submit_result['article_id'], 'details' => 'Successfully submitted for review.', 'post_id' => $post_id));

	}
	elseif($submit_result['error'])
	{
		if( isset($_REQUEST['ea_do_validate']) )
			ea_log(array(null, 'details' => $submit_result['error'], 'post_id' => $post_id));
		else
			ea_log(array('article_id' => $submit_result['article_id'], 'details' => $submit_result['error'], 'post_id' => $post_id));
	}



function ea_log($details)
{
	global $wpdb;

	$wpdb->insert('wp_ezinearticles', array('post_id' => $details['post_id'], 'article_id' => $details['article_id'], 'details' => $details['details'], 'date' => date('Y-m-d H:i:s')));

}

function ea_is_article($post_id)
{
	global $wpdb;

	$row = $wpdb->get_row("SELECT article_id FROM `wp_ezinearticles` WHERE post_id='{$post_id}' AND article_id!=0 LIMIT 1");

	if($row && $row->article_id)
		return $row->article_id;
	else
		return false;
}

function getSummaryFromPostContent($content)
{

	preg_match('/^([^.!?]*[\.!?]+){0,2}/', strip_tags($content), $summary);

	return $summary[0];
}

?>
