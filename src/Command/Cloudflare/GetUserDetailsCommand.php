<?php

declare(strict_types=1);

namespace App\Command\Cloudflare;

use App\Service\CloudflareApiService;
use DateTimeImmutable;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GetUserDetailsCommand extends Command
{
    /** @var LoggerInterface */
    private $logger;
    /** @var CloudflareApiService */
    private $apiService;
    /** @var DateTimeImmutable */
    private $requestTime;

    public function __construct(
        LoggerInterface $logger,
        CloudflareApiService $apiService
    ) {
        parent::__construct();

        $this->logger = $logger;
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
        $e = null;

        $messages = [
            'Starttime:         ' . $this->requestTime->format('Y-m-d H:i:s'),
        ];

        try {
            $user = $this->apiService->getUser();

            $messages = \array_merge($messages, [
                '',
                'User ID:           ' . $user->getUserID(),
                'User Email:        ' . $user->getUserEmail(),
                'User Details:      ' . \json_encode($user->getUserDetails(), JSON_PRETTY_PRINT),
            ]);
        } catch (Exception $e) {
        }

        $messages = \array_merge($messages, [
            '',
            'Endtime:           ' . $this->requestTime->format('Y-m-d H:i:s'),
            '',
        ]);

        $io = new SymfonyStyle($input, $output);

        if ($e instanceof \Exception) {
            $io->title('[ERROR] ' . $e->getCode());
            $io->caution($e->getMessage());
        } else {
            $io->text($messages);
        }

        if ($e instanceof Exception) {
            $this->logger->critical('[ERROR] ' . $e->getCode(), [$e->getMessage()]);
        }
    }
}
