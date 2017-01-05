<?php

namespace Constructs;


use A;
use Data;


class Construct
{
	protected $settings;

	public function __construct($path)
	{
		$this->settings = Data::read($path . DS . 'settings.yml', 'yaml');
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
