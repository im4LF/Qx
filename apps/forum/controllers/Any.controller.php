<?php
class Any_Controller
{
	public $___x	= 'default';
	
	public $request;
	public $response;
	
	public $cookie;
	public $args;
	public $get;
	public $post;
	public $files;
	
	function __construct(&$request)
	{
		$this->request =& $request;
		$this->response = array();
		
		$this->cookie =& $this->request->cookie;
		$this->args =& $this->request->url->args;
		$this->get =& $this->request->get;
		$this->post =& $this->request->post;
		$this->files =& $this->request->files;
	}
	
	function response()
	{
		return $this->response;
	}
	
	function ___someAction_x_xml() {}
}
?>