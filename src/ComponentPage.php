<?php

namespace Constructs;

use Page;
use Redirect;


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
		return $this->kirby->render($this, ['component' => true]);
	}

	/**
	 * Prevents components from being viewed directly
	 */
	public function controller($arguments = array())
	{
		if (!isset($arguments['component']) || isset($arguments['component']) !== true) {
			if ($host = $this->host()) {
				Redirect::to($host->uri());
			} else {
				Redirect::home();
			}
		}

		return parent::controller($arguments);
	}
}
