<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;

class FileHandler
{
    public function __construct(
        private Filesystem $filesystem
    ) {
    }

    public function write(string $filename, string $content): void
    {
        $this->filesystem->appendToFile($filename, $content);
    }

    public function remove(string $filePath): void
    {
        $this->filesystem->remove($filePath);
    }

    public function read(string $filename): iterable
    {
        $file = new \SplFileObject(sprintf('compress.zlib:///%s', $filename));
        $file->setFlags(\SplFileObject::DROP_NEW_LINE | \SplFileObject::SKIP_EMPTY);

        foreach ($file as $line) {
            yield $line;
        }
    }
}
