<?php
namespace ImmoNova\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CacheImageListener {
    protected $cacheManager;

    public function __construct($cacheManager) {
        $this->cacheManager = $cacheManager;
    }

    public function postUpdate(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        dump($entity);

        if ($entity instanceof UploadedFile) {
            // clear cache of thumbnail
            $this->cacheManager->remove($entity->getUploadDir());
        }
    }

// when delete entity so remove all thumbnails related
    public function preRemove(LifecycleEventArgs $args) {
        $entity = $args->getEntity();

        if ($entity instanceof UploadedFile) {
//            $this->cacheManager->resolve($this->request, $entity->getPath(), $filter);
            $this->cacheManager->remove($entity->getWebPath());
        }
    }

}