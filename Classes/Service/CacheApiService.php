<?php
namespace BERGWERK\BwrkCacheCleaner\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Georg Dümmler <gd@bergwerk.ag>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 *
 * @author    Georg Dümmler <gd@bergwerk.ag>
 * @package    TYPO3
 * @subpackage    bwrk_cache_cleaner
 ***************************************************************/

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class CacheApiService
 * @package BERGWERK\BwrkCacheCleaner\Service
 */
class CacheApiService
{
    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandler;
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager
     */
    protected $objectManager;
    /**
     * @var \TYPO3\CMS\Install\Service\ClearCacheService
     */
    protected $installToolClearCacheService;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dataHandler = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\DataHandling\\DataHandler');
        $this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $this->installToolClearCacheService = GeneralUtility::makeInstance('TYPO3\\CMS\\Install\\Service\\ClearCacheService');

        // Create a fake admin user
        $adminUser = $this->objectManager->get('TYPO3\\CMS\\Core\\Authentication\\BackendUserAuthentication');
        $adminUser->user['uid'] = $GLOBALS['BE_USER']->user['uid'];
        $adminUser->user['username'] = '_CLI_lowlevel';
        $adminUser->user['admin'] = 1;
        $adminUser->workspace = 0;
        $this->dataHandler->start(Array(), Array(), $adminUser);
    }

    /**
     * Clear all caches.
     *
     * @param bool $hard
     * @return void
     */
    public function clearAllCaches($hard = FALSE)
    {
        if (!$hard) {
            $this->dataHandler->clear_cacheCmd('all');
        } else {
            GeneralUtility::rmdir(PATH_site . 'typo3temp/Cache', TRUE);

            $cacheManager = new \TYPO3\CMS\Core\Cache\CacheManager();
            $cacheManager->setCacheConfigurations($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']);
            new \TYPO3\CMS\Core\Cache\CacheFactory('production', $cacheManager);

            $cacheManager->flushCaches();
        }
    }

    /**
     * Clear the cache per page
     *
     * @param int $uid
     */
    public function clearCacheFromPage($uid)
    {
       $this->dataHandler->clear_cacheCmd($uid);
    }

    /**
     * Use this to clear the Page Cache.
     *
     * @return void
     */
    public function clearPageCache()
    {
        $this->dataHandler->clear_cacheCmd('pages');
    }

    /**
     * Use this to clear the configuration cache.
     *
     * @return void
     */
    public function clearConfigurationCache()
    {
        $this->dataHandler->clear_cacheCmd('temp_cached');
    }

    /**
     * Use this to clear the system cache
     *
     * @return void
     */
    public function clearSystemCache()
    {
        $this->dataHandler->clear_cacheCmd('system');
    }

    /**
     * Use this to clear the opcode cache.
     *
     * @param string|NULL $fileAbsPath
     * @return void
     */
    public function clearAllActiveOpcodeCache($fileAbsPath = NULL)
    {
        $this->clearAllActiveOpcodeCacheWrapper($fileAbsPath);
    }

    /**
     * Clear all caches except the page cache.
     *
     * @return array with list of cleared caches
     */
    public function clearAllExceptPageCache()
    {
        $out = array();
        $cacheKeys = array_keys($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']);
        $ignoredCaches = array('cache_pages', 'cache_pagesection');
        $toBeFlushed = array_diff($cacheKeys, $ignoredCaches);
        /** @var \TYPO3\CMS\Core\Cache\CacheManager $cacheManager */
        $cacheManager = $GLOBALS['typo3CacheManager'];
        foreach ($cacheKeys as $cacheKey) {
            if ($cacheManager->hasCache($cacheKey)) {
                $out[] = $cacheKey;
                $singleCache = $cacheManager->getCache($cacheKey);
                $singleCache->flush();
            }
        }
        return $toBeFlushed;
    }

    /**
     * Use this to clear the opcode cache.
     *
     * @param string|NULL $fileAbsPath
     * @return void
     */
    protected function clearAllActiveOpcodeCacheWrapper($fileAbsPath)
    {
        if(version_compare(TYPO3_version, '7.4.0', '>='))
        {
            /** @var \TYPO3\CMS\Core\Service\OpcodeCacheService $opcodeCacheService */
            $opcodeCacheService = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Service\\OpcodeCacheService');
            $opcodeCacheService->clearAllActive($fileAbsPath);
        } else {
            \TYPO3\CMS\Core\Utility\OpcodeCacheUtility::clearAllActive($fileAbsPath);
        }
    }
}