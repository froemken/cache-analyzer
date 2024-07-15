<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/cache-analyzer.
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\CacheAnalyzer\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class CacheExpression
{
    protected string $title = '';

    protected bool $isRegexp = false;

    protected bool $throwException = false;

    protected bool $throwExceptionFeOnly = true;

    protected string $expression = '';

    /**
     * @var string[]
     */
    protected array $cacheConfigurations = [];

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function isRegexp(): bool
    {
        return $this->isRegexp;
    }

    public function setIsRegexp(int $isRegexp): void
    {
        $this->isRegexp = (bool)$isRegexp;
    }

    public function isThrowException(): bool
    {
        return $this->throwException;
    }

    public function setThrowException(int $throwException): void
    {
        $this->throwException = (bool)$throwException;
    }

    public function isThrowExceptionFeOnly(): bool
    {
        return $this->throwExceptionFeOnly;
    }

    public function setThrowExceptionFeOnly(int $throwExceptionFeOnly): void
    {
        $this->throwExceptionFeOnly = (bool)$throwExceptionFeOnly;
    }

    public function getExpression(): string
    {
        return $this->expression;
    }

    public function setExpression(string $expression): void
    {
        $this->expression = $expression;
    }

    /**
     * @return string[]
     */
    public function getCacheConfigurations(): array
    {
        return $this->cacheConfigurations;
    }

    public function setCacheConfigurations(string $cacheConfigurations): void
    {
        $this->cacheConfigurations = GeneralUtility::trimExplode(',', $cacheConfigurations, true);
    }
}
