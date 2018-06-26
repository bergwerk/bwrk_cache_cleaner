<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Cache Cleaner API',
    'description' => 'You can use this extension to clear your typo3-caches programmatically.',
    'category' => 'plugin',
    'author' => 'BERGWERK',
    'author_email' => 'technik@bergwerk.ag',
    'author_company' => 'BERGWERK Werbeagentur GmbH',
    'state' => 'stable',
    'version' => '1.5.4',
    'constraints' => array(
        'depends' => array(
            'typo3' => '6.2.0-7.99.99',
        ),
        'conflicts' => array(),
        'suggests' => array()
    )
);