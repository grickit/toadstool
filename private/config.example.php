<?php

return [
    // Branding
    'sitename' => 'toad.photos',
    'description' => 'Photo gallery for my pet toads plus their supporting flora and fauna.',
    'preferred_base_url' => 'https://toad.photos/',

    // Lots of footer settings, so group them
    'footer' => [
        // Who do all the photos on the site belong to?
        'copyright' => 'Derek Hoagland',

        // Change to do a miscellaneous advertisement or additional message
        'summary' => [
            'text' => 'High quality original photos available upon request and approval.',
            'url' => null
        ],

        // Effectively just a list of links
        'socials' => [
            'Twitter' => 'https://twitter.com/Grickit',
            'Instagram' => 'https://www.instagram.com/Grickit',
            'Github' => 'https://www.github.com/grickit'
        ]
    ],
    
    // Preview for linking the site on discord, facebook, et cetera
    'preview' => [
        'url' => 'https://toad.photos/',
        'image' => [
            'url' => 'https://toad.photos/resources/toadcuddles.jpeg',
            'mimetype' => 'image/jpeg',
            'width' => '2000',
            'height' => '1333',
            'caption' => 'Two of my pet toads cuddling under dripping water.',
        ]
    ],

    'storage' => [
        'classname' => '\Toadstool\AWSS3',
        'basepath' => 'dev.toadstool/images',
        'awss3' => [
            'endpoint' => '',
            'version' => 'latest',
            'region' => 'nyc3',
            'secret' => '',
            'key' => '',
            'bucket' => '',
        ]
    ]
];