<?php

declare(strict_types=1);

namespace App\Command\Cloudflare;

use App\Service\CloudflareApiService;
use App\Service\CommandlineOutputService;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetUserDetailsCommand extends Command
{
    /** @var CommandlineOutputService */
    private $commandlineOutputService;
    /** @var CloudflareApiService */
    private $apiService;
    /** @var DateTimeImmutable */
    private $requestTime;

    public function __construct(
        CommandlineOutputService $commandlineOutputService,
        CloudflareApiService $apiService
    ) {
        parent::__construct();

        $this->commandlineOutputService = $commandlineOutputService;
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
        try {
            $user = $this->apiService->getUser();

            $messages = [
                'User ID:      ' . $user->getUserID(),
                'User Email:   ' . $user->getUserEmail(),
                'User Details: ' . \json_encode($user->getUserDetails(), JSON_PRETTY_PRINT),
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
