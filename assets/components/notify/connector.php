<?php
/**
 * Notify
 *
 * Copyright 2013-2015 by Bob Ray <http://bobsguides.com>
 *
 * @package notify
 * @var modX $modx
 */
$path =  dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
if (!(file_exists($path))) {
    $path = dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))) . '/config.core.php';
}
if (!(file_exists($path))) {
    die('config.core.php file not found');
}
require_once $path;
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';


$corePath = $modx->getOption('nf.core_path', NULL, $modx->getOption('core_path') . 'components/notify/');
/*require_once $corePath . 'model/notify/notify.class.php';
$modx->notify = new Notify($modx);*/

$modx->lexicon->load('notify:default');

/* handle request */
$path = $corePath . 'processors/';
$_SERVER['HTTP_MODAUTH'] = $modx->user->getUserToken($modx->context->get('key'));
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location'        => '',
));