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

		$items = array_filter(scandir($this->path), function ($entry) {
			return $entry !== '.' && $entry !== '..';
		});

		$this->entries = array_map(function ($entry) use ($path) {
			return new Obj(['name' => $entry, 'path' => $path . DS . $entry]);
		}, $items);
	}

	public function files()
	{
		return array_filter($this->entries, function ($entry) {
			return is_file($entry->path());
		});
	}

	public function dirs()
	{
		$dirs = array_filter($this->entries, function ($entry) {
			return is_dir($entry->path());
		});

		return array_map(function ($dir) {
			if (!isset($dir->dir)) {
				$dir->set('dir', new Dir($dir->path));
			}

			return $dir;
		}, $dirs);
	}

	public function all()
	{
		return $this->entries;
	}
}
