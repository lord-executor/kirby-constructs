<?php

namespace Constructs;

use A;
use C;
use Data;


/**
 * Represents the meta information about a construct loaded from its settings file.
 */
class Construct
{
	protected $settings;

	/**
	 * Loads the construct settings from the given construct path.
	 *
	 * @param string $path
	 *   The path of the construct main directory.
	 */
	public function __construct($path)
	{
		$this->settings = Data::read($path . DS . 'settings.yml', 'yaml');
		$name = $this->name();

		// allow overrides of construct settings through Kirby config with naming convention
		foreach ($this->settings as $key => $value) {
			$confKey = implode('.', ['constructs', $name, $key]);
			if (C::get($confKey)) {
				$this->settings[$key] = C::get($confKey);
			}
		}

		$this->settings['path'] = $path;
	}

	/**
	 * Gets the construct name.
	 *
	 * @return string
	 */
	public function name()
	{
		return A::get($this->settings, 'name');
	}

	/**
	 * Gets the full path to the construct main directory.
	 *
	 * @return string
	 */
	public function path()
	{
		return A::get($this->settings, 'path');
	}

	/**
	 * Gets the root namespace for the construct's src directory.
	 *
	 * @return string
	 */
	public function rootNamespace()
	{
		return A::get($this->settings, 'rootNamespace', '');
	}

	/**
	 * Gets the fully qualified path name of the page model to be used for components in this construct.
	 *
	 * @return string|NULL
	 */
	public function pageModel()
	{
		return A::get($this->settings, 'pageModel', 'Constructs\ComponentPage');
	}

	/**
	 * Gets the nesting behavior for components in this construct.
	 *
	 * @return string
	 */
	public function nesting()
	{
		return A::get($this->settings, 'nesting', ':children:');
	}

	/**
	 * Gets the path to the construct components.
	 *
	 * @return string
	 */
	public function componentsPath()
	{
		return $this->path() . DS . 'components';
	}

	/**
	 * Gets the path to the construct snippets.
	 *
	 * @return string
	 */
	public function snippetsPath()
	{
		return $this->path() . DS . 'snippets';
	}

	/**
	 * Gets the path to the global field definitions of the construct.
	 *
	 * @return string
	 */
	public function fieldsPath()
	{
		return $this->path() . DS . 'fields';
	}

	/**
	 * Gets the absolute file path of the initialization PHP file of this construct.
	 *
	 * @return string
	 */
	public function initFilePath()
	{
		return $this->processRelativePath(A::get($this->settings, 'initFile', 'init.php'));
	}

	/**
	 * Gets the default template to be used when a component does not define its own template
	 *
	 * return null|string
	 */
	public function defaultTemplate()
	{
		return $this->processRelativePath(A::get($this->settings, 'defaultTemplate'));
	}

	/**
	 * Gets the default controller to be used when a component does not define its own controller
	 *
	 * @return null|string
	 */
	public function defaultController()
	{
		return $this->processRelativePath(A::get($this->settings, 'defaultController'));
	}

	/**
	 * Makes relative paths relative to the construct's root directory. If the given path is already absolute or NULL,
	 * it will return it as is.
	 *
	 * @param string $path
	 *   The path to process.
	 *
	 * @return null|string
	 */
	protected function processRelativePath($path)
	{
		if ($path === NULL) {
			return NULL;
		}

		if (realpath($path) === $path) {
			return $path;
		} else {
			return $this->path() . DS . $path;
		}
	}
}
