<?php

/**
 * Static and dynamic routing extensions for ffcms.
 * 1. In section of @Alias you can use "static aliasing" for single-targeted uri. As example, if you want to make alias
 * for '/content/list/page/about' item on uri '/about' in 'Front' env you can add it in section "Alias" -> "Front" as array 'target' => 'source'
 */

return [
    'Alias' => [
        'Front' => [
            '/about' => '/content/read/news/ffcms'
        ],
        /**'Admin' => [
            '/test' => '/main/settings'
        ]*/
    ],
    'Callback' => [
        'Front' => [
            '/demo' => '\\Apps\\Controller\\Front\\Main'
        ]
    ]
];