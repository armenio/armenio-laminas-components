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

/**
 * Class RenameUploadTest
 *
 * @package ArmenioTest\Filter\File
 */
class RenameUploadTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var string
     */
    protected $filesPath;

    /**
     * @var string
     */
    protected $sourceFilename = 'tést file Not^Sanitizêd@.txt';

    /**
     * @var string
     */
    protected $sourceFile;

    /**
     * @var string
     */
    protected $targetPath;

    /**
     * @var string
     */
    protected $targetPathFile;

    /**
     * @var string
     */
    protected $targetFilenameSanitized = 'tést_file_notsanitizêd.txt';

    /**
     * @var string
     */
    protected $targetPathFileSanitized;

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

    protected function removeDir($dir)
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

    /**
     * @return void
     */
    public function testOptions()
    {
        $filter = new RenameUploadFilter([
            'sanitize' => true,
        ]);
        $this->assertTrue($filter->getSanitize());
    }

    /**
     * @return void
     */
    public function testGetSetOptions()
    {
        $filter = new RenameUploadFilter();
        $this->assertFalse($filter->getSanitize());
        $filter->setSanitize(true);
        $this->assertTrue($filter->getSanitize());
    }

    /**
     * @return void
     */
    public function testTargetDirectoryWillBeCreatedWithoutUseUploadName()
    {
        $filter = new RenameUploadMock($this->targetPath . '/testTargetDirectoryWillBeCreatedWithoutUseUploadName');
        $filter->setUseUploadName(false);
        $filter->filter($this->sourceFile);
        $this->assertFalse($filter->getUseUploadName());
        $this->assertTrue(is_dir($this->targetPath . '/testTargetDirectoryWillBeCreatedWithoutUseUploadName'));
        $this->assertTrue(file_exists($this->targetPath . '/testTargetDirectoryWillBeCreatedWithoutUseUploadName/' . $this->sourceFilename));
    }

    /**
     * @return void
     */
    public function testTargetDirectoryWillBeCreatedWithUseUploadName()
    {
        $filter = new RenameUploadMock($this->targetPath . '/testTargetDirectoryWillBeCreatedWithUseUploadName');
        $filter->setUseUploadName(true);
        $filter->filter($this->sourceFile);
        $this->assertTrue($filter->getUseUploadName());
        $this->assertTrue(is_dir($this->targetPath . '/testTargetDirectoryWillBeCreatedWithUseUploadName'));
        $this->assertTrue(file_exists($this->targetPath . '/testTargetDirectoryWillBeCreatedWithUseUploadName/' . $this->sourceFilename));
    }

    /**
     * @return void
     */
    public function testTargetDirectoryWillBeCreatedWithUseUploadNameAndSanitize()
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

    /**
     * @return void
     */
    public function testTargetPathFileWillBeCreated()
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

    /**
     * @return void
     */
    public function testTargetPathFileSanitizedWillBeCreated()
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
