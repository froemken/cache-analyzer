<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/cache-analyzer.
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\CacheAnalyzer\UserFunc;

class RegisteredCacheConfigurationsUserFunc
{
    public function getCacheConfigurations(array &$parameters, mixed $reference): void
    {
        foreach ($this->getRegisteredCacheConfigurations() as $registeredBackend) {
            $parameters['items'][] = [
                'label' => $registeredBackend,
                'value' => $registeredBackend,
            ];
        }

    }

    protected function getRegisteredCacheConfigurations(): array
    {
        $cacheConfigurations = $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'] ?? [];
        if (!is_array($cacheConfigurations)) {
            return [];
        }

        $registeredBackends = array_keys($cacheConfigurations);

        sort($registeredBackends);

        return $registeredBackends;
    }
}
