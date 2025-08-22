<?php

return [
    // map your roles to OAuth scopes enforced by middleware
    'role_scopes' => [
        'owner' => ['devices:read', 'devices:write', 'tickets:read', 'tickets:write', 'settings:read', 'settings:write'],
        'operator' => ['devices:read', 'devices:write', 'tickets:read', 'tickets:write', 'settings:read'],
        'viewer' => ['devices:read', 'tickets:read', 'settings:read'],
        'support' => ['tickets:read', 'tickets:write', 'devices:read'],
    ],

    // accepted issuer & audience for tokens
    'issuer' => env('OAUTH_ISSUER_URL'),
    'audience' => env('OAUTH_AUDIENCE'),

    // JWKS endpoint for RS256 public keys
    'jwks_uri' => env('OAUTH_JWKS_URI'),

    // cache JWKS for this many seconds
    'jwks_ttl' => env('OAUTH_JWKS_TTL', 300),
];
