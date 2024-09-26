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
use Symfony\Component\HttpFoundation\Response;
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

        for ($hour = 0; $hour < 24; ++$hour) {
            $url = sprintf('http://data.gharchive.org/%s-%s.json.gz', $date, $hour);
            $output->writeln('Fetching GitHub events from '.$url);

            try {
                $response = $this->httpClient->request('GET', $url);

                if (Response::HTTP_OK !== $response->getStatusCode()) {
                    $io->error('Failed to fetch GitHub events');

                    return Command::FAILURE;
                }

                $content = gzdecode($response->getContent());

                $filename = 'events_'.$date.'_'.$hour.'.json';
                $tempName = $this->filesystem->tempnam(sys_get_temp_dir(), $filename);
                $this->filesystem->dumpFile($tempName, $content);

                $file = fopen($tempName, 'r');
                if (!$file) {
                    $io->error('Failed to open '.$filename);

                    return Command::FAILURE;
                }

                while (false !== ($line = fgets($file))) {
                    $data = json_decode($line, true);
                    $io->info($data['type']);
                }
            
                $this->filesystem->remove($tempName);
                fclose($file);
            }
            catch (\Exception $e) {
                $io->error($e->getMessage());

                return Command::FAILURE;
            }
        }

        $io->success('Events imported');

        return Command::SUCCESS;
    }
}
