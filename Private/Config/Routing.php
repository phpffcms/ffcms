<?php

/**
 * Static and dynamic routing extensions for ffcms.
 * 1. In section of @Alias you can use "static aliasing" for single-targeted uri. As example, if you want to make alias
 * for '/content/list/page/about' item on uri '/about' in 'Front' env you can add it in section "Alias" -> "Front" as array 'target' => 'source'
 * 2. In section of @Callback you can use "dynamic callback aliasing" for your own application controllers. As example, you can hook /page URI (you must use controller "Page" as a key)
 * to your own class "\Mynamespace\Space\MyApp" using key-value initiation: 'Page' => '\\Mynamespace\\Space\\MyApp'
 */

return [
    'Alias' => [
        'Front' => [
            null => '/content/read/news/ffcms'
        ],
        /**'Admin' => [
            '/test' => '/main/settings'
        ]*/
    ],
    'Callback' => [
        'Front' => [
            'Demo' => '\\Apps\\Controller\\Front\\Main'
        ]
    ]
];