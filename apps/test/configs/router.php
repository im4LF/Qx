<?php
return array(
	'#^/catalogue/some-category/.*#'	=> 'CataloguePage2_Controller',
	'#^/catalogue/other-category/.*#'	=> 'CataloguePage3_Controller',
	'#^/catalogue/.*#'					=> 'CataloguePage_Controller',
	'#^/news/$#'						=> 'Bulletin_Controller',
	'#^/news/.*#'						=> 'News_Controller',
	'#^/company/$#'						=> 'Content_Controller',
	'#^/contacts/$#'					=> 'Content_Controller',
	'#^/user/$#'						=> 'User_Controller',
	'#^/$#'								=> 'Index_Controller',
);
?>