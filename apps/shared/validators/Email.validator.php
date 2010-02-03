<?php
class Email extends String
{
	function correct()
	{
		if ($this->_optional && empty($this->_value))
			return $this->_addValidationResult('correct', true);
		
		$re = '/^[a-z0-9!#$%&*+-=?^_`{|}~]+(\.[a-z0-9!#$%&*+-=?^_`{|}~]+)*';
    	$re.= '@([-a-z0-9]+\.)+([a-z]{2,3}';
    	$re.= '|info|arpa|aero|coop|name|museum)$/ix';

		return $this->_addValidationResult('correct', (bool) preg_match($re, $this->_value));
	}
}
?>