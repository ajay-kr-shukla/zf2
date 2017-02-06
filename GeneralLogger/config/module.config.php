<?php

/**
 * Contains all specific configuration for API Logger
 *
 * PHP version 5.5
 *
 * @category Configuration
 * @package  AppLogger
 * @author   Beachbody Digital
 * @license  http://www.beachbody.com, Beachbody, LLC.
 * @link     {}
 */
return array(
    'debugMode' => true,
    'debugFileName' => array(
        'api_log' => 'api_log.txt',
        'query_log' => 'query_log.txt',
        'stack_trace_log' => 'stack_trace_log.txt',
    ),
    'log_flag' => array(
        'api_log' => true,
        'query_log' => true,
        'stack_trace_log' => true,
    ),
    'log_paths' => array(
        'api_log' => './log/api_log/'
    ),
    'service_manager' => array(
        'factories' => array(
            'ApiLogListener' => 'GeneralLogger\Service\ApiLogListenerFactory'
        )
    )
);
