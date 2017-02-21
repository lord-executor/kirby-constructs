<?php

namespace Constructs\Util;

use ArrayObject;
use IteratorAggregate;
use Traversable;


/**
 * Enumerable helper class to deal with PHP's complete lack of reasonable map & filter APIs. - to be clear, PHP
 * does have map and filter APIs, they are just not at all _reasonable_.
 */
class Enumerable implements IteratorAggregate
{
	/**
	 * Creates an Enumerable from an array.
	 *
	 * @param array $arr
	 *   The array.
	 *
	 * @return Enumerable
	 */
	public static function fromArray(array $arr)
	{
		return new Enumerable(new ArrayObject($arr));
	}

	protected $inner;

	/**
	 * Creates an Enumerable from any IteratorAggregate implementation (anything with a getIterator function).
	 *
	 * @param Traversable $inner
	 *   The underlying traversable object.
	 */
	public function __construct(Traversable $inner)
	{
		$this->inner = $inner;
	}

	/**
	 * Creates a new Enumerable by applying the mapping function $func to each element of the current Enumerable.
	 *
	 * @param callable $func
	 *   Mapping function with the signature 'function (mixed) { return mixed; }'
	 *
	 * @return Enumerable
	 */
	public function map($func)
	{
		$result = [];

		foreach ($this->inner as $key => $value) {
			$result[] = call_user_func($func, $value);
		}

		return new Enumerable(new ArrayObject($result));
	}

	/**
	 * Creates a new Enumerable that only contains the elements from the current Enumerable that match the given
	 * $predicate function.
	 *
	 * @param callable $predicate
	 *   Predicate function with the signature 'function (mixed) { return bool; }'
	 *
	 * @return Enumerable
	 */
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
	 * Returns an array with all the enumerable items.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return iterator_to_array($this->inner);
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
