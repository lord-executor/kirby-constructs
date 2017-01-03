<?php

namespace Constructs;


use Kirby;
use Obj;


class ConstructManager
{
	private $kirby;

	public function __construct(Kirby $kirby)
	{
		$this->kirby = $kirby;
	}

	public function register($path)
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

	public static function componentsPath($path)
	{
		return $path . DS . 'components';
	}
}
