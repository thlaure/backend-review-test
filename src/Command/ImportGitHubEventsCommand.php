<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\EventType;
use App\Message\DataEventsMessage;
use App\Service\FileHandler;
use App\Service\InputValidator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * This command must import GitHub events.
 * You can add the parameters and code you want in this command to meet the need.
 */
#[AsCommand(
    name: 'app:import-github-events',
    description: 'Import GH events',
)]
class ImportGitHubEventsCommand extends Command
{
    public function __construct(
        private InputValidator $validator,
        private MessageBusInterface $messageBus,
        private Filesystem $filesystem,
        private HttpClientInterface $httpClient,
        private FileHandler $fileHandler,
        private int $batchSize
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Import GH events')
            ->addOption('date', null, InputOption::VALUE_REQUIRED, 'Date to import events for (format: YYYY-MM-DD)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Import GitHub events');

        $date = $input->getOption('date');

        if (!$this->validator->validateDate($date)) {
            $io->error('Invalid date format or future date.');

            return Command::FAILURE;
        }

        $io->info('Importing events for '.$date);

        for ($hour = 0; $hour < 24; ++$hour) {
            $url = sprintf('http://data.gharchive.org/%s-%s.json.gz', $date, $hour);
            $io->writeln('Fetching GitHub events from '.$url);

            try {
                $response = $this->httpClient->request(Request::METHOD_GET, $url);

                $filename = $this->filesystem->tempnam(sys_get_temp_dir(), 'events_'.$date.'_'.$hour);
                foreach ($this->httpClient->stream($response) as $chunk) {
                    $this->filesystem->appendToFile($filename, $chunk->getContent());
                }

                $dataEvents = new DataEventsMessage();
                foreach ($this->fileHandler->read($filename) as $index => $line) {
                    $eventData = json_decode($line, true);
                    if (!is_array($eventData) || !array_key_exists('type', $eventData) || !array_key_exists($eventData['type'], EventType::EVENT_MAPPING)) {
                        continue;
                    }

                    $dataEvents->addEventData($eventData);

                    if (($index + 1) % $this->batchSize === 0) {
                        $this->messageBus->dispatch($dataEvents);
                        $dataEvents = new DataEventsMessage();
                    }
                }

                if (!empty($dataEvents->getEventsData())) {
                    $this->messageBus->dispatch($dataEvents);
                }

                $this->filesystem->remove($filename);
            } catch (\Exception $e) {
                $io->error($e->getMessage());

                return Command::FAILURE;
            }
        }

        $io->success('Events dispatched');

        return Command::SUCCESS;
    }
}
