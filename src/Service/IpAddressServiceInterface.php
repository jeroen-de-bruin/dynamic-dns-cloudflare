<?php

declare(strict_types=1);

namespace App\Service;

interface IpAddressServiceInterface
{
    public function getPublicIpAddress(): string;
}
