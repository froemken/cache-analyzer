<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/cache-analyzer.
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Functional\Domain\Repository;

use PHPUnit\Framework\Attributes\Test;
use StefanFroemken\CacheAnalyzer\Domain\Model\CacheExpression;
use StefanFroemken\CacheAnalyzer\Domain\Repository\CacheExpressionRepository;
use StefanFroemken\CacheAnalyzer\Persistence\DataMapper;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class CacheExpressionRepositoryTest extends FunctionalTestCase
{
    protected CacheExpressionRepository $subject;

    protected array $testExtensionsToLoad = [
        'stefanfroemken/cache-analyzer',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new CacheExpressionRepository(
            new DataMapper(),
            $this->createMock(Logger::class)
        );
    }

    #[Test]
    public function getCacheExpressionRecordsWillReturnEmptyItemsArray(): void
    {
        self::assertSame(
            [],
            $this->subject->getCacheExpressionRecords(),
        );
    }

    #[Test]
    public function getCacheExpressionRecordsWillMapRecordsToCacheExpressionArray(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/tx_cacheanalyzer_expression.csv');

        $cacheExpressions = $this->subject->getCacheExpressionRecords();

        // Just 2 as the first record has no title and will not be taken into account
        self::assertCount(
            2,
            $cacheExpressions,
        );

        foreach ($cacheExpressions as $cacheExpression) {
            self::assertInstanceOf(
                CacheExpression::class,
                $cacheExpression
            );
        }
    }
}
