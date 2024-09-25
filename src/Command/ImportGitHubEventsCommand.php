<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
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
        private HttpClientInterface $httpClient,
        private Filesystem $filesystem
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Import GH events')
            ->addOption('date', null, InputOption::VALUE_REQUIRED, 'Date to import events for (format: YYYY-MM-DD)', date('Y-m-d'));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Let's rock !
        // It's up to you now
        $io = new SymfonyStyle($input, $output);

        $io->title('Import GitHub events');

        $date = $input->getOption('date');
        if (!\DateTime::createFromFormat('Y-m-d', $date)) {
            $io->error('Date format must be YYYY-MM-DD');

            return Command::FAILURE;
        }

        $today = new \DateTime();
        if ($date > $today->format('Y-m-d')) {
            $io->error('Date must be in the past');

            return Command::FAILURE;
        }

        $io->info('Importing events for ' . $date);

        $url = sprintf('http://data.gharchive.org/%s-12.json.gz', $date);
        $output->writeln('Fetching GitHub events from '.$url);

        try {
            $response = $this->httpClient->request('GET', $url);

            if (200 !== $response->getStatusCode()) {
                $io->error('Failed to fetch GitHub events');

                return Command::FAILURE;
            }

            $content = gzdecode($response->getContent());
            $filename = 'docker/imports/events_'.$date.'.json';
            $io->info('Saving events to '.$filename);
            $this->filesystem->dumpFile($filename, $content);

            $file = fopen($filename, 'r');
            if (!$file) {
                $io->error('Failed to open '.$filename);

                return Command::FAILURE;
            }

            while (($line = fgets($file)) !== false) {
                $data = json_decode($line, true);
                $io->info($data['type']);
            }
        
            fclose($file);
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->success('Events imported');

        return Command::SUCCESS;
    }
}
