<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace ArmenioTest\Filter\File;

use Armenio\Filter\File\RenameUpload as RenameUploadFilter;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class RenameUploadTest extends TestCase
{
    use ProphecyTrait;

    protected ?string $filesPath = null;

    protected string $sourceFilename = 'tést file Not^Sanitizêd@.txt';

    protected ?string $sourceFile;

    protected ?string $targetPath;

    protected ?string $targetPathFile;

    protected string $targetFilenameSanitized = 'tést_file_notsanitizêd.txt';

    protected ?string $targetPathFileSanitized;

    public function setUp(): void
    {
        $this->filesPath = sprintf('%s%s%s', sys_get_temp_dir(), DIRECTORY_SEPARATOR, uniqid('laminasilter'));
        $this->targetPath = sprintf('%s%s%s', $this->filesPath, DIRECTORY_SEPARATOR, 'targetPath');

        mkdir($this->targetPath, 0775, true);

        $this->sourceFile = $this->filesPath . DIRECTORY_SEPARATOR . $this->sourceFilename;
        $this->targetPathFile = $this->targetPath . DIRECTORY_SEPARATOR . $this->sourceFilename;
        $this->targetPathFileSanitized = $this->targetPath . DIRECTORY_SEPARATOR . $this->targetFilenameSanitized;

        touch($this->sourceFile);
    }

    public function tearDown(): void
    {
        $this->removeDir($this->filesPath);
    }

    protected function removeDir($dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        foreach (glob($dir . DIRECTORY_SEPARATOR . '*') as $file) {
            if (is_file($file)) {
                unlink($file);
                continue;
            }
            if (is_dir($file)) {
                $this->removeDir($file);
                continue;
            }
        }

        rmdir($dir);
    }

    public function testOptions(): void
    {
        $filter = new RenameUploadFilter([
            'sanitize' => true,
        ]);
        $this->assertTrue($filter->getSanitize());
    }

    public function testGetSetOptions(): void
    {
        $filter = new RenameUploadFilter();
        $this->assertFalse($filter->getSanitize());
        $filter->setSanitize(true);
        $this->assertTrue($filter->getSanitize());
    }

    public function testTargetDirectoryWillBeCreatedWithoutUseUploadName(): void
    {
        $filter = new RenameUploadMock($this->targetPath . '/testTargetDirectoryWillBeCreatedWithoutUseUploadName');
        $filter->setUseUploadName(false);
        $filter->filter($this->sourceFile);
        $this->assertFalse($filter->getUseUploadName());
        $this->assertTrue(is_dir($this->targetPath . '/testTargetDirectoryWillBeCreatedWithoutUseUploadName'));
        $this->assertTrue(file_exists($this->targetPath . '/testTargetDirectoryWillBeCreatedWithoutUseUploadName/' . $this->sourceFilename));
    }

    public function testTargetDirectoryWillBeCreatedWithUseUploadName(): void
    {
        $filter = new RenameUploadMock($this->targetPath . '/testTargetDirectoryWillBeCreatedWithUseUploadName');
        $filter->setUseUploadName(true);
        $filter->filter($this->sourceFile);
        $this->assertTrue($filter->getUseUploadName());
        $this->assertTrue(is_dir($this->targetPath . '/testTargetDirectoryWillBeCreatedWithUseUploadName'));
        $this->assertTrue(file_exists($this->targetPath . '/testTargetDirectoryWillBeCreatedWithUseUploadName/' . $this->sourceFilename));
    }

    public function testTargetDirectoryWillBeCreatedWithUseUploadNameAndSanitize(): void
    {
        $filter = new RenameUploadMock($this->targetPath . '/testTargetDirectoryWillBeCreatedWithUseUploadNameWithoutBar');
        $filter->setUseUploadName(true);
        $filter->setSanitize(true);
        $filter->filter($this->sourceFile);
        $this->assertTrue($filter->getUseUploadName());
        $this->assertTrue($filter->getSanitize());
        $this->assertTrue(is_dir($this->targetPath . '/testTargetDirectoryWillBeCreatedWithUseUploadNameWithoutBar'));
        $this->assertTrue(file_exists($this->targetPath . '/testTargetDirectoryWillBeCreatedWithUseUploadNameWithoutBar/' . $this->targetFilenameSanitized));
    }

    public function testTargetPathFileWillBeCreated(): void
    {
        $filter = new RenameUploadMock([
            'target' => $this->targetPath,
            'use_upload_name' => true,
        ]);
        $this->assertEquals($this->targetPath, $filter->getTarget());
        $this->assertTrue($filter->getUseUploadName());

        $filered = $filter->filter($this->sourceFile);
        $this->assertTrue(file_exists($filered));
        $this->assertEquals($this->targetPathFile, $filered);
    }

    public function testTargetPathFileSanitizedWillBeCreated(): void
    {
        $filter = new RenameUploadMock([
            'target' => $this->targetPath,
            'use_upload_name' => true,
            'sanitize' => true,
        ]);
        $this->assertEquals($this->targetPath, $filter->getTarget());
        $this->assertTrue($filter->getUseUploadName());
        $this->assertTrue($filter->getSanitize());

        $filered = $filter->filter($this->sourceFile);
        $this->assertTrue(file_exists($filered));
        $this->assertEquals($this->targetPathFileSanitized, $filered);
    }
}
