<?php

declare(strict_types=1);

namespace App\Service;

use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Endpoints\User;
use Cloudflare\API\Endpoints\Zones;

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
     * @param string $domain
     * @param string $name
     * @param string $type
     *
     * @throws \Cloudflare\API\Endpoints\EndpointException
     *
     * @return \stdClass
     */
    public function getDNSRecordDetails(string $domain, string $type, string $name)
    {
        $zones = new Zones($this->adapter);

        $zoneId = $zones->getZoneID($domain);

        $dns = new DNS($this->adapter);

        $recordId = $dns->getRecordID($zoneId, $type, $name);

        return $dns->getRecordDetails($zoneId, $recordId);
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return new User($this->adapter);
    }
}
