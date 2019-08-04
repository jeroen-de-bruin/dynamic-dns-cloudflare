<?php

namespace App\Service;

use DateTimeImmutable;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CommandlineOutputService
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    public function processOutput(
        DateTimeImmutable $datetime,
        InputInterface $input,
        OutputInterface $output,
        array $commandMessages,
        ?Exception $e = null,
        bool $verbose = true
    ): void {
        $messages = \array_merge([
            'Starttime: ' . $datetime->format('Y-m-d H:i:s'),
            '',
        ], $commandMessages, [
            '',
            'Endtime: ' . (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            '',
        ]);

        $io = new SymfonyStyle($input, $output);

        if ($verbose) {
            if ($e instanceof Exception) {
                $io->title('[ERROR] ' . $e->getCode());
                $io->caution($e->getMessage());
            } else {
                $io->text($messages);
            }
        }

        if ($e instanceof Exception) {
            $this->logger->critical('[ERROR] ' . $e->getCode(), [$e->getMessage()]);
        }
    }
}
