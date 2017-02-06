<?php
/**
 * A Factory that return the ApiLogListener's instance
 *
 * PHP version 5.5
 *
 * @category Service
 * @package  AppLogger\Service
 * @author   Kanwar Pal <kanwar@osscube.com>
 * @license  http://www.beachbody.com, Beachbody, LLC.
 * @link     {}
 */

namespace GeneralLogger\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ApiLogListenerFactory Class Doc Comment
 *
 * @category Class
 * @package  AppLogger\Service
 * @author   Kanwar Pal <kanwar@osscube.com>
 * @license  http://www.beachbody.com, Beachbody, LLC.
 * @link     {}
 */
class ApiLogListenerFactory implements FactoryInterface
{
    /**
     * Retrieve ApiLogListener instance
     * @param object $serviceLocator service manager instance
     * @return ApiLogListener object
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ApiLogListener($serviceLocator);
    }
}