<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/cache-analyzer.
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\CacheAnalyzer\Persistence;

use StefanFroemken\CacheAnalyzer\Domain\Model\CacheExpression;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DataMapper
{
    /**
     * @return CacheExpression[]
     */
    public function map(array $rows): array
    {
        $cacheExpressions = [];
        foreach ($rows as $row) {
            $cacheExpressions[] = $this->mapSingleRow($row);
        }
        return $cacheExpressions;
    }

    protected function mapSingleRow(array $row)
    {
        $cacheExpression = $this->createEmptyCacheExpression();
        $this->thawProperties($cacheExpression, $row);

        return $cacheExpression;
    }

    protected function thawProperties(CacheExpression $cacheExpression, array $row): void
    {
        foreach ($row as $columnName => $value) {
            $propertyName = GeneralUtility::underscoredToLowerCamelCase($columnName);
            $setterName = 'set' . ucfirst($propertyName);
            if (method_exists($cacheExpression, $setterName)) {
                $cacheExpression->$setterName($value);
            }
        }
    }

    protected function createEmptyCacheExpression(): object
    {
        $cacheExpression = GeneralUtility::makeInstance(CacheExpression::class);
        if (is_callable($callable = [$cacheExpression, 'initializeObject'])) {
            $callable();
        }

        return $cacheExpression;
    }
}
