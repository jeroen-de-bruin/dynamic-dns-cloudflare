<?php

declare(strict_types=1);

namespace App\Command\Cloudflare;

use App\Service\CloudflareApiService;
use DateTimeImmutable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateIpAddressCommand extends Command
{
    /** @var CloudflareApiService */
    private $apiService;
    /** @var DateTimeImmutable */
    private $requestTime;

    public function __construct(
        CloudflareApiService $apiService
    ) {
        parent::__construct();

        $this->apiService = $apiService;
        $this->requestTime = new DateTimeImmutable();
    }

    protected function configure(): void
    {
        $this->setName('jdd:cloudflare:updateipaddress')
            ->setDescription('Update IP address for record in Cloudflare.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starttime: ' . $this->requestTime->format('Y-m-d H:i:s'));

        $output->writeln('Endtime: ' . $this->requestTime->format('Y-m-d H:i:s'));
    }
}
