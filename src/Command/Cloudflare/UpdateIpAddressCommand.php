<?php

declare(strict_types=1);

namespace App\Command\Cloudflare;

use App\Service\CloudflareApiService;
use App\Service\CommandlineOutputService;
use App\Service\IpAddressServiceInterface;
use DateTimeImmutable;
use GuzzleHttp\Exception\ClientException;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateIpAddressCommand extends Command
{
    /** @var CommandlineOutputService */
    private $commandlineOutputService;
    /** @var CloudflareApiService */
    private $apiService;
    /** @var IpAddressServiceInterface */
    private $ipAddressService;
    /** @var DateTimeImmutable */
    private $requestTime;

    public function __construct(
        CommandlineOutputService $commandlineOutputService,
        CloudflareApiService $apiService,
        IpAddressServiceInterface $ipAddressService
    ) {
        parent::__construct();

        $this->commandlineOutputService = $commandlineOutputService;
        $this->apiService = $apiService;
        $this->ipAddressService = $ipAddressService;
        $this->requestTime = new DateTimeImmutable();
    }

    protected function configure(): void
    {
        $this->setName('jdd:cloudflare:updateipaddress')
            ->setDescription('Update IP address for record in Cloudflare.')
            ->addOption('domainname', 'd', InputOption::VALUE_REQUIRED, 'The domain record to modify.')
            ->addOption('type', 't', InputOption::VALUE_REQUIRED, 'The type of record to use.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws \Cloudflare\API\Endpoints\EndpointException
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $name = $this->getRecordName($input);
            $type = $this->getRecordType($input);

            $response = $this->apiService->updateDNSRecordDetails(
                $name,
                $type,
                $this->ipAddressService->getPublicIpAddress()
            );

            $messages = [
                'Starttime:         ' . $this->requestTime->format('Y-m-d H:i:s'),
                'Record:            ' . $name,
                'Type:              ' . $type,
                'Public IP Address: ' . $this->ipAddressService->getPublicIpAddress(),
                '',
                (string) \json_encode($response, JSON_PRETTY_PRINT),
            ];
        } catch (ClientException $e) {
        }

        $this->commandlineOutputService->processOutput(
            $this->requestTime,
            $input,
            $output,
            $messages ?? [],
            $e ?? null,
            (bool) $input->getOption('verbose') ?: false
        );
    }

    private function getRecordName(InputInterface $input)
    {
        $domainnameArgument = $input->getOption('domainname');

        if (!\is_string($domainnameArgument)) {
            throw new InvalidArgumentException('`domainname` has to be a string');
        }

        return $domainnameArgument;
    }

    /**
     * @param InputInterface $input
     *
     * @return string
     */
    protected function getRecordType(InputInterface $input): string
    {
        $typeOption = $input->getOption('type');

        if (!\is_string($typeOption) || empty($typeOption)) {
            throw new InvalidArgumentException('`type` has to be a string');
        }

        return \strtoupper($typeOption);
    }
}
