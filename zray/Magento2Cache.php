<?php
namespace Magento2Cache;

class Magento2Cache
{
    private $cacheKeyMap = array();
    
    public function cacheKeyMapper($context) {
        $identifier = $context['functionArgs'][0];
        $return = $context['returnValue'];
        
        $this->cacheKeyMap[$identifier] = $return;
    }
    
    public function cacheLoad($context, &$storage)
    {
        $identifier = $context['functionArgs'][0];
        $cacheKey = $this->cacheKeyMap[strtoupper($identifier)];
        
        $accessProxy = $context['this'];
        
        $metadatas = $accessProxy->getBackend()->getMetadatas($cacheKey);
        if (!$metadatas) return;
        
        $expire = ($metadatas['expire'] > time() * 3600 * 24 * 365 * 5) ? date('Y-m-d H:i:s', $metadatas['expire']) : 'never';
        $metadatas['expire formatted'] = $expire;
        $metadatas['mtime formatted'] = date('Y-m-d H:i:s', $metadatas['mtime']);
        
        $storage['cacheLoad'][$identifier] = array_merge(
            array('cache_key' => $cacheKey),
            array('content' => $this->getCacheData($context['returnValue'])),
            $metadatas
        );
    }
    
    public function cacheSave($context, &$storage)
    {
        $cacheItem = array();
        
        $cacheItem['data'] = $this->getCacheData($context['functionArgs'][0]);
        $cacheItem['identifier'] = $context['functionArgs'][1];
        $cacheItem['tags'] = $context['functionArgs'][2];
        $cacheItem['lifeTime'] = $context['functionArgs'][3];
        
        $storage['cacheSave'][$cacheItem['identifier']] = array($cacheItem);
    }
    
    private function getCacheData($data) {
        try {
            $data = unserialize($data);
        }
        catch (\Exception $e) {
            $data = htmlspecialchars($data);
        }
        
        return $data;
    }
}