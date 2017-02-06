<?php

/**
 * Capture the api log in a file
 *
 * PHP version 5.5
 *
 * Declare Lib Namespace
 *
 * @uses AppLogger\Lib
 */

namespace GeneralLogger\Lib;

use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Log\Logger;
use ZF\Rest\AbstractResourceListener;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class CaptureLog for logging
 *
 * @category    CaptureLog
 *
 * @package     AppLogger
 *
 * @author      Beachbody Digital
 *
 * @version     Version 1.0
 *
 * @copyright   (c) 2016, Beachbody Digital
 *
 * @uses        Zend\Log\Logger;
 *
 * @uses        Zend\Http\PhpEnvironment\RemoteAddress
 */
class CaptureLog extends AbstractResourceListener implements ServiceLocatorAwareInterface
{

    private $_serviceLocator;
    private $_param = array();
    private $_start = '';
    private $_dir = '';
    private $_file = '';
    private $_log = '';

    /**
     * Set serviceManager instance
     *
     * @param ServiceLocatorInterface $serviceLocator set service manager instance
     *            
     * @return void
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->_serviceLocator = $serviceLocator;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->_serviceLocator;
    }

    /**
     * Fetch log Folder path
     *
     * @param string $param log name
     * 
     * @return string logPath      
     */
    private function getCurrentLogPath()
    {
        $config = $this->_serviceLocator->get('config');
        $this->_dir = $config['log_paths']['api_log'] . date('d-m-y');
        if (!is_dir($this->_dir)) {
            mkdir($this->_dir, 0777);
        }
        return $this->_dir;
    }

    /**
     * Fetch log file path
     *
     * @param string $param log name
     * 
     * @return string logPath      
     */
    private function getCurrentLogFile($file)
    {
        $config = $this->_serviceLocator->get('config');
        $this->_file = $config['debugFileName'][$file];
        return $this->_file;
    }

    /**
     * Get api Log  from the called Method
     * To capture the api-log in log file
     * 
     * @param object  $event Event Manager Object
     * 
     * @return void
     */
    public function generalApiLog($event)
    {
        /** API Logger starts * */
        $this->_log = new Logger();
        $logPath = $this->getCurrentLogPath();
        $logFile = $this->getCurrentLogFile('api_log');
        $filePath = $logPath . '/' . $logFile;
        $writer = new \Zend\Log\Writer\Stream($filePath);
        $logger = $this->_log->addWriter($writer);

        $remote = new RemoteAddress();
        $match = $event->getRouteMatch();
        if ($match->getParam('controller')) {
            $controller = $match->getParam('controller');
        }
        
        $methodName = $event->getApplication()->getRequest()->getMethod();
        if($methodName == 'GET' || $methodName == 'DELETE') {
            $requestData = $event->getRequest()->getQuery()->toArray();
        } else {
            $requestData = $event->getRequest()->getPost()->toArray();
        }
         
        $this->_param = array(' Api Logger Info',
            'ZF Version         => '. \Zend\Version\Version::VERSION,
            'Request Time       => '.date('Y-m-d H:i:s'),
            'IP                 => '.$remote->getIpAddress(),
            'Status Code        => '.$event->getResponse()->getStatusCode(),
            'Method Name        => '.$methodName,
            'API Url            => '.$event->getApplication()->getRequest()->getUriString(),
            'controller         => '.$controller,
            'Header Data        => '.json_encode($event->getApplication()->getRequest()->getHeaders()),
            'Request Data       => '.json_encode($requestData),
            'Response Data      => '.$event->getResponse()->getContent(),
            '',
        );
        $logger->log(\Zend\Log\Logger::INFO, implode("\n", $this->_param));
        // Save Api log into file
        /** API Logger Ends * */
    }

    /**
     * Get Query Log  from the called Method
     * 
     * @return void
     */
    public function generalQueryLog($event)
    {
        /** API Query Logger starts * */
        $profiler = $this->_serviceLocator->get('dbPdoAdapter');

        $this->_log = new Logger();
        $logPath = $this->getCurrentLogPath();
        $logFile = $this->getCurrentLogFile('query_log');
        $filePath = $logPath . '/' . $logFile;
        $writer = new \Zend\Log\Writer\Stream($filePath);
        $logger = $this->_log->addWriter($writer);

        $queryProfiles = $profiler->getProfiler()->getProfiles();
        $queries[] = ' ----- Query Logger Info -------';
        $i = 1;
        foreach ($queryProfiles as $key => $row) {
            if (is_object($row['parameters'])) {
                $parameters = $row['parameters']->getNamedArray();
            } else {
                $parameters = $row['parameters'];
            }

            $query = $row['sql'];
            foreach ($parameters as $whereKey => $whereValue) {
                $whereValue = (!is_int($whereValue)) ? "'" . $whereValue . "'" : $whereValue;
                $whereKey = (strpos($whereKey, ':') === false) ? $whereKey = ":" . $whereKey : $whereKey;
                $query = str_replace($whereKey, $whereValue, $query);
            }
            $queries[] = '('.$i++.') '.$query;
        }
        $queries[] = '';
        $logger->log(\Zend\Log\Logger::INFO, implode("\n", $queries));
        /** API Query Logger Ends * */
    }

    /**
     * Get Stack Trace Log  from the called Method
     * 
     * @return void
     */
    public function generalStackTraceLog($event)
    {
        
        /** General Stack Trace  Logger starts * */
        $stackTrace[] = '--------- Stack Trace App Info -----------';
        
        $infoArry = explode('\\', $event->getRouteMatch()->getParam('controller'));
        
        $stackTrace[] = 'Route Name         => ' .$event->getRouteMatch()->getMatchedRouteName();
        $stackTrace[] = 'Module Name        => ' .$infoArry[0];
        $stackTrace[] = 'Resourse Name      => ' .$infoArry[0].'Resource.php';
        $stackTrace[] = 'Api Action Name    => ' .$event->getRouteMatch()->getParam('action');
        $stackTrace[] = 'API-Version        => ' .$infoArry[1];

        $stackTrace[] = '';
        
        $exception = new \Exception();
        $stackTrace[] = $exception->getTraceAsString();
        
        $this->_log = new Logger();
        $logPath = $this->getCurrentLogPath();
        $logFile = $this->getCurrentLogFile('stack_trace_log');
        $filePath = $logPath . '/' . $logFile;
        $writer = new \Zend\Log\Writer\Stream($filePath);
        $logger = $this->_log->addWriter($writer);
        $logger->log(\Zend\Log\Logger::INFO, implode("\n", $stackTrace));
        /**  General Stack Trace  Logger Ends * */
    }

}

?>