<?php

namespace Constructs\Util;


use ArrayObject;
use IteratorAggregate;
use Traversable;


class Enumerable implements IteratorAggregate
{
	public static function fromArray($arr)
	{
		return new Enumerable(new ArrayObject($arr));
	}

	protected $inner;

	public function __construct(IteratorAggregate $inner)
	{
		$this->inner = $inner;
	}

	public function map($func)
	{
		$result = [];

		foreach ($this->inner as $key => $value) {
			$result[] = call_user_func($func, $value);
		}

		return new Enumerable(new ArrayObject($result));
	}

	public function filter($predicate)
	{
		$result = [];

		foreach ($this->inner as $key => $value) {
			if (call_user_func($predicate, $value)) {
				$result[] = $value;
			}
		}

		return new Enumerable(new ArrayObject($result));
	}

	/**
	 * Retrieve an external iterator
	 * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
	 * @return Traversable An instance of an object implementing <b>Iterator</b> or
	 * <b>Traversable</b>
	 * @since 5.0.0
	 */
	public function getIterator()
	{
		return $this->inner->getIterator();
	}
}
