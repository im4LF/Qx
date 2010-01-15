<?php
class Viewer
{
	function view()
	{
		$requests = QF::s('RequestManager')->getRequests();
		
		$responses = array();
		foreach ($requests as $url => $request)
		{
			echo $url.': '.print_r($request->response, 1);
		}
	}
}
?>