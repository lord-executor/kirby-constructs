<?php

namespace Constructs;

use Composer\Autoload\ClassLoader;
use Constructs\Util\Dir;
use Data;
use F;
use Kirby;
use L;
use Media;
use Response;


/**
 * Manages construct registration.
 */
class ConstructManager
{
	public static $instance;

	/**
	 * Singleton implementation.
	 *
	 * @return ConstructManager
	 */
	public static function instance()
	{
		if (!is_null(static::$instance)) {
			return static::$instance;
		}
		return static::$instance = new static(kirby());
	}


	private $kirby;
	private $loader;
	private $constructs;

	/**
	 * Creates a new construct manager.
	 *
	 * @param Kirby $kirby
	 *   The Kirby instance.
	 */
	public function __construct(Kirby $kirby)
	{
		$this->kirby = $kirby;
		$this->loader = new ClassLoader();
		$this->loader->register();
		$this->constructs = [];
	}

	/**
	 * Locates all constructs in the given path - that is: all subirectories with a settings.yml file - and registers
	 * them with the manager.
	 *
	 * @param string $path
	 *   Path to search for constructs.
	 */
	public function find($path)
	{
		$dir = new Dir($path);

		foreach ($dir->dirs() as $construct) {
			if (file_exists($construct->path() . DS . 'settings.yml')) {
				$this->register($construct->path());
			}
		}
	}

	/**
	 * Registers a construct at the given location.
	 *
	 * @param string $path
	 *   The path to a construct's main directory.
	 */
	public function register($path)
	{
		$construct = new Construct($path);

		$this->constructs[$construct->name()] = $construct;

		if (is_dir($path . DS . 'src')) {
			$this->loader->addPsr4($construct->rootNamespace() . '\\', $path . DS . 'src');
		}

		$this->registerComponents($construct);
		$this->registerSnippets($construct);
		$this->registerGlobalFields($construct);

		$initFile = $construct->initFilePath();
		if (file_exists($initFile)) {
			require($initFile);
		}
	}

	/**
	 * Code mostly borrowed from Kirby's 'pluginAssets' route (kirby.php)
	 *
	 * @param string $constructName
	 *   The name of the construct from which the asset should be retrieved.
	 * @param string $path
	 *   The relative path to the asset within the construct's 'assets' directory.
	 *
	 * @return Response
	 */
	public function assetsAction($constructName, $path)
	{
		$construct = $this->constructs[$constructName];

		if ($construct) {
			$assetPath = $construct->path() . DS . 'assets' . DS . $path;
			$file = new Media($assetPath);

			if ($file->exists()) {
				return new Response(F::read($assetPath), F::extension($assetPath));
			}
		}

		return new Response('The file could not be found', F::extension($path), 404);
	}

	/**
	 * Loads language files for all constructs. This function _must_ be called from within the site's language files
	 * in order to work. Add the following piece of code to each language file at the top:
	 *
	 * ```php
	 * \Constructs\ConstructManager::instance()->localize();
	 * ```
	 *
	 * See also {@see Kirby::localize()}
	 */
	public function localize()
	{
		$lang = $this->kirby->site()->language()->code();

		/** @var Construct $construct */
		foreach ($this->constructs as $construct) {
			$path = $construct->languagesPath() . DS . $lang;

			// load .php file if it exists
			if(f::exists($path . '.php')) include_once($path . '.php');

			// load .yml file and set as language variables if it exists
			if(f::exists($path . '.yml')) L::set(Data::read($path . '.yml', 'yaml'));
		}
	}

	protected function registerComponents(Construct $construct)
	{
		$defaultController = NULL;
		$dir = new Dir($construct->componentsPath());

		// If a controller is registered as a _file_, the controller registry only loads the controller once and all
		// subsequent uses of that controller won't work because it will reload the file (require_once). Therefore,
		// the default controller has to be registered as a closure.
		if (($file = $construct->defaultController()) && file_exists($file)) {
			$defaultController = require_once($file);
		}

		foreach ($dir->dirs() as $component) {

			if (file_exists($component->path() . DS . $component->name() . '.yml')) {
				$this->kirby->set('blueprint', $component->name(), $component->path() . DS . $component->name() . '.yml');
				$this->kirby->set('construct', $component->name(), $construct);

				if ($model = $construct->pageModel($component->name())) {
					$this->kirby->set('page::model', $component->name(), $model);
				}

				if (file_exists($component->path() . DS . $component->name() . '.php')) {
					$this->kirby->set('controller', $component->name(), $component->path() . DS . $component->name() . '.php');
				} else if ($defaultController) {
					$this->kirby->set('controller', $component->name(), $defaultController);
				}

				if (file_exists($component->path() . DS . $component->name() . '.html.php')) {
					$this->kirby->set('template', $component->name(), $component->path() . DS . $component->name() . '.html.php');
				} else if ($construct->defaultTemplate()) {
					$this->kirby->set('template', $component->name(), $construct->defaultTemplate());
				}
			}

		}
	}

	protected function registerSnippets(Construct $construct)
	{
		foreach ((new Dir($construct->snippetsPath()))->find(Dir::filterByExtension('php')) as $snippet) {
			// constructs/myconstruct/snippets/test.php => snippet "name" constructs/myconstruct/test
			$this->kirby->set('snippet', 'constructs' . DS . $construct->name() . DS . substr($snippet->relative(), 0, -4), $snippet->path());
		}
	}

	protected function registerGlobalFields(Construct $construct)
	{
		foreach ((new Dir($construct->fieldsPath()))->find(Dir::filterByExtension('yml')) as $field) {
			$this->kirby->set('blueprint', 'fields/' . basename($field->name(), '.yml'), $field->path());
		}
	}


}
