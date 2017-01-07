<?php

namespace Constructs;

use Kirby\Registry\Entry;


/**
 * Kirby registry implementation for constructs. This registry associates the construct meta information of a
 * {@see Construct} instance with each component of that construct. Basically, this allows a component page to figure
 * out which component it belongs to.
 */
class ConstructRegistry extends Entry
{
	protected static $components = [];

	/**
	 * Registers a component (by name) with a construct.
	 *
	 * @param string $name
	 *   The name of the component.
	 *
	 * @param Construct $construct
	 *   The construct this component belongs to.
	 */
	public function set($name, Construct $construct)
	{
		static::$components[$name] = $construct;
	}

	/**
	 * Gets a specific component's construct by component name or a complete map (array) of all components mapped to
	 * their constructs.
	 *
	 * @param string|null $name
	 *   The name of a component.
	 *
	 * @return array|bool|Construct
	 *   Returns: false if a component name is given that does not exist; an instance of {@see Construct} if it does; an
	 *   array mapping component names to constructs if no name is given.
	 */
	public function get($name = NULL)
	{
		if (is_null($name)) {
			return static::$components;
		} else if (isset(static::$components[$name])) {
			return static::$components[$name];
		}

		return false;
	}
}
