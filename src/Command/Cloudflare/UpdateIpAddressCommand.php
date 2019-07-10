<?php

declare(strict_types=1);

namespace App\Command\Cloudflare;

use DateTimeImmutable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateIpAddressCommand extends Command
{
    private $requestTime;

    public function __construct()
    {
        parent::__construct();

        $this->requestTime = new DateTimeImmutable();
    }

    protected function configure(): void
    {
        $this->setName('jdd:cloudflare:updateipaddress')
            ->setDescription('Update IP address for record in Cloudflare.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starttijd: ' . $this->requestTime->format('Y-m-d H:i:s'));

        $output->writeln('Eindtijd: ' . $this->requestTime->format('Y-m-d H:i:s'));
    }
}
