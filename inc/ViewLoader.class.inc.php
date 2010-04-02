<?php
class ViewLoader
{
	public static function load($view, $args, $subpath = null)
	{
		if(is_array($args))
		{
			extract($args);
		}
		include(ViewLoader::getPath("view/".$subpath,$view));
	}
	private static function getPath($folder, $view)
	{
		return $folder.$view.".tpl.php";
	}
}
?>