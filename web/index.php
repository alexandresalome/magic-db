<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Process\ProcessBuilder;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Application();
$app['debug'] = true;
$app['images.file'] = __DIR__.'/images.json';
$app['images'] = json_decode(file_get_contents($app['images.file']), true);
$app['containers.file'] = __DIR__.'/containers.json';
$app['containers'] = json_decode(file_get_contents($app['containers.file']), true);

$app->get('/', function (Application $app) {
    return json_encode(array(
        'available_images' => $app['images']
    ));
});

$app->post('/snapshot', function (Application $app, Request $request) {
    $data = json_decode($request->getContent(), true);
    $id   = $data['id'];
    $name = $data['name'];

    $container = null;
    foreach ($app['containers'] as $row) {
        if ($row['id'] == $id) {
            $container = $row;
        }
    }

    if (null === $container) {
        throw new NotFoundHttpException();
    }

    $command = array('docker', 'commit', $id, $name);
    $process = ProcessBuilder::create($command)->getProcess();
    $process->mustRun();

    $images = $app['images'];
    $images[$name] = $name;

    file_put_contents($app['images.file'], json_encode($images));

    return json_encode(array('ok :)'));
});

$app->post('/stop', function (Application $app, Request $request) {
    $data = json_decode($request->getContent(), true);
    $id   = $data['id'];

    $container = null;
    $hahaha = array();
    foreach ($app['containers'] as $row) {
        if ($row['id'] == $id) {
            $container = $row;
        } else {
            $hahaha[] = $row;
        }
    }

    if (null === $container) {
        throw new NotFoundHttpException();
    }

    $command = array('docker', 'rm', '-f', $id);
    $process = ProcessBuilder::create($command)->getProcess();
    $process->mustRun();

    $images = $app['images'];
    $images[$name] = $name;

    file_put_contents($app['containers.file'], json_encode($hahaha));

    return json_encode(array('ok :)'));
});

$app->post('/create', function (Application $app, Request $request) {
    $data = json_decode($request->getContent(), true);
    $image = $data['image'];
    $images = $app['images'];
    $pos = array_search($image, $images);
    if (false === $pos) {
        throw new NotFoundHttpException();
    }

    $port = count($app['containers']) + 4000;
    $command = array('docker', 'run', '-d', '-p', $port.':3306', $pos);

    $process = ProcessBuilder::create($command)->getProcess();
    $process->mustRun();

    $container  = array(
        'image' => $image,
        'port'  => $port,
        'id'    => trim($process->getOutput()),
    );
    $containers = $app['containers'];
    $containers[] = $container;

    file_put_contents($app['containers.file'], json_encode($containers));

    return json_encode($container);
});

$app->run();
