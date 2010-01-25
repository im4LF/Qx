<?php
class sExternal extends sAny
{
	public $impls;
	
	function open()
	{
		$config = import::config('app:app.php');
		$this->impls = $config['impls'];
		
		/*import::from('app:libs/dibi/dibi.min.php');
		dibi::connect($config['db']['dsn']);*/
		/*import::from('app:libs/DbSimple/config.php');
		import::from('app:libs/DbSimple/Generic.php');
		$db = DbSimple_Generic::connect($config['db']['dsn']);
		print_r($db);*/
		return $this;
	}
	
	function run()
	{
		$b_key = 'main request - ['.$this->request->raw_url.']';
		Benchmark::start($b_key);
		
		$this->request->url    = F($this->impls['url'], $this->request->raw_url)->parse();
		$this->request->args   = $this->request->url->args;  
		$this->request->router = F($this->impls['router'], $this->request)->route();
		$this->request->runner = F('Runner', $this->request)->run();
		echo print_r($this, 1);
		Benchmark::stop($b_key);
		
		return $this;
	}
	
	function close()
	{
		$response = $this->request->runner->result;
		/*$response['__debug__']['benchmarks'] = Benchmark::get();
		
		//echo print_r($response, 1);
		
		$view_name = $this->request->runner->view_name;
		$view_impl = $this->impls[$this->request->url->view.'-view'];
		
		echo F::n($view_impl)->view($response, $view_name);	// and make view*/
	}
}
?>