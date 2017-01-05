<?php

namespace Constructs;


use Obj;


class Dir
{
	protected $path;
	protected $entries;

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

	public function files()
	{
		return $this->entries->filter([static::class, 'filterFiles']);
	}

	public function dirs()
	{
		return $this->entries
			->filter([static::class, 'filterDirectories'])
			->map([static::class, 'extendDir']);
	}

	public function all()
	{
		return $this->entries;
	}

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
