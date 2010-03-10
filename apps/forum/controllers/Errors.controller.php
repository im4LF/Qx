<?php
class Errors_Controller extends Any_Controller
{
	function notFound() 
	{
		$this->response = array(
			'title' => '['.$this->request->raw_url.'] not found'
		);
		return true;
	}
	
	function accessDenied()
	{
		$this->response = array(
			'title' => 'not allowed'
		);
		return true;
	}
}
?>