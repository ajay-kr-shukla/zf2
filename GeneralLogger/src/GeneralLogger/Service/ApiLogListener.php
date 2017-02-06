<?php

/**
 * Attach listeners on events 
 *
 * PHP version 5.5
 *
 * Declare Service Namespace
 *
 * @uses AppLogger\Service
 */

namespace GeneralLogger\Service;

use Zend\Log\Logger;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

/**
 * User validation service
 *
 * @category    ApiLogListener
 *
 * @package     AppLogger
 *
 * @author      Beachbody Digital
 *
 * @version     Version 1.0
 *
 * @copyright   (c) 2016, Beachbody Digital
 *
 * @uses        ZF\ApiProblem\ApiProblem
 *
 * @uses        CoreFramework\Service\CoreService
 * 
 * @uses        BBModule\Utilities\UtilsHelper
 */
class ApiLogListener extends AbstractListenerAggregate {

    private $_listeners = array();
    private $_service;
    private $_param = array();


    /**
     * Set serviceManager instance
     *
     * @param object $service service manager instance
     *            
     * @return void
     */
    public function __construct($service) {
        // We store the service from the service manager
        $this->_service = $service;
        $this->objListener = $this->_service->get('captureLog');
        $this->config = $this->_service->get('config');
    }

    /**
     * Attach listners
     *
     * @param EventManagerInterface $events 
     * 
     * @return void          
     */
    public function attach(EventManagerInterface $events) {

        // The AbstractListenerAggregate, allows us to attach our event listeners
        $this->_listeners[] = $events->attach(
                MvcEvent::EVENT_ROUTE, array(
            $this,
            'getApiLog'
                )
        );

        $this->_listeners[] = $events->attach(
                MvcEvent::EVENT_FINISH, array(
            $this,
            'getGeneralLogResponse'
                ), 1000
        );
    }

    /**
     * Get api response from the called event
     *
     * @param MvcEvent $event  
     * 
     * @return void
     */
    public function getGeneralLogResponse(MvcEvent $event) {
        $loggerType = $this->config['log_flag'];
        if (!empty($this->_param['method'])) {
            if($loggerType['api_log'] == true) {
                $this->objListener->generalApiLog($event);
            }
            if($loggerType['query_log'] == true) {
                $this->objListener->generalQueryLog($event);
            }
            if($loggerType['stack_trace_log'] == true) {
                $this->objListener->generalStackTraceLog($event);
            }
        }
    }

    /**
     * Get Cache from the called event
     *
     * @param MvcEvent $event 
     * 
     * @return void          
     */
    public function getApiLog(MvcEvent $event) {
        $this->_param = array(
            'method' => $event->getApplication()->getRequest()->getMethod(),
            'uri' => $event->getApplication()->getRequest()->getUriString(),
        );
    }

}
