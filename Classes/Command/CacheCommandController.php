<?php

namespace BERGWERK\BwrkCacheCleaner\Command;

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

use BERGWERK\BwrkCacheCleaner\Domain\Model\LogEntry;
use BERGWERK\BwrkCacheCleaner\Service\CacheApiService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Class CacheCommandController
 * @package BERGWERK\BwrkCacheCleaner\Command
 */
class CacheCommandController extends CommandController
{

    /** @var  CacheApiService */
    protected $cacheApiService;

    /**
     * @var \BERGWERK\BwrkCacheCleaner\Domain\Repository\LogEntryRepository
     * @inject
     */
    protected $logEntryRepository;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cacheApiService = GeneralUtility::makeInstance('BERGWERK\\BwrkCacheCleaner\\Service\\CacheApiService');
    }

    /**
     * @param string $type
     */
    public function runCommand($type = '')
    {
        if ($type == 'page') {
            $this->cacheApiService->clearPageCache();

            echo "Page cache cleared successfully!\n";
        } else if ($type == 'system') {
            $this->cacheApiService->clearSystemCache();

            echo "System cache cleared successfully!\n";
        } else if ($type == 'configuration') {
            $this->cacheApiService->clearConfigurationCache();

            echo "Configuration cache cleared successfully!\n";
        } else if ($type == 'opc') {
            $this->cacheApiService->clearAllActiveOpcodeCache();

            echo "OPC cache cleared successfully!\n";
        } else if ($type == 'except_page') {
            $this->cacheApiService->clearAllExceptPageCache();

            echo "All caches except page cache cleared successfully!\n";
        } else {
            $this->clearCache();

            echo "All caches cleared successfully!\n";
        }
    }

    /**
     * @param int $uid
     */
    public function clearCachePerPage($uid)
    {
        $this->cacheApiService->clearCacheFromPage($uid);
    }

    /**
     * Clear Cache
     */
    private function clearCache()
    {
        $this->cacheApiService->clearAllActiveOpcodeCache();
        $this->cacheApiService->clearSystemCache();
        $this->cacheApiService->clearConfigurationCache();
        $this->cacheApiService->clearPageCache();

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
