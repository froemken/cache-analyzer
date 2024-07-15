<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/cache-analyzer.
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\CacheAnalyzer\Tests\Unit\Domain\Model;

use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function getTitleInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getTitle(),
        );
    }

    #[Test]
    public function setTitleSetsTitle(): void
    {
        $this->subject->setTitle('TYPO3');

        self::assertSame(
            'TYPO3',
            $this->subject->getTitle(),
        );
    }

    #[Test]
    public function getIsRegexpInitiallyReturnsFalse(): void
    {
        self::assertFalse(
            $this->subject->isRegexp(),
        );
    }

    #[Test]
    public function setIsRegexpSetsRegexp(): void
    {
        $this->subject->setIsRegexp(1);

        self::assertTrue(
            $this->subject->isRegexp(),
        );
    }

    #[Test]
    public function getIsThrowExceptionInitiallyReturnsFalse(): void
    {
        self::assertFalse(
            $this->subject->isThrowException(),
        );
    }

    #[Test]
    public function setIsThrowExceptionSetsThrowException(): void
    {
        $this->subject->setThrowException(1);

        self::assertTrue(
            $this->subject->isThrowException(),
        );
    }

    #[Test]
    public function getIsThrowExceptionFeOnlyInitiallyReturnsTrue(): void
    {
        self::assertTrue(
            $this->subject->isThrowExceptionFeOnly(),
        );
    }

    #[Test]
    public function setIsThrowExceptionFeOnlySetsThrowExceptionFeOnly(): void
    {
        $this->subject->setThrowExceptionFeOnly(0);

        self::assertFalse(
            $this->subject->isThrowExceptionFeOnly(),
        );
    }

    #[Test]
    public function getExpressionInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getExpression(),
        );
    }

    #[Test]
    public function setExpressionSetsExpression(): void
    {
        $this->subject->setExpression('TYPO3');

        self::assertSame(
            'TYPO3',
            $this->subject->getExpression(),
        );
    }

    #[Test]
    public function getCacheConfigurationsInitiallyReturnsEmptyArray(): void
    {
        self::assertSame(
            [],
            $this->subject->getCacheConfigurations(),
        );
    }

    #[Test]
    public function setCacheConfigurationsSetsCacheConfigurations(): void
    {
        $this->subject->setCacheConfigurations('runtime, extbase');

        self::assertSame(
            [
                'runtime',
                'extbase',
            ],
            $this->subject->getCacheConfigurations(),
        );
    }
}
