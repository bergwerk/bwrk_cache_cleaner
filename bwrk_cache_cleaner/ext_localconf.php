<?php
	if (TYPO3_MODE=='BE')    {
	    // Setting up scripts that can be run from the cli_dispatch.phpsh script.
	    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['cliKeys'][$_EXTKEY] = array('EXT:bwrk_cache_cleaner/cli/cleaner_cli.php', '_CLI_lowlevel');
	}
?>