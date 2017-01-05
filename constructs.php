<?php

$loader = new \Composer\Autoload\ClassLoader();
$loader->addPsr4('Constructs\\', __DIR__ . DS . 'src');
$loader->register();

$loader = new \Composer\Autoload\ClassLoader();
$mgr = new \Constructs\ConstructManager($kirby, $loader);

foreach (c::get('constructs.dirs', ['site/constructs']) as $constructDir) {
	$mgr->register($kirby->roots()->index . DS . $constructDir);
}

$loader->register();
