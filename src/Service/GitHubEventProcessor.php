<?php

namespace App\Service;

use App\Service\GitHubEventMapper;

class GitHubEventProcessor
{
    public function __construct(
        private GitHubEventMapper $mapper
    ) {
    }

    public function processFile(string $filePath): void
    {
        $file = fopen($filePath, 'r');
        if (!$file) {
            throw new \Exception('Failed to open file');
        }

        while (false !== ($line = fgets($file))) {
            $data = json_decode($line, true);
            if (null === $data) {
                continue;
            }

            $event = $this->mapper->map($data);
        }

        fclose($file);
    }
}
