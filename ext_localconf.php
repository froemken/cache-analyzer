<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

use Psr\Log\LogLevel;
use StefanFroemken\CacheAnalyzer\Hook\CachingFrameworkAnalyzerHook;
use TYPO3\CMS\Core\Utility\GeneralUtility;

call_user_func(static function (): void {
    // Create our own logger file
    if (!isset($GLOBALS['TYPO3_CONF_VARS']['LOG']['StefanFroemken']['CacheAnalyzer']['writerConfiguration'])) {
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['StefanFroemken']['CacheAnalyzer']['writerConfiguration'] = [
            LogLevel::INFO => [
                \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
                    'logFileInfix' => 'cache_analyzer',
                ],
            ],
        ];
    }

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/cache/frontend/class.t3lib_cache_frontend_variablefrontend.php']['set'][1720985328]
        = GeneralUtility::makeInstance(CachingFrameworkAnalyzerHook::class)->analyze(...);
});
