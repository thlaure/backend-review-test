<?php

use App\Service\FileHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class FileHandlerTest extends TestCase
{
    private Filesystem $filesystem;
    private FileHandler $fileHandler;

    protected function setUp(): void
    {
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->fileHandler = new FileHandler($this->filesystem);
    }

    public function testRemove(): void
    {
        $filePath = 'test-file-path';

        $this->filesystem
            ->expects($this->once())
            ->method('remove')
            ->with($filePath);

        $this->fileHandler->remove($filePath);
    }
}
