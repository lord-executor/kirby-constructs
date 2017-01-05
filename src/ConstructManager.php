<?php

namespace Constructs;

use Kirby;


class ConstructManager
{
	private $kirby;

	public function __construct(Kirby $kirby)
	{
		$this->kirby = $kirby;
	}

	public function register($path)
	{
		$this->registerComponents($path);
		$this->registerSnippets($path);
		$this->registerGlobalFields($path);
	}

	protected function registerComponents($path)
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

	protected function registerSnippets($path)
	{
		$name = basename($path);

		foreach ((new Dir(self::snippetsPath($path)))->find(Dir::filterByExtension('php')) as $snippet) {
			// constructs/myconstruct/snippets/test.php => snippet "name" constructs/myconstruct/test
			$this->kirby->set('snippet', 'constructs' . DS . $name . DS . substr($snippet->relative(), 0, -4), $snippet->path());
		}
	}

	protected function registerGlobalFields($path)
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
