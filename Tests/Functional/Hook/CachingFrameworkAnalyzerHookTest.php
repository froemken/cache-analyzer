<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/cache-analyzer.
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\CacheAnalyzer\Tests\Functional\Hook;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use StefanFroemken\CacheAnalyzer\Domain\Model\CacheExpression;
use StefanFroemken\CacheAnalyzer\Domain\Repository\CacheExpressionRepository;
use StefanFroemken\CacheAnalyzer\Hook\CachingFrameworkAnalyzerHook;
use StefanFroemken\CacheAnalyzer\Hook\Exception\PreventStoringFalseCacheEntryException;
use StefanFroemken\CacheAnalyzer\UserFunc\RegisteredCacheConfigurationsUserFunc;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\SysLog\Action\Cache;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class CachingFrameworkAnalyzerHookTest extends FunctionalTestCase
{
    protected CachingFrameworkAnalyzerHook $subject;

    protected CacheExpressionRepository|MockObject $cacheExpressionRepositoryMock;

    protected LoggerInterface|MockObject $loggerMock;

    protected FrontendInterface|MockObject $frontendMock;

    protected array $testExtensionsToLoad = [
        'stefanfroemken/cache-analyzer',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->cacheExpressionRepositoryMock = $this->createMock(CacheExpressionRepository::class);
        $this->loggerMock = $this->createMock(Logger::class);
        $this->frontendMock = $this->createMock(VariableFrontend::class);

        $this->subject = new CachingFrameworkAnalyzerHook(
            $this->cacheExpressionRepositoryMock,
            $this->loggerMock
        );
    }

    #[Test]
    public function analyzeWithMissingVariableWillNotLogError(): void
    {
        $parameters = [
            'entryIdentifier' => 'page_1_bla',
        ];

        $this->loggerMock
            ->expects(self::never())
            ->method('error');

        $this->subject->analyze($parameters, $this->frontendMock);
    }

    #[Test]
    public function analyzeWithMissingEntryIdentifierWillNotLogError(): void
    {
        $parameters = [
            'variable' => 'test',
        ];

        $this->loggerMock
            ->expects(self::never())
            ->method('error');

        $this->subject->analyze($parameters, $this->frontendMock);
    }

    #[Test]
    public function analyzeWithNonMatchingCacheExpressionsWillNotLogError(): void
    {
        $parameters = [
            'variable' => 'Cache entry for page 12',
            'entryIdentifier' => 'page_1_bla',
        ];

        $this->frontendMock
            ->expects(self::once())
            ->method('getIdentifier')
            ->willReturn('runtime');

        $cacheExpression = new CacheExpression();
        $cacheExpression->setTitle('strpos');
        $cacheExpression->setExpression('TYPO3');
        $cacheExpression->setIsRegexp(0);

        $cacheExpressions = [$cacheExpression];

        $this->cacheExpressionRepositoryMock
            ->expects(self::once())
            ->method('getCacheExpressions')
            ->willReturn($cacheExpressions);

        $this->loggerMock
            ->expects(self::never())
            ->method('error');

        $this->subject->analyze($parameters, $this->frontendMock);
    }

    #[Test]
    public function analyzeWithNonMatchingCacheConfigurationCacheExpressionsWillNotLogError(): void
    {
        $parameters = [
            'variable' => 'Cache entry for page 12',
            'entryIdentifier' => 'page_1_bla',
        ];

        $this->frontendMock
            ->expects(self::once())
            ->method('getIdentifier')
            ->willReturn('runtime');

        $cacheExpression = new CacheExpression();
        $cacheExpression->setTitle('strpos');
        $cacheExpression->setExpression('page');
        $cacheExpression->setIsRegexp(0);
        $cacheExpression->setCacheConfigurations('extbase', 'core');

        $cacheExpressions = [$cacheExpression];

        $this->cacheExpressionRepositoryMock
            ->expects(self::once())
            ->method('getCacheExpressions')
            ->willReturn($cacheExpressions);

        $this->loggerMock
            ->expects(self::never())
            ->method('error');

        $this->subject->analyze($parameters, $this->frontendMock);
    }

    #[Test]
    public function analyzeWithStrPosCacheExpressionsWillLogError(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest('https://www.example.com/'));

        $parameters = [
            'variable' => 'Cache entry for page 12',
            'entryIdentifier' => 'page_1_bla',
        ];

        $this->frontendMock
            ->expects(self::atLeastOnce())
            ->method('getIdentifier')
            ->willReturn('runtime');

        $cacheExpression = new CacheExpression();
        $cacheExpression->setTitle('strpos');
        $cacheExpression->setExpression('page');
        $cacheExpression->setIsRegexp(0);
        $cacheExpression->setCacheConfigurations('runtime');

        $cacheExpressions = [$cacheExpression];

        $this->cacheExpressionRepositoryMock
            ->expects(self::once())
            ->method('getCacheExpressions')
            ->willReturn($cacheExpressions);

        $this->loggerMock
            ->expects(self::once())
            ->method('error');

        $this->subject->analyze($parameters, $this->frontendMock);
    }

    #[Test]
    public function analyzeWithRegExpCacheExpressionsWillLogError(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest('https://www.example.com/'));

        $parameters = [
            'variable' => 'Cache entry for page 12',
            'entryIdentifier' => 'page_1_bla',
        ];

        $this->frontendMock
            ->expects(self::atLeastOnce())
            ->method('getIdentifier')
            ->willReturn('runtime');

        $cacheExpression = new CacheExpression();
        $cacheExpression->setTitle('regexp');
        $cacheExpression->setExpression('[0-9]{2}');
        $cacheExpression->setIsRegexp(1);
        $cacheExpression->setCacheConfigurations('runtime');

        $cacheExpressions = [$cacheExpression];

        $this->cacheExpressionRepositoryMock
            ->expects(self::once())
            ->method('getCacheExpressions')
            ->willReturn($cacheExpressions);

        $this->loggerMock
            ->expects(self::once())
            ->method('error');

        $this->subject->analyze($parameters, $this->frontendMock);
    }

    #[Test]
    public function analyzeWithArrayVariableCacheExpressionsWillLogError(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest('https://www.example.com/'));

        $parameters = [
            'variable' => ['page', '12', 'cache'],
            'entryIdentifier' => 'page_1_bla',
        ];

        $this->frontendMock
            ->expects(self::atLeastOnce())
            ->method('getIdentifier')
            ->willReturn('runtime');

        $cacheExpression = new CacheExpression();
        $cacheExpression->setTitle('regexp');
        $cacheExpression->setExpression('[0-9]{2}');
        $cacheExpression->setIsRegexp(1);
        $cacheExpression->setCacheConfigurations('runtime');

        $cacheExpressions = [$cacheExpression];

        $this->cacheExpressionRepositoryMock
            ->expects(self::once())
            ->method('getCacheExpressions')
            ->willReturn($cacheExpressions);

        $this->loggerMock
            ->expects(self::once())
            ->method('error');

        $this->subject->analyze($parameters, $this->frontendMock);
    }

    #[Test]
    public function analyzeWithThrowExceptionWillLogErrorAndThrowException(): void
    {
        $this->expectException(PreventStoringFalseCacheEntryException::class);

        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest('https://www.example.com/'))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE);

        $parameters = [
            'variable' => 'Cache entry for page 12',
            'entryIdentifier' => 'page_1_bla',
        ];

        $this->frontendMock
            ->expects(self::atLeastOnce())
            ->method('getIdentifier')
            ->willReturn('runtime');

        $cacheExpression = new CacheExpression();
        $cacheExpression->setTitle('regexp');
        $cacheExpression->setExpression('[0-9]{2}');
        $cacheExpression->setIsRegexp(1);
        $cacheExpression->setThrowException(1);
        $cacheExpression->setThrowExceptionFeOnly(1);
        $cacheExpression->setCacheConfigurations('runtime');

        $cacheExpressions = [$cacheExpression];

        $this->cacheExpressionRepositoryMock
            ->expects(self::once())
            ->method('getCacheExpressions')
            ->willReturn($cacheExpressions);

        $this->loggerMock
            ->expects(self::once())
            ->method('error');

        $this->subject->analyze($parameters, $this->frontendMock);
    }

    #[Test]
    public function analyzeWithThrowExceptionWillLogErrorAndThrowExceptionInBackend(): void
    {
        $this->expectException(PreventStoringFalseCacheEntryException::class);

        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest('https://www.example.com/typo3/'))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE);

        $parameters = [
            'variable' => 'Cache entry for page 12',
            'entryIdentifier' => 'page_1_bla',
        ];

        $this->frontendMock
            ->expects(self::atLeastOnce())
            ->method('getIdentifier')
            ->willReturn('runtime');

        $cacheExpression = new CacheExpression();
        $cacheExpression->setTitle('regexp');
        $cacheExpression->setExpression('[0-9]{2}');
        $cacheExpression->setIsRegexp(1);
        $cacheExpression->setThrowException(1);
        $cacheExpression->setThrowExceptionFeOnly(0);
        $cacheExpression->setCacheConfigurations('runtime');

        $cacheExpressions = [$cacheExpression];

        $this->cacheExpressionRepositoryMock
            ->expects(self::once())
            ->method('getCacheExpressions')
            ->willReturn($cacheExpressions);

        $this->loggerMock
            ->expects(self::once())
            ->method('error');

        $this->subject->analyze($parameters, $this->frontendMock);
    }

    #[Test]
    public function analyzeWithThrowExceptionWillLogErrorAndWillNotThrowExceptionInBackend(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest('https://www.example.com/typo3/'))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE);

        $parameters = [
            'variable' => 'Cache entry for page 12',
            'entryIdentifier' => 'page_1_bla',
        ];

        $this->frontendMock
            ->expects(self::atLeastOnce())
            ->method('getIdentifier')
            ->willReturn('runtime');

        $cacheExpression = new CacheExpression();
        $cacheExpression->setTitle('regexp');
        $cacheExpression->setExpression('[0-9]{2}');
        $cacheExpression->setIsRegexp(1);
        $cacheExpression->setThrowException(1);
        $cacheExpression->setThrowExceptionFeOnly(1);
        $cacheExpression->setCacheConfigurations('runtime');

        $cacheExpressions = [$cacheExpression];

        $this->cacheExpressionRepositoryMock
            ->expects(self::once())
            ->method('getCacheExpressions')
            ->willReturn($cacheExpressions);

        $this->loggerMock
            ->expects(self::once())
            ->method('error');

        $this->subject->analyze($parameters, $this->frontendMock);
    }
}
