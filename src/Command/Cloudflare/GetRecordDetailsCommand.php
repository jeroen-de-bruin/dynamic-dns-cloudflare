<?php

declare(strict_types=1);

namespace App\Command\Cloudflare;

use App\Service\CloudflareApiService;
use App\Service\IpAddressServiceInterface;
use DateTimeImmutable;
use Exception;
use GuzzleHttp\Exception\ClientException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GetRecordDetailsCommand extends Command
{
    /** @var LoggerInterface */
    private $logger;
    /** @var CloudflareApiService */
    private $apiService;
    /** @var IpAddressServiceInterface */
    private $ipAddressService;
    /** @var DateTimeImmutable */
    private $requestTime;

    public function __construct(
        LoggerInterface $logger,
        CloudflareApiService $apiService,
        IpAddressServiceInterface $ipAddressService
    ) {
        parent::__construct();

        $this->logger = $logger;
        $this->apiService = $apiService;
        $this->ipAddressService = $ipAddressService;
        $this->requestTime = new DateTimeImmutable();
    }

    protected function configure(): void
    {
        $this->setName('jdd:cloudflare:getrecorddetails')
            ->setDescription('Get the details of a record in Cloudflare.')
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
        $e = null;
        $name = $this->getRecordName($input);
        $type = $this->getRecordType($input);

        $messages = [
            'Starttime:         ' . $this->requestTime->format('Y-m-d H:i:s'),
            'Record:            ' . $name,
            'Type:              ' . $type,
            'Public IP Address: ' . $this->ipAddressService->getPublicIpAddress(),
        ];

        try {
            $recordDetails = $this->apiService->getDNSRecordDetails($name, $type);

            $messages = \array_merge($messages, [
                '',
                (string) \json_encode($recordDetails, JSON_PRETTY_PRINT),
            ]);
        } catch (ClientException $e) {
        }

        $messages = \array_merge($messages, [
            '',
            'Endtime:           ' . $this->requestTime->format('Y-m-d H:i:s'),
            '',
        ]);

        $io = new SymfonyStyle($input, $output);

        if ($e instanceof Exception) {
            $io->title('[ERROR] ' . $e->getCode());
            $io->caution($e->getMessage());
        } else {
            $io->text($messages);
        }

        if ($e instanceof Exception) {
            $this->logger->critical('[ERROR] ' . $e->getCode(), [$e->getMessage()]);
        }
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
