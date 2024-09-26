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

    public function testDump(): void
    {
        $tempDir = sys_get_temp_dir();
        $filePath = 'test-file';
        $content = 'Sample content';
        $tempFilePath = $tempDir . '/test-file1234';

        $this->filesystem
            ->method('tempnam')
            ->with($tempDir, $filePath)
            ->willReturn($tempFilePath);

        $this->filesystem
            ->expects($this->once())
            ->method('dumpFile')
            ->with($tempFilePath, $content);

        $result = $this->fileHandler->dump($filePath, $content);

        $this->assertEquals($tempFilePath, $result);
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
