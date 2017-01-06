<?php

namespace Constructs\Util;

use Obj;


/**
 * Helper class that facilitates working with and going through directory structures, finding specific files, etc.
 */
class Dir
{
	protected $path;
	protected $entries;

	/**
	 * Creates a new Dir instance with the given directory path.
	 *
	 * @param string $path
	 *   Path to a file system directory.
	 */
	public function __construct($path)
	{
		$this->path = $path;

		if (is_dir($path)) {
			$this->entries = Enumerable::fromArray(scandir($this->path))
				->filter([static::class, 'filterExcludeDots'])
				->map(static::expandObj($path));
		} else {
			$this->entries = Enumerable::fromArray([]);
		}
	}

	/**
	 * Gets an Enumerable collection of all the files (no sub-directories) within this directory.
	 *
	 * @return Enumerable<Obj>
	 */
	public function files()
	{
		return $this->entries->filter([static::class, 'filterFiles']);
	}

	/**
	 * Gets an Enumerable collection of all the sub-directories (no files) within this directory.
	 *
	 * @return Enumerable<Obj>
	 */
	public function dirs()
	{
		return $this->entries
			->filter([static::class, 'filterDirectories'])
			->map([static::class, 'extendDir']);
	}

	/**
	 * Gets an Enumerable of all files and sub-directories within this directory.
	 *
	 * @return Enumerable<Obj>
	 */
	public function all()
	{
		return $this->entries;
	}

	/**
	 * Recursivel finds all files within the current directory and it's descendants that match the given predicate.
	 *
	 * @param callable $predicate
	 *   A file matching predicate with the signature 'function (Obj) { return bool; }' that takes a directory entry
	 *   and returns true/false depending on whether the file should match or not.
	 *
	 * @return Enumerable
	 */
	public function find($predicate)
	{
		return Enumerable::fromArray($this->findInternal($predicate, $this->path));
	}

	private function findInternal($predicate, $base)
	{
		$result = [];

		foreach ($this->entries as $entry) {
			if (call_user_func($predicate, $entry)) {
				$clone = clone($entry);
				$clone->set('relative', substr($clone->path(), strlen($base) + 1));
				$result[] = $clone;
			} else if ($entry->isDir()) {
				$child = new Dir($entry->path());
				$result = array_merge($result, $child->findInternal($predicate, $base));
			}
		}

		return $result;
	}

	public static function filterExcludeDots($name)
	{
		return $name !== '.' && $name !== '..';
	}

	public static function filterFiles($entry)
	{
		return $entry->isFile();
	}

	public static function filterDirectories($entry)
	{
		return $entry->isDir();
	}

	public static function filterByExtension($extension)
	{
		return function ($entry) use ($extension) {
			return pathinfo($entry->name(), PATHINFO_EXTENSION) === $extension;
		};
	}

	public static function expandObj($path)
	{
		return function ($name) use ($path) {
			$filePath = $path . DS . $name;
			return new Obj([
				'name' => $name,
				'path' => $filePath,
				'isDir' => is_dir($filePath),
				'isFile' => is_file($filePath),
			]);
		};
	}

	public static function extendDir($entry)
	{
		if (!isset($entry->dir)) {
			$clone = clone($entry);
			$clone->set('dir', new Dir($clone->path));
		}

		return $entry;
	}
}
