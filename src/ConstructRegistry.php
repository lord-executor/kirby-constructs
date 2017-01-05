<?php

namespace Constructs;


use Kirby\Registry\Entry;


class ConstructRegistry extends Entry
{
	protected static $components = [];

	public function set($name, $construct) {
		static::$components[$name] = $construct;
	}

	public function get($name = null) {
		if(is_null($name)) {
			return static::$components;
		} else if (isset(static::$components[$name])) {
			return static::$components[$name];
		}

		return false;
	}
}
