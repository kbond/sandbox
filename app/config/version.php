<?php
/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */

$file = $container->getParameter('kernel.root_dir').'/../.buildinfo';
$build = 'dev';

if (file_exists($file)) {
    $build = file_get_contents($file);
}

$container->setParameter('app.build', $build);