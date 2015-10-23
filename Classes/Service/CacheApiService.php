<?php
namespace BERGWERK\BwrkCacheCleaner\Service;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CacheApiService {
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
    public function clearAllCaches($hard = FALSE) {
        if(!$hard)
        {
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
     * Clear the page cache.
     *
     * @return void
     */
    public function clearPageCache() {
        $this->dataHandler->clear_cacheCmd('pages');
    }
    /**
     * Clears the configuration cache.
     *
     * @return void
     */
    public function clearConfigurationCache() {
        $this->dataHandler->clear_cacheCmd('temp_cached');
    }
    /**
     * Clear the system cache
     *
     * @return void
     */
    public function clearSystemCache() {
        $this->dataHandler->clear_cacheCmd('system');
    }
    /**
     * Clears the opcode cache.
     *
     * @param string|NULL $fileAbsPath The file as absolute path to be cleared
     *                                 or NULL to clear completely.
     *
     * @return void
     */
    public function clearAllActiveOpcodeCache($fileAbsPath = NULL) {
        $this->clearAllActiveOpcodeCacheWrapper($fileAbsPath);
    }
    /**
     * Clear all caches except the page cache.
     * This is especially useful on big sites when you can't
     * just drop the page cache.
     *
     * @return array with list of cleared caches
     */
    public function clearAllExceptPageCache() {
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
     * Clears the opcode cache. This just wraps the static call for testing purposes.
     *
     * @param string|NULL $fileAbsPath The file as absolute path to be cleared
     *                                 or NULL to clear completely.
     *
     * @return void
     */
    protected function clearAllActiveOpcodeCacheWrapper($fileAbsPath) {
        \TYPO3\CMS\Core\Utility\OpcodeCacheUtility::clearAllActive($fileAbsPath);
    }
}