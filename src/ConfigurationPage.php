<?php

namespace Constructs;

use Page;
use Redirect;


/**
 * Default component page model.
 */
class ConfigurationPage extends Page
{
	public function controller($arguments = array())
	{
		$data = parent::controller($arguments);
		if (empty($data)) {
			Redirect::home();
		}
		return $data;
	}
}
