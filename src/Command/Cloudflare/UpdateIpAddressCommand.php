<?php

declare(strict_types=1);

namespace App\Command\Cloudflare;

use App\Service\CloudflareApiService;
use App\Service\IpAddressService;
use DateTimeImmutable;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateIpAddressCommand extends Command
{
    /** @var CloudflareApiService */
    private $apiService;
    /** @var IpAddressService */
    private $ipAddressService;
    /** @var DateTimeImmutable */
    private $requestTime;

    public function __construct(
        CloudflareApiService $apiService,
        IpAddressService $ipAddressService
    ) {
        parent::__construct();

        $this->apiService = $apiService;
        $this->ipAddressService = $ipAddressService;
        $this->requestTime = new DateTimeImmutable();
    }

    protected function configure(): void
    {
        $this->setName('jdd:cloudflare:updateipaddress')
            ->setDescription('Update IP address for record in Cloudflare.')
            ->addArgument('domainname', InputArgument::REQUIRED, 'The domain record to modify.')
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
        $io = new SymfonyStyle($input, $output);
        $io->newLine();

        $io->text('Starttime: ' . $this->requestTime->format('Y-m-d H:i:s'));

        $domainnameArgument = $input->getArgument('domainname');
        if (!\is_string($domainnameArgument)) {
            throw new InvalidArgumentException('Domainname has to be a string');
        }

        $parts = \explode('.', $domainnameArgument);

        $name = \trim((string) \array_shift($parts));
        $domain = \implode('.', $parts);

        $typeOption = $input->getOption('type');

        $type = \strtoupper(\is_string($typeOption) ? $typeOption : 'TXT');

        $io->text([
            'Domain:            ' . $domain,
            'Record:            ' . $name,
            'Type:              ' . $type,
            'Public IP Address: ' . $this->ipAddressService->getPublicIpAddress(),
        ]);

        $io->newLine();

        $recordDetails = $this->apiService->getDNSRecordDetails(
            $domain,
            $type,
            $name
        );

        $io->text(\json_encode($recordDetails));

        $io->newLine();
        $io->text('Endtime: ' . $this->requestTime->format('Y-m-d H:i:s'));
        $io->newLine();
    }
}
