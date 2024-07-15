<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/cache-analyzer.
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\CacheAnalyzer\Tests\Unit\Persistence;

use PHPUnit\Framework\Attributes\Test;
use StefanFroemken\CacheAnalyzer\Persistence\DataMapper;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class DataMapperTest extends UnitTestCase
{
    protected DataMapper $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new DataMapper();
    }

    #[Test]
    public function mapWithEmptyRowsWillReturnEmptyArray(): void
    {
        self::assertSame(
            [],
            $this->subject->map([]),
        );
    }

    #[Test]
    public function mapWithRowsWillReturnCacheExpressionArray(): void
    {
        $rows = [
            0 => [
                'title' => 'strpos expression',
                'is_regexp' => 0,
                'expression' => 'href=\"\"',
                'throw_exception' => 0,
                'throw_exception_fe_only' => 0,
                'cache_configurations' => 'core, extbase, di',
            ],
            1 => [
                'title' => 'reg exp expression',
                'is_regexp' => 1,
                'expression' => '[0-9]+',
                'throw_exception' => 1,
                'throw_exception_fe_only' => 1,
                'cache_configurations' => 'runtime',
            ],
        ];

        $cacheExpressions = $this->subject->map($rows);

        self::assertCount(
            2,
            $cacheExpressions,
        );

        $firstCacheExpression = $cacheExpressions[0];
        self::assertSame(
            'href=\"\"',
            $firstCacheExpression->getExpression(),
        );
        self::assertSame(
            ['core', 'extbase', 'di'],
            $firstCacheExpression->getCacheConfigurations(),
        );

        $secondCacheExpression = $cacheExpressions[1];
        self::assertTrue(
            $secondCacheExpression->isThrowException(),
        );
        self::assertTrue(
            $secondCacheExpression->isThrowExceptionFeOnly(),
        );
    }
}
