<?php

namespace Aigisu\Components\Oauth;

use League\OAuth2\Server\Entities\ScopeEntityInterface;

trait FormatsScopesForStorage
{
    /**
     * Format the given scopes for storage.
     *
     * @param  array  $scopes
     * @return string
     */
    public function formatScopesForStorage(array $scopes)
    {
        return json_encode(array_map(function (ScopeEntityInterface $scope) {
            return $scope->getIdentifier();
        }, $scopes));
    }
}
