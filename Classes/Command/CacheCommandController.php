<?php

namespace BERGWERK\BwrkCacheCleaner\Command;

use BERGWERK\BwrkCacheCleaner\Service\CacheApiService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

class CacheCommandController extends CommandController
{

    /** @var  CacheApiService */
    protected $cacheApiService;

    public function __construct()
    {
        $this->cacheApiService = GeneralUtility::makeInstance('BERGWERK\\BwrkCacheCleaner\\Service\\CacheApiService');
    }

    /**
     * @param bool|false $system
     */
    public function runCommand($system = false)
    {
        $this->clearCache($system);
    }

    private function clearCache($system)
    {
        $this->cacheApiService->clearAllActiveOpcodeCache();

        if($system == true)
        {
            $this->cacheApiService->clearSystemCache();
        }

        $this->cacheApiService->clearAllCaches(true);

    }
}
