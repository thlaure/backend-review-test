<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;

class FileHandler
{
    public function __construct(
        private Filesystem $filesystem
    ) {
    }

    public function dump(string $path, string $content): string
    {
        $tempName = $this->filesystem->tempnam(sys_get_temp_dir(), $path);
        $this->filesystem->dumpFile($tempName, $content);

        return $tempName;
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
