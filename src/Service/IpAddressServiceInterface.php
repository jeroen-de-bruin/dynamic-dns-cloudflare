<?php

declare(strict_types=1);

namespace App\Service;

interface IpAddressServiceInterface
{
    /**
     * @throws IpAddressServiceException
     */
    public function getPublicIPv4Address(): string;

    /**
     * @throws IpAddressServiceException
     */
    public function getPublicIPv6Address(): string;
}
