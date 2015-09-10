# Cache Cleaner


## Installation

1. Install the extension.
2. Create "_cli_lowlevel" User

### Create "_cli_lowlevel" User

You have to create a backend-user named "_cli_lowlevel".
The password doesn't matter, but you have to give the user the following TSconfig:

options.clearCache.all=1
options.clearCache.pages=1


## How to use the Cache Cleaner API

### Via PHP

```php

// First Instance the CacheApiService

$cacheCleaner = new BERGWERK\BwrkCacheCleaner\Service\CacheApiService();

// Call one of the following options to clear the cache.

$cacheCleaner->clearAllCaches();              // Clear All Caches. Parameter true for hard delete
$cacheCleaner->clearPageCache();              // Clears the page cache.
$cacheCleaner->clearConfigurationCache();     // Clears the configuration cache.
$cacheCleaner->clearSystemCache();            // Clears the system cache
$cacheCleaner->clearAllActiveOpcodeCache();   // Clears all active opcode caches
$cacheCleaner->clearAllExceptPageCache();     // Clears all except the page cache
```


### Via SSH

Call the cli dispatcher via ssh with the following command:

```php
./typo3/cli_dispatch.phpsh extbase cache:run
```


Also you can add one of the following parameters: page, system, configuration, opc, except_page

If you doesn't add any Parameter, it will clear all caches hard.

Example for clear the page Cache via cli:

```php
./typo3/cli_dispatch.phpsh extbase cache:run page
```