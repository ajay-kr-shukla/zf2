<?php
/**
 * Module Config 
 * Load the ApiResponseListener from Listener
 */

return array(
    'service_manager' => array(
        'invokables' => array(
            'ApiResponseListener' => 'PartialResponse\Listener\ApiResponseListener'
        )
    )
);