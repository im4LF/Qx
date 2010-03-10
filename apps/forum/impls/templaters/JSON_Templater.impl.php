<?php 
class JSON_Templater_Impl
{
    function view($data, $template = '')
    {
        return json_encode($data);
    }
}
?>
