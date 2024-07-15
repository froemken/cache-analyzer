<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/cache-analyzer.
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\CacheAnalyzer\Tests\Unit\Domain\Model;

use StefanFroemken\CacheAnalyzer\Domain\Model\CacheExpression;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class CacheExpressionTest extends UnitTestCase
{
    protected CacheExpression $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new CacheExpression();
    }

    public function testGetTitleInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getTitle(),
        );
    }

    public function testSetTitleSetsTitle(): void
    {
        $this->subject->setTitle('TYPO3');

        self::assertSame(
            'TYPO3',
            $this->subject->getTitle(),
        );
    }
}
