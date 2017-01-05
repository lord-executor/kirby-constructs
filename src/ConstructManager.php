<?php

namespace Constructs;

use Composer\Autoload\ClassLoader;
use Data;
use Kirby;


class ConstructManager
{
	private $kirby;
	private $loader;

	public function __construct(Kirby $kirby, ClassLoader $loader)
	{
		$this->kirby = $kirby;
		$this->loader = $loader;
	}

	public function register($path)
	{
		$settings = Data::read($path . DS . 'settings.yml', 'yaml');

		if (is_dir($path . DS . 'src')) {
			$this->loader->addPsr4($settings['rootNamespace'] . '\\', $path . DS . 'src');
		}

		$this->registerComponents($path, $settings);
		$this->registerSnippets($path, $settings);
		$this->registerGlobalFields($path, $settings);
	}

	protected function registerComponents($path, $settings)
	{
		$dir = new Dir(self::componentsPath($path));

		foreach ($dir->dirs() as $component) {

			if (file_exists($component->path() . DS . $component->name() . '.yml')) {
				$this->kirby->set('blueprint', $component->name(), $component->path() . DS . $component->name() . '.yml');
			}

			if (file_exists($component->path() . DS . $component->name() . '.php')) {
				$this->kirby->set('controller', $component->name(), $component->path() . DS . $component->name() . '.php');
			}

			if (file_exists($component->path() . DS . $component->name() . '.html.php')) {
				$this->kirby->set('template', $component->name(), $component->path() . DS . $component->name() . '.html.php');
			}

		}
	}

	protected function registerSnippets($path, $settings)
	{
		foreach ((new Dir(self::snippetsPath($path)))->find(Dir::filterByExtension('php')) as $snippet) {
			// constructs/myconstruct/snippets/test.php => snippet "name" constructs/myconstruct/test
			$this->kirby->set('snippet', 'constructs' . DS . $settings['name'] . DS . substr($snippet->relative(), 0, -4), $snippet->path());
		}
	}

	protected function registerGlobalFields($path, $settings)
	{
		foreach ((new Dir(self::fieldsPath($path)))->find(Dir::filterByExtension('yml')) as $field) {
			$this->kirby->set('blueprint', 'fields/' . basename($field->name(), '.yml'), $field->path());
		}
	}

	public static function componentsPath($path)
	{
		return $path . DS . 'components';
	}

	public static function snippetsPath($path)
	{
		return $path . DS . 'snippets';
	}

	public static function fieldsPath($path)
	{
		return $path . DS . 'fields';
	}
}
