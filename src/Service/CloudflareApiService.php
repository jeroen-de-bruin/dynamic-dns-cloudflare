<?php

declare(strict_types=1);

namespace App\Service;

use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Endpoints\User;
use Cloudflare\API\Endpoints\Zones;
use stdClass;

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

    /**
     * @param string $completeName
     * @param string $type
     *
     * @throws \Cloudflare\API\Endpoints\EndpointException
     *
     * @return stdClass
     */
    public function getDNSRecordDetails(string $completeName, string $type): stdClass
    {
        $zones = new Zones($this->adapter);

        $zoneId = $zones->getZoneID($this->getDomain($completeName));

        $dns = new DNS($this->adapter);

        $recordId = $dns->getRecordID($zoneId, $type, $completeName);

        return $dns->getRecordDetails($zoneId, $recordId);
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return new User($this->adapter);
    }

    private function getDomain(string $completeName): string
    {
        $parts = \explode('.', $completeName);

        \array_shift($parts);

        return \implode('.', $parts);
    }

    private function getRecordName(string $completeName): string
    {
        $parts = \explode('.', $completeName);

        return \trim((string) \array_shift($parts));
    }
}
