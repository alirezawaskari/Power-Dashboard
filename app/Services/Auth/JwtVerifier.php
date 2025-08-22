<?php declare(strict_types=1);

namespace App\Services\Auth;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use UnexpectedValueException;

final class JwtVerifier
{
    public function __construct(
        private readonly string $issuer,
        private readonly string $audience,
        private readonly string $jwksUri,
        private readonly int $jwksTtl = 300
    ) {
    }

    public static function fromConfig(): self
    {
        $c = config('authz');
        return new self($c['issuer'], $c['audience'], $c['jwks_uri'], (int) $c['jwks_ttl']);
    }

    public function verify(string $token): array
    {
        $decodedHeader = $this->decodeHeader($token);
        $kid = $decodedHeader['kid'] ?? null;
        if (!$kid)
            throw new UnexpectedValueException('Missing kid');

        $keys = $this->jwks();
        $publicKeys = JWK::parseKeySet($keys);

        $payload = (array) JWT::decode($token, $publicKeys);
        if (($payload['iss'] ?? null) !== $this->issuer)
            throw new UnexpectedValueException('Bad iss');
        $aud = $payload['aud'] ?? null;
        if (is_array($aud) ? !in_array($this->audience, $aud, true) : $aud !== $this->audience) {
            throw new UnexpectedValueException('Bad aud');
        }
        return $payload; // contains sub, scope, exp, etc.
    }

    private function jwks(): array
    {
        return Cache::remember("jwks:{$this->jwksUri}", $this->jwksTtl, function () {
            $res = Http::timeout(5)->get($this->jwksUri);
            if (!$res->ok())
                throw new UnexpectedValueException('JWKS fetch failed');
            return $res->json();
        });
    }

    private function decodeHeader(string $jwt): array
    {
        [$h] = explode('.', $jwt, 2);
        return json_decode(base64_decode(strtr($h, '-_', '+/')), true) ?: [];
    }
}
