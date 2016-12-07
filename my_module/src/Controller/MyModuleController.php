<?php

namespace Drupal\my_module\Controller;

use Drupal\Core\Controller\ControllerBase;

class MyModuleController extends ControllerBase
{
	public function test() {
		$output = node_load_multiple();
		$output = node_view_multiple($output);
		return array(
			'#markup' => render($output),
		);
	}
}