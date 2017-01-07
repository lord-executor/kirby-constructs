<?php

load([
	'kirby\\registry\\construct' => __DIR__ . DS . 'kirby' . DS . 'registry' . DS . 'Construct.php',
]);

$loader = new \Composer\Autoload\ClassLoader();
$loader->addPsr4('Constructs\\', __DIR__ . DS . 'src');
$loader->register();


$mgr = \Constructs\ConstructManager::instance();

foreach (c::get('constructs.dirs', ['site/constructs']) as $constructDir) {
	$mgr->find($kirby->roots()->index . DS . $constructDir);
}

$kirby->routes([
	'constructAssets' => [
		'pattern' => 'assets/constructs/(:any)/(:all)',
		'method' => 'GET',
		'action' => [$mgr, 'assetsAction'],
	],
]);

Page::$methods['components'] = function (Page $page, $container = NULL) {
	if ($container === NULL) {
		$container = c::get('constructs.components.container.default', ':children:');
	}

	if ($container === ':children:') {
		return $page->children();
	} else {
		return $page->find($container)->children();
	}
};
