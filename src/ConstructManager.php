<?php

namespace Constructs;


use Kirby;


class ConstructManager
{
	private $kirby;

	public function __construct(Kirby $kirby)
	{
		$this->kirby = $kirby;
	}

	public function register($path)
	{
	}
}