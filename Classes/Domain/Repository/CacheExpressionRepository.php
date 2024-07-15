<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/cache-analyzer.
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\CacheAnalyzer\Domain\Repository;

use Psr\Log\LoggerInterface;
use StefanFroemken\CacheAnalyzer\Domain\Model\CacheExpression;
use StefanFroemken\CacheAnalyzer\Persistence\DataMapper;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CacheExpressionRepository
{
    private const TABLE = 'tx_cacheanalyzer_expression';

    public function __construct(
        readonly private DataMapper $dataMapper,
        readonly private LoggerInterface $logger,
    ) {}

    /**
     * @return CacheExpression[]
     */
    public function getCacheExpressionRecords(): array
    {
        $cacheExpressions = [];
        if (($connectionPool = $this->getConnectionPool()) === null) {
            return [];
        }

        try {
            $queryBuilder = $connectionPool->getQueryBuilderForTable(self::TABLE);
            $statement = $queryBuilder
                ->select('*')
                ->from(self::TABLE)
                ->where(
                    $queryBuilder->expr()->neq('title', $queryBuilder->createNamedParameter('')),
                    $queryBuilder->expr()->neq('expression', $queryBuilder->createNamedParameter('')),
                )
                ->executeQuery();

            while ($cacheExpression = $statement->fetchAssociative()) {
                $cacheExpressions[] = $cacheExpression;
            }
        } catch (\Exception | \Doctrine\DBAL\Exception $exception) {
            $this->logger->error(
                sprintf(
                    'Error while querying table %s: %s',
                    self::TABLE,
                    $exception->getMessage(),
                )
            );
        }

        return $this->dataMapper->map($cacheExpressions);
    }

    /**
     * As long as boot process of TYPO3 is not completed, it is not allowed to
     * instantiate the ConnectionPool.
     *
     * See "ServiceProvider" in EXT:core with condition on: $container->get('boot.state')->complete
     *
     * As this repo will be called by a class called from ext_localconf.php, we are
     * in the boot process of TYPO3, where the configuration cache will just be build up.
     *
     * That's why this method (in this special case) can also return NULL.
     */
    protected function getConnectionPool(): ?ConnectionPool
    {
        try {
            return GeneralUtility::makeInstance(ConnectionPool::class);
        } catch (\LogicException $exception) {
        }

        return null;
    }
}
