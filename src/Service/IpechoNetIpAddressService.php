<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Validator\Constraints\Ip;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class IpechoNetIpAddressService implements IpAddressServiceInterface
{
    private const PUBLIC_IP_ADDRESS_PROVIDER = 'https://ipecho.net/plain';

    /** @var ValidatorInterface */
    private $validator;
    /** @var string */
    private $publicIpAddress;

    public function __construct(
        ValidatorInterface $validator
    ) {
        $this->validator = $validator;
    }

    public function getPublicIPv4Address(): string
    {
        if ($this->publicIpAddress === null) {
            $this->publicIpAddress = $this->parsePublicIPv4Address();
        }

        return $this->publicIpAddress;
    }

    public function getPublicIPv6Address(): string
    {
        // TODO: Implement getPublicIPv6Address() method.
    }

    /**
     * @throws IpAddressServiceException
     */
    private function parsePublicIPv4Address(): string
    {
        $address = \trim((string) \file_get_contents(self::PUBLIC_IP_ADDRESS_PROVIDER));

        $this->validateIPv4Address($address);

        return $address;
    }

    /**
     * @throws IpAddressServiceException
     */
    private function validateIPv4Address(string $ipAddress): void
    {
        $errors = $this->validator->validate(
            $ipAddress,
            [
                new NotBlank(),
                new Ip([
                    'version' => Ip::V4,
                ]),
            ]
        );

        if ($errors->count() !== 0) {
            throw new IpAddressServiceException('Cannot get public IP address from ' . self::PUBLIC_IP_ADDRESS_PROVIDER);
        }
    }
}
