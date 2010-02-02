<?php
class Simple_Access_Impl
{
	function allowed($url)
	{
		$access_config = import::config('app:configs/access.php');
		
		$user_key = '*';
		if (0)
			$user_key = 'admin:47d86a9d6b2787ce57f7d4cfc8085827';
			
		$user = $access_config->users[$user_key];
		$rules = $access_config->rules[$user['group']];
		print_r($url);
		print_r($rules);
		
		$allowed = $rules['actions']['*'];
		foreach ($rules['actions'] as $action => $access)
		{
			if ($action !== $url->action)
				continue;
				
			$allowed = $access;
			break;
		}
		
		return $allowed;
	}
}
?>