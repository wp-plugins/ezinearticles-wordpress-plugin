<?php
require_once(ABSPATH.'wp-includes/class-snoopy.php');

class eaRemote{

	private static $API = 'http://api.ezinearticles.com/api.php?';
	private static $RESPONSEFORMAT = 'phpserial';
	private static $DATAAMOUNT = 'extended';
	private $options = 'extended';

	/* in as array */
	static function get($method, $with = null)
	{

		$that = array_merge( array('search'=>$method), (array)$with, self::common_vars($method) );

		array_walk($that, create_function('&$i,$k','$i="$k=$i";'));

		$get_query =  implode('&', $that);

		$s = new Snoopy();

		$s->fetch(self::$API . $get_query);

		$results = unserialize($s->results);

		return $results[$method];

	}

	/* in as array */
	static function post($method, $with = null)
	{
		// post as array

		$post_query = array_merge( array('submit'=>$method), (array)$with, self::common_vars($method));

		$s = new Snoopy();

		$s->submit(self::$API, $post_query);

		$results = unserialize($s->results);

		if($results[$method][0])
			return $results[$method][0];
		else
			return $results;
	}

	function common_vars($method)
	{

		$common_vars = array();

		if(self::authentication_required($method))
		{
			$common_vars['email'] = self::email();
			$common_vars['pass'] = self::password();
		}

		$common_vars['response_format'] = self::$RESPONSEFORMAT;
		$common_vars['data_amount'] = self::$DATAAMOUNT;
		$common_vars['key'] = self::key();


		return $common_vars;

	}

	function authentication_required($method)
	{
		$required = array('account.status', 'account.article.new', 'account.article.update', 'account.article.view');

		return in_array($method, $required);
	}

	function key()
	{
		return self::get_option('ea_api_key');
	}

	function email()
	{
		return self::get_option('ea_email');
	}

	function password()
	{
		return self::get_option('ea_password');
	}


	function get_option($option)
	{
		$options = get_option('ezinearticles_options');
		return $options[$option];
	}

}



?>
