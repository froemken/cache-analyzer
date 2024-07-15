<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/cache-analyzer.
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\CacheAnalyzer\Tests\Functional\UserFunc;

use PHPUnit\Framework\Attributes\Test;
use StefanFroemken\CacheAnalyzer\UserFunc\RegisteredCacheConfigurationsUserFunc;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class RegisteredCacheConfigurationsUserFuncTest extends FunctionalTestCase
{
    protected RegisteredCacheConfigurationsUserFunc $subject;

    protected array $testExtensionsToLoad = [
        'stefanfroemken/cache-analyzer',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new RegisteredCacheConfigurationsUserFunc();
    }

    #[Test]
    public function getCacheConfigurationsWillReturnMoreThanFifeCacheConfigurations(): void
    {
        $parameters = [
            'items' => [],
        ];

        $this->subject->getCacheConfigurations($parameters);

        // Testing Framework comes with ~12 cache configurations.
        // So, 5 should be enough for testing
        self::assertGreaterThan(
            5,
            $parameters['items'],
        );

        $cacheConfigurationValues = array_column($parameters['items'], 'value');

        self::assertTrue(
            in_array('runtime', $cacheConfigurationValues),
        );
    }
}
