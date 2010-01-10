<?php
return array(
	'#/catalogue/some-category/.*#'		=> 'CataloguePage2',
	'#/catalogue/other-category/.*#'	=> 'CataloguePage3',
	'#/catalogue/.*#'					=> 'CataloguePage',
	'#/news/\d+#'						=> 'CataloguePage2',
	'#/news/#'							=> 'Bulletin',
	'#/news/.*#'						=> 'News',
	'#/company/#'						=> 'Content',
	'#/contacts/#'						=> 'Content',
	'#/user/#'							=> 'User',
	'#/#'								=> 'Index',
	'#.*#'								=> 'Error404'
);
?>