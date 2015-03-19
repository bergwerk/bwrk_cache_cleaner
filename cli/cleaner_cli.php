<?php

if (!defined('TYPO3_cliMode'))  die('You cannot run this script directly!');

// Include basis cli class
//require_once(PATH_t3lib.'class.t3lib_cli.php');
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class cleaner_cli extends t3lib_cli {

	/**
	 * Constructor
	 */
	function direct_mail_cli () {

		// Running parent class constructor
		parent::__construct();

		// Setting help texts:
		$this->cli_help['name'] = 'BERGWERK Cli-Cache-Cleaner';
		$this->cli_help['synopsis'] = '';
		$this->cli_help['description'] = '_cli_lowlevel Clear Cache';
		$this->cli_help['examples'] = '/.../cli_dispatch.phpsh clearCache';
		$this->cli_help['author'] = 'BERGWERK[GD], (c) 2014';
	}

	/**
	 * CLI engine
	 *
	 * @return    string
	 */
	function cli_main() {

		// get task (function)
		$task = (string)$this->cli_args['_DEFAULT'][1];

		if (!$task){
				$this->cli_validateArgs();
				$this->cli_help();
				exit;
		}

		if ($task == 'masssend') {
				$this->massSend();
		}

		/**
		 * Or other tasks
		 * Which task shoud be called can you define in the shell command
		 * /www/typo3/cli_dispatch.phpsh cli_example otherTask
		 */
		if ($task == 'clearCache') {
			$this->clearCache();
		}
	}

	/**
	 * myFunction which is called over cli
	 *
	 */
	function clearCache(){
		global $TYPO3_CONF_VARS;
		
		
		$t3lib_TCEmain = t3lib_div::makeInstance('t3lib_TCEmain');
		$t3lib_TCEmain->start(array(), array()); // Gegenmaßnahme zu "Fatal error: Call to a member function getTSConfigVal() on a non-object "
		$t3lib_TCEmain->clear_cacheCmd('all');  
		$t3lib_TCEmain->clear_cacheCmd('pages');  
		
		
		echo "Cache gelöscht!\n";
	}
}

// Call the functionality
/** @var $cleanerObj cleaner_cli */
$cleanerObj = t3lib_div::makeInstance('cleaner_cli');
$cleanerObj->cli_main($_SERVER['argv']);

?>