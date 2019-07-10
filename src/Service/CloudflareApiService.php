<?php

declare(strict_types=1);

namespace App\Service;

use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\User;

class CloudflareApiService
{
    /** @var Guzzle */
    private $adapter;

    public function __construct(
        string $cloudflareApiKey,
        string $cloudflareUsername
    ) {
        $key = new APIKey(
            $cloudflareUsername,
            $cloudflareApiKey
        );

        $this->adapter = new Guzzle($key);
    }

    public function getUser(): User
    {
        return new User($this->adapter);
    }
}
