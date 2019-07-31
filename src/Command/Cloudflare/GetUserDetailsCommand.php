<?php

declare(strict_types=1);

namespace App\Command\Cloudflare;

use App\Service\CloudflareApiService;
use DateTimeImmutable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetUserDetailsCommand extends Command
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
        $this->setName('jdd:cloudflare:getuserdetails')
            ->setDescription('Get user details from Cloudflare.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starttime: ' . $this->requestTime->format('Y-m-d H:i:s'));

        $user = $this->apiService->getUser();

        $output->writeln('User ID: ' . $user->getUserID());
        $output->writeln('User Email: ' . $user->getUserEmail());
        $output->writeln('User Details: ' . \json_encode($user->getUserDetails()));
        $output->writeln('User Body: ' . \json_encode($user->getBody()));

        $output->writeln('Endtime: ' . $this->requestTime->format('Y-m-d H:i:s'));
    }
}
