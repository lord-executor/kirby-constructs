<?php

namespace Constructs;

use Page;


class ComponentPage extends Page
{
	public function host()
	{
		/** @var Construct $construct */
		$construct = $this->kirby->get('construct', $this->intendedTemplate());

		if ($construct) {
			if ($construct->nesting() === 'children') {
				return $this->parent();
			} else {
				return $this->parent()->parent();
			}
		}

		return NULL;
	}

	public function render()
	{
		return $this->kirby->render($this);
	}
}
