<?php
/**
 * Configuration provider for Partial API Response
 */
namespace PartialResponse;

use Zend\Mvc\MvcEvent;
use Zend\Mvc\ModuleRouteListener;

class Module
{
    /**
     * To get the module configuartion
     *
     * @return array 
     */   
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * To get the autoloader configuartion
     *
     * @return array
     */  
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }



    /**
     * Attach a event listener
     * @param object $ev event's object
     * @return void
     */
    public function onBootstrap(MvcEvent $ev) {
        $sm = $ev->getApplication()->getServiceManager();
        $eventManager = $ev->getApplication()->getEventManager();
        
        // get the api-Response listener service
        $apiResponseListener = $sm->get('ApiResponseListener');
        // attach the listeners to the event manager
        $eventManager->attach($apiResponseListener);
    }
} 