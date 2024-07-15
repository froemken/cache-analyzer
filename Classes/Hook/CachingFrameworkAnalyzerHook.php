<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/cache-analyzer.
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\CacheAnalyzer\Hook;

use Psr\Log\LoggerInterface;
use StefanFroemken\CacheAnalyzer\Domain\Model\CacheExpression;
use StefanFroemken\CacheAnalyzer\Domain\Repository\CacheExpressionRepository;
use StefanFroemken\CacheAnalyzer\Hook\Exception\PreventStoringFalseCacheEntryException;
use StefanFroemken\CacheAnalyzer\Traits\RequestArgumentsTrait;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook to analyze the cache data just before it gets stored in Caching Framework
 */
class CachingFrameworkAnalyzerHook
{
    use RequestArgumentsTrait;

    public function __construct(
        readonly private CacheExpressionRepository $cacheExpressionRepository,
        readonly private LoggerInterface $logger,
    ) {}

    /**
     * Analyze the data. If it matches create a new log entry
     *
     * @param array{'entryIdentifier': string, "variable": string, "tags": array{mixed}, "lifetime": int} $parameters
     * @throws PreventStoringFalseCacheEntryException
     */
    public function analyze(array $parameters, VariableFrontend $frontend): void
    {
        // Don't spend further time, if these variables are empty
        if (!isset($parameters['variable'], $parameters['entryIdentifier'])) {
            return;
        }

        $cacheEntry = $parameters['variable'];

        // I know nothing about the datatype, structure or whatever in $variable.
        // IMO a string representation is a good start for analyzing: preg_match, strpos, ...
        if (!is_string($cacheEntry)) {
            $cacheEntry = json_encode($cacheEntry);
        }

        $matchingExpressions = $this->getExpressionsMatchingVariable(
            $cacheEntry,
            $frontend,
            $this->cacheExpressionRepository->getCacheExpressions(),
        );

        foreach ($matchingExpressions as $cacheExpression) {
            $this->createLogEntry(
                $parameters['entryIdentifier'],
                $frontend->getIdentifier(),
                $cacheExpression,
            );

            $this->throwExceptionIfConfigured($cacheExpression);
        }
    }

    /**
     * @throws PreventStoringFalseCacheEntryException
     */
    protected function throwExceptionIfConfigured(CacheExpression $cacheExpression): void
    {
        if ($cacheExpression->isThrowException()) {
            // throw exception in FE only or also in BE, if fe_only is disabled
            if (
                $cacheExpression->isThrowExceptionFeOnly() === false
                || (
                    $cacheExpression->isThrowExceptionFeOnly() === true
                    && ApplicationType::fromRequest($this->getServerRequest())->isFrontend()
                )
            ) {
                throw new PreventStoringFalseCacheEntryException(
                    '[cache_analyzer] CF logger prevents inserting invalid cache entry',
                    1720993875,
                );
            }
        }
    }

    /**
     * @param CacheExpression[] $cacheExpressions
     * @return CacheExpression[]
     */
    protected function getExpressionsMatchingVariable(
        string $cacheEntry,
        FrontendInterface $frontend,
        array $cacheExpressions,
    ): array {
        $matchingExpressions = [];
        foreach ($cacheExpressions as $cacheExpression) {
            if (!in_array($frontend->getIdentifier(), $cacheExpression->getCacheConfigurations(), true)) {
                continue;
            }

            try {
                if ($this->isVariableMatchingCacheExpression($cacheEntry, $cacheExpression)) {
                    $matchingExpressions[] = $cacheExpression;
                }
            } catch (\Exception $exception) {
                $this->logger->error(
                    '[cache_analyzer] Error occurred while analyzing cache entry: ' . $exception->getMessage(),
                );
            }
        }

        return $matchingExpressions;
    }

    protected function isVariableMatchingCacheExpression(string $variable, CacheExpression $cacheExpression): bool
    {
        if ($cacheExpression->isRegexp()) {
            if (preg_match(
                $cacheExpression->getExpression(),
                $variable,
            )) {
                return true;
            }
        } elseif (mb_strpos($variable, $cacheExpression->getExpression()) !== false) {
            return true;
        }

        return false;
    }

    protected function createLogEntry(string $entryIdentifier, string $cacheIdentifier, CacheExpression $cacheExpression): void
    {
        $context = [
            'entryIdentifier' => $entryIdentifier,
            'cacheIdentifier' => $cacheIdentifier,
            'cacheExpression' => $cacheExpression,
            'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5),
            'request' => GeneralUtility::getIndpEnv('_ARRAY'),
            'GET' => $this->getGetArguments(),
            'POST' => $this->getPostArguments(),
        ];

        // Yes, we log that as error. In most cases you have problems on LIVE/PRODUCTION where severities of info and
        // warning are not logged.
        $this->logger->error(
            '[cache_analyzer] Query Cache detection. A cache expression matches.',
            $context,
        );
    }
}
