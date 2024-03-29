<?php

/*
 * This file is part of the symfony package.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__).'/../lib/lime/lime.php';
require_once dirname(__FILE__).'/../../lib/sfTemplateAutoloader.php';
sfTemplateAutoloader::register();

$t = new lime_test(2);

// __construct() __toString()
$t->diag('__construct() __toString()');

$storage = new sfTemplateStorage('foo');
$t->is((string) $storage, 'foo', '__toString() returns the template name');

// ->getRenderer()
$t->diag('->getRenderer()');
$storage = new sfTemplateStorage('foo', $renderer = new sfTemplateRendererPhp());
$t->ok($storage->getRenderer() === $renderer, '->getRenderer() returns the renderer');
