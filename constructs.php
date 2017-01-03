<?php

$loader = new \Composer\Autoload\ClassLoader();
$loader->addPsr4('Constructs\\', __DIR__ . DS . 'src');

foreach (c::get('constructs.dirs', ['Constructs' => 'site/constructs']) as $name => $constructDir) {
	$dir = $kirby->roots()->index . DS . $constructDir;
	if (is_dir($dir . DS . 'src')) {
		$loader->addPsr4('Constructs\\' . $name . '\\', $dir . DS . 'src');
	}
}

$loader->register();

$mgr = new \Constructs\ConstructManager($kirby);
foreach (c::get('constructs.dirs', ['Constructs' => 'site/constructs']) as $name => $constructDir) {
	$mgr->register($kirby->roots()->index . DS . $constructDir);
}
