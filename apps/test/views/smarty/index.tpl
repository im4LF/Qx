<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title>{$title}</title>
	</head>
	<body>
		<ul>
		{foreach item=v key=k from=$__debug__.benchmarks}
			<li>{$k}: {$v->time}</li>
		{/foreach}
		</ul>
	</body>
</html>
