<?php

namespace Constructs;


use A;
use C;
use Data;


class Construct
{
	protected $settings;

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

	public function name()
	{
		return A::get($this->settings, 'name');
	}

	public function path()
	{
		return A::get($this->settings, 'path');
	}

	public function rootNamespace()
	{
		return A::get($this->settings, 'rootNamespace', '');
	}

	public function pageModel()
	{
		return A::get($this->settings, 'pageModel');
	}

	public function nesting()
	{
		return A::get($this->settings, 'nesting', 'children');
	}

	public function componentsPath()
	{
		return $this->path() . DS . 'components';
	}

	public function snippetsPath()
	{
		return $this->path() . DS . 'snippets';
	}

	public function fieldsPath()
	{
		return $this->path() . DS . 'fields';
	}
}
