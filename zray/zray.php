<?php
namespace Magento2Cache;

$zre = new \ZRayExtension('Magento2Cache');
$zre->setMetadata(array(
    'logo' => __DIR__ . DIRECTORY_SEPARATOR . 'logo.png',
    'actionsBaseUrl' => $_SERVER['REQUEST_URI'] 
));

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Magento2Cache.php';

$magento2Cache = new Magento2Cache();
$zre->setEnabledAfter('Magento\Framework\App\Cache\Type\FrontendPool::get');

$zre->traceFunction(
    'Magento\Framework\Cache\Core::_id',
    function() {},
    array(
        $magento2Cache,
        'cacheKeyMapper'
    )
);

$zre->traceFunction(
    'Magento\Framework\App\Cache\Type\AccessProxy::load',
    function() {},
    array(
        $magento2Cache,
        'cacheLoad'
    )
);

$zre->traceFunction(
    'Magento\Framework\App\Cache\Type\AccessProxy::save',
    function() {},
    array(
        $magento2Cache,
        'cacheSave'
    )
);

