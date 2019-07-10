<?php

declare(strict_types=1);

namespace App\Service;

class CloudflareApiService
{
    /** @var string */
    private $apiKey;
    /** @var string */
    private $username;

    public function __construct(
        string $cloudflareApiKey,
        string $cloudflareUsername
    ) {
        $this->apiKey = $cloudflareApiKey;
        $this->username = $cloudflareUsername;
    }
}
