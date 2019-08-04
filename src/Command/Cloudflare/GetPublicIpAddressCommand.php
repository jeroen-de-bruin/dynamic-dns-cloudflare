<?php

namespace App\Command\Cloudflare;

use App\Service\CommandlineOutputService;
use App\Service\IpAddressServiceInterface;
use DateTimeImmutable;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetPublicIpAddressCommand extends Command
{
    /** @var CommandlineOutputService */
    private $commandlineOutputService;
    /** @var LoggerInterface */
    private $logger;
    /** @var IpAddressServiceInterface */
    private $ipAddressService;
    /** @var DateTimeImmutable */
    private $requestTime;

    public function __construct(
        CommandlineOutputService $commandlineOutputService,
        LoggerInterface $logger,
        IpAddressServiceInterface $ipAddressService
    ) {
        parent::__construct();

        $this->commandlineOutputService = $commandlineOutputService;
        $this->logger = $logger;
        $this->ipAddressService = $ipAddressService;
        $this->requestTime = new DateTimeImmutable();
    }

    protected function configure(): void
    {
        $this->setName('jdd:cloudflare:getpublicipaddress')
            ->setDescription('Get public IP address.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $ipAddress = $this->ipAddressService->getPublicIpAddress();

            $messages = [
                'Public IP Address: ' . $ipAddress,
            ];
        } catch (Exception $e) {
        }

        $this->commandlineOutputService->processOutput(
            $this->requestTime,
            $input,
            $output,
            $messages ?? [],
            $e ?? null
        );
    }
}
