<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
|  Theme Settings
| -------------------------------------------------------------------
|  layout_default = path to the default theme 
|  layout_assets  = path to the assets folder 
|  layout_shared  = path to a shared assets folder
|  layout_styles  = path to css folder inside assets
|  layout_images  = path to images folder inside assets
|  layout_script  = path to javascript folder inside assets
|  layout_commons = path to common elements folder inside view/theme
|  layout_content = path to content elements folder inside view/theme
|
|  Note: make sure you don't forget the trailing slashes
| -------------------------------------------------------------------
*/
$config['layout_default'] = "default";
$config['layout_assets']  = "assets/";
$config['layout_shared']  = "share/";
$config['layout_styles']  = "css/";
$config['layout_images']  = "images/";
$config['layout_script']  = "script/";
$config['layout_commons'] = "common/";
$config['layout_content'] = "content/";

/*
| -------------------------------------------------------------------
|  Layout Elements
| -------------------------------------------------------------------
|  layout_model    = common elements model name
|  layout_elements = references all functions in this model so the 
|                    library can automatically call each one of them. 
|                    Don't forget to write them in 'layout_model' ;-)
|  					 prototype: array("function" => "parameter", ...);
|			  		 where function is the funcion name and parameter
|					 is a single value or an array of values to send 
|					 to that function. 
| -------------------------------------------------------------------
*/
$config['layout_model']    = "layout_model";
$config['layout_elements'] = array();


?>