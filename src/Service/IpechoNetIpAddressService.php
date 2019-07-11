<?php

declare(strict_types=1);

namespace App\Service;

class IpechoNetIpAddressService implements IpAddressServiceInterface
{
    private const PUBLIC_IP_ADDRESS_PROVIDER = 'https://ipecho.net/plain';

    /** @var string */
    private $publicIpAddress;

    public function getPublicIpAddress(): string
    {
        if ($this->publicIpAddress === null) {
            $this->publicIpAddress = $this->parsePublicIpAddress();
        }

        return $this->publicIpAddress;
    }

    private function parsePublicIpAddress(): string
    {
        return \trim((string) \file_get_contents(self::PUBLIC_IP_ADDRESS_PROVIDER));
    }
}
