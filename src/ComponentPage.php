<?php

namespace Constructs;

use Page;


/**
 * Default component page model.
 */
class ComponentPage extends Page
{
	/**
	 * Gets the component's host (parent) page depending on the nesting setting of the construct it belongs to.
	 *
	 * @return null|Page
	 */
	public function host()
	{
		/** @var Construct $construct */
		$construct = $this->kirby->get('construct', $this->intendedTemplate());

		if ($construct) {
			if ($construct->nesting() === ':children:') {
				return $this->parent();
			} else {
				return $this->parent()->parent();
			}
		}

		return NULL;
	}

	/**
	 * Renders the component template going through the normal Kirby controller process and returns the resulting string.
	 *
	 * @return string
	 */
	public function render()
	{
		return $this->kirby->render($this);
	}
}
