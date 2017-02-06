<?php
/**
 * Configuration provider for API Logger
 *
 * PHP version 5.5
 *
 * Declare Service Namespace
 *
 * @uses AppLogger
 */
namespace GeneralLogger;

use Zend\Mvc\MvcEvent;
use Zend\Mvc\ModuleRouteListener;

/**
 * Module Class Doc Comment
 *
 * @category Class
 * @package  AppLogger
 * @author   Kanwar Pal <kanwar@osscube.com>
 * @license  http://www.beachbody.com, Beachbody, LLC.
 * @link     {}
 */

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
     * To get the service configuartion
     *
     * @return array
     */
    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                'captureLog' => 'GeneralLogger\Lib\CaptureLog'
            )
        );
    }

    /**
     * Attach a event listener
     *
     * @param object $ev event's object
     * 
     * @return void
     */
    public function onBootstrap(MvcEvent $ev)
    {
        $sm = $ev->getApplication()->getServiceManager();
        $eventManager = $ev->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $request = $ev->getApplication()->getRequest();
        $config = $sm->get('config');
        $uriString = $ev->getApplication()->getRequest()->getRequestUri();

        // Handle Api-Log
        if (isset($config['debugMode']) && ($config['debugMode'] == true)) {
            // get the api-log listener service
            $apiLogListener = $sm->get('ApiLogListener');
            // attach the listeners to the event manager
            $eventManager->attach($apiLogListener);
        } // End of Api-Log

    }
} 