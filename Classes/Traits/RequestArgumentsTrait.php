<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/cache-analyzer.
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\CacheAnalyzer\Traits;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ServerRequestFactory;

/**
 * Trait to get Merged Request Arguments. Used for replacing the old _GP array.
 */
trait RequestArgumentsTrait
{
    public function getGPValue(string $key): ?string
    {
        $request = $this->getServerRequest();

        return $request->getParsedBody()[$key] ?? $request->getQueryParams()[$key];
    }

    /**
     * @return array<mixed>
     */
    public function getGetArguments(): array
    {
        return $this->getServerRequest()->getQueryParams();
    }

    /**
     * @return array<mixed>
     */
    public function getPostArguments(): array
    {
        return $this->getServerRequest()->getParsedBody() ?? [];
    }

    /**
     * @return array<mixed>
     */
    public function getMergedPostAndGetValues(): array
    {
        $request = $this->getServerRequest();

        return array_merge($request->getQueryParams(), $request->getParsedBody());
    }

    private function getServerRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'] ?? ServerRequestFactory::fromGlobals();
    }
}
