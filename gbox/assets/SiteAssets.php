<?php
namespace app\assets;
use Gbox\base\Assets;
class SiteAssets extends Assets
{
	public static $css = [
		'//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css',
		'//netdna.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.css',
		'//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css',
		'styles.css',
	];
	public static $js = [
		'//code.jquery.com/jquery-1.10.2.min.js',
		'//code.jquery.com/ui/1.10.3/jquery-ui.js',
		'//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js',
		'//cdn.ckeditor.com/4.5.7/standard/ckeditor.js',
		'//cdn.ckeditor.com/4.5.7/standard/adapters/jquery.js',
	];
	public static $meta = [
		[
			'name' => 'viewport',
			'content' => 'width=device-width, initial-scale=1, user-scalable=no',
		],
	];
}