<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'BERGWERK\BwrkCacheCleaner\Command\CacheCommandController';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['\TYPO3\CMS\Scheduler\Task\CachingFrameworkGarbageCollectionTask'] = array(
	'extension' => $_EXTKEY,
	'title' => 'LLL:EXT:' . $_EXTKEY . '/locallang.xlf:cachingFrameworkGarbageCollection.name',
	'description' => 'LLL:EXT:' . $_EXTKEY . '/locallang.xlf:cachingFrameworkGarbageCollection.description',
	'additionalFields' => '\TYPO3\CMS\Scheduler\Task\CachingFrameworkGarbageCollectionAdditionalFieldProvider'
);