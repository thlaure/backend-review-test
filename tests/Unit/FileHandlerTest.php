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

    public function testWrite(): void
    {
        $filePath = 'test-file-path';
        $content = 'test-content';

        $this->filesystem
            ->expects($this->once())
            ->method('appendToFile')
            ->with($filePath, $content);

        $this->fileHandler->write($filePath, $content);
    }

    public function testRead(): void
    {
        $testFile = sys_get_temp_dir().'/test-file.gz';
        
        $content = "line1\nline2\nline3\n";
        file_put_contents('compress.zlib://'.$testFile, $content);

        $lines = [];
        foreach ($this->fileHandler->read($testFile) as $line) {
            $lines[] = $line;
        }

        $this->assertCount(3, $lines);
        $this->assertEquals('line1', trim($lines[0]));
        $this->assertEquals('line2', trim($lines[1]));
        $this->assertEquals('line3', trim($lines[2]));

        unlink($testFile);
    }
}
