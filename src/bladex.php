<?php

namespace IsraelNogueira\BladeX;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\Container as ContainerInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory as FactoryContract;
use Illuminate\Contracts\View\View;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Facade;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Factory;
use Illuminate\View\ViewServiceProvider;
use Illuminate\Support\Facades\Blade;
class bladex implements FactoryContract{
	/**
	 * @var Application
	 */
	protected $container;

	/**
	 * @var Factory
	 */
	private $factory;

	/**
	 * @var BladeCompiler
	 */
	private $compiler;

	public function __construct($viewPaths=null, string $cachePath=null, ContainerInterface $container = null)
	{
		$this->container = $container ?: new Container;
		$VIEWS = $viewPaths?? realpath(__DIR__.'/../../../..').'/views';
		$CACHE = $cachePath?? realpath(__DIR__.'/../../../..').'/cache';
		$this->setupContainer((array) $VIEWS, $CACHE);
		(new ViewServiceProvider($this->container))->register();
		$this->factory = $this->container->get('view');
		$this->compiler = $this->container->get('blade.compiler');


		
		/*
		|--------------------------------------------------------------------
		|	Aqui acrescentei apenas para funcionar em meu framework
		|   pode comentar se quiser
		|--------------------------------------------------------------------
		*/
			$this->directive('include', function ($view) {
				$view = bladex::processView($view);
				return '<?php echo $__env->make("'.$view.'", \Illuminate\Support\Arr::except(get_defined_vars(), [\'__data\', \'__path\']))->render(); ?'.'>';
			});

			$this->directive('block', function ($expression) {

				$view =   explode('.',trim(trim($expression,'"'),"'"));
				if ($view[0] == "project") {
					$view[0] = "app.projetos.".getEnv('APP_NAME');
				}
				if ($view[0] == "system") {
					$view[0] = "app.system";
				}

				$section = end($view)??0;
				array_pop($view);
				$hasPath = count($view);
				$_return = "";

				$_return.="<?php".PHP_EOL;
				
				if($hasPath>0  && $section!=0){		$_return.="	if (empty(trim(\$__env->yieldContent('$section')))){".PHP_EOL;}
				if($hasPath>0  && $section!=0){		$_return.="		echo \$__env->make('".implode('.', $view)."', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render();".PHP_EOL;}
				if($hasPath>0  && $section!=0){		$_return.="	}".PHP_EOL.PHP_EOL;}

				$_return.="	if (!empty(trim(\$__env->yieldContent('$section')))){".PHP_EOL;
				$_return.="		echo \$__env->yieldContent('$section');".PHP_EOL;
				$_return.="	}".PHP_EOL;
				$_return.="?>".PHP_EOL;
				return $_return;
			});

			$this->directive('csrfToken', function ($expression) {
				$_return ="<?php".PHP_EOL;
				$_return.="	echo	system\lib\system::windowCsrf_token($expression);".PHP_EOL;
				$_return.="?>".PHP_EOL;
				return $_return;
			});
			
			$this->directive('langView', function ($expression) {
				$_return ="<?php".PHP_EOL;
				$_return.=" echo \system\lib\system::langView();".PHP_EOL;
				$_return.="?>".PHP_EOL;
				return $_return;
			});

		/*--------------------------------------------------------------*/
		/*------------------------- FIM --------------------------------*/
		/*--------------------------------------------------------------*/
	}

	static public function processView(string $view){
		/*
		|--------------------------------------------------------------------
		|	Aqui acrescentei apenas para funcionar em meu framework
		|   pode comentar se quiser
		|--------------------------------------------------------------------
		*/
			$_PATHARRAY =	explode('.', trim($view,"'"));
			if(!is_null(getEnv('APP_NAME'))){
				if($_PATHARRAY[0]=='project'){
					$_PATHARRAY[0]='app.projetos.'.getEnv('APP_NAME');
				}
				if($_PATHARRAY[0]=='system'){
					$_PATHARRAY[0]='app.system';
				}
				$view=implode('.', $_PATHARRAY);
			}
			return $view;
	}


	static public function view(string $view, array $data = [], array $mergeData = []){

		$VIEWS      =   $viewPaths?? realpath(__DIR__.'/../../../..');
		$CACHE      =   $cachePath?? realpath(__DIR__.'/../../../..').'/cache';
		$instancia  =   new self($VIEWS,$CACHE); 
		$view		= bladex::processView($view);
		return $instancia->make($view, $data, $mergeData);

	}


	static public function render(string $view, array $data = [], array $mergeData = []): string
	{
		return bladex::view($view, $data, $mergeData)->render();
	}

	public function make($view, $data = [], $mergeData = []): View
	{
		return $this->factory->make($view, $data, $mergeData);
	}

	public function compiler(): BladeCompiler
	{
		return $this->compiler;
	}

	public function directive(string $name, callable $handler)
	{
		$this->compiler->directive($name, $handler);
	}
	
	public function if($name, callable $callback)
	{
		$this->compiler->if($name, $callback);
	}

	public function exists($view): bool
	{
		return $this->factory->exists($view);
	}

	public function file($path, $data = [], $mergeData = []): View
	{
		return $this->factory->file($path, $data, $mergeData);
	}

	public function share($key, $value = null)
	{
		return $this->factory->share($key, $value);
	}

	public function composer($views, $callback): array
	{
		return $this->factory->composer($views, $callback);
	}

	public function creator($views, $callback): array
	{
		return $this->factory->creator($views, $callback);
	}

	public function addNamespace($namespace, $hints): self
	{
		$this->factory->addNamespace($namespace, $hints);

		return $this;
	}

	public function replaceNamespace($namespace, $hints): self
	{
		$this->factory->replaceNamespace($namespace, $hints);

		return $this;
	}

	public function __call(string $method, array $params)
	{
		return call_user_func_array([$this->factory, $method], $params);
	}

	protected function setupContainer(array $viewPaths, string $cachePath)
	{
		$this->container->bindIf('files', function () {
			return new Filesystem;
		}, true);

		$this->container->bindIf('events', function () {
			return new Dispatcher;
		}, true);

		$this->container->bindIf('config', function () use ($viewPaths, $cachePath) {
			return [
				'view.paths' => $viewPaths,
				'view.compiled' => $cachePath,
			];
		}, true);
		
		Facade::setFacadeApplication($this->container);
	}
}


