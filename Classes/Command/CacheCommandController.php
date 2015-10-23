<?php

namespace BERGWERK\BwrkCacheCleaner\Command;

use BERGWERK\BwrkCacheCleaner\Domain\Model\LogEntry;
use BERGWERK\BwrkCacheCleaner\Service\CacheApiService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

class CacheCommandController extends CommandController
{

    /** @var  CacheApiService */
    protected $cacheApiService;

    /**
     * @var \BERGWERK\BwrkCacheCleaner\Domain\Repository\LogEntryRepository
     * @inject
     */
    protected $logEntryRepository;

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

    /**
     * @param int $uid
     */
    public function clearCachePerPage($uid)
    {
        $this->cacheApiService->clearCacheFromPage($uid);
    }

    /**
     * @param string $system
     */
    private function clearCache($system)
    {
        $this->cacheApiService->clearAllActiveOpcodeCache();

        if($system == true)
        {
            $this->cacheApiService->clearSystemCache();
        }

        $this->cacheApiService->clearAllCaches(false);
    }

    /**
     * @param $text
     */
    private function createLogEntry($text)
    {
        $logEntry = new LogEntry();
        $logEntry->setDetails($text);
        $logEntry->setTstamp(time());
        $logEntry->setType(3);
        $logEntry->setAction(1);
        $logEntry->setBackendUserUid(1);
        $this->logEntryRepository->add($logEntry);
    }
}
