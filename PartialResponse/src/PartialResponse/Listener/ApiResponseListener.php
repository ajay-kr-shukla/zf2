<?php
/**
 * Attach API Response listeners on events 
 */
namespace PartialResponse\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

class ApiResponseListener extends AbstractListenerAggregate {

    private $_listeners = array();

    private $_service;

    private $_param = array();

    private $_start = '';



    /**
     * Attach listners
     * @param EventManagerInterface $events 
     * @return void          
     */
    public function attach(EventManagerInterface $events) {
        
        $this->_listeners[] = $events->attach(
            MvcEvent::EVENT_FINISH, array($this, 'getPartialResponse')
        );
    }

    /**
     * Return Partial api response from the called event
     * Formatting api response json 
     * @param MvcEvent $event  
     * 
     * @return void
     */
    public function getPartialResponse(MvcEvent $event){
        $queryParams = $event->getApplication()->getRequest()->getQuery()->toArray();
        if(isset($queryParams['fields'])){
            $queryParamString = array_flip(explode(',', str_replace(' ', '', $queryParams['fields'])));
            $response = $event->getApplication()->getResponse()->getContent();
            $jsonContent = json_decode($response, true);
            $tempJson = [];
            foreach($jsonContent['items'] as $key => $value) {
                $tempJson[] = array_intersect_key($value, $queryParamString);
            }
            $event->getResponse()->setContent(json_encode($tempJson));
        }
    }   
}