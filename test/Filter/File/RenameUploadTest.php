<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

declare(strict_types=1);

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
    protected $sourceFile;

    /**
     * @var string
     */
    protected $targetPath;

    /**
     * @var string
     */
    protected $targetPathFile;

    public function setUp(): void
    {
        $this->filesPath = sprintf('%s%s%s', sys_get_temp_dir(), DIRECTORY_SEPARATOR, uniqid('laminasilter'));
        $this->targetPath = sprintf('%s%s%s', $this->filesPath, DIRECTORY_SEPARATOR, 'targetPath');

        mkdir($this->targetPath, 0775, true);

        $this->sourceFile = $this->filesPath . DIRECTORY_SEPARATOR . 'tést file Not^Sanitizêd@.txt';
        $this->targetPathFile = $this->targetPath . DIRECTORY_SEPARATOR . 'tést_file_notsanitizêd.txt';

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
    public function testTargetDirectoryWillNotBeCreated()
    {
        $filter = new RenameUploadFilter($this->targetPath . '/targetDirectoryWillNotExists');
        $filter->filter('falsefile');
        $this->assertFalse(is_dir($this->targetPath . '/targetDirectoryWillNotExists'));
    }

    /**
     * @return void
     */
    public function testTargetDirectoryWillBeCreated()
    {
        $filter = new RenameUploadFilter($this->targetPath . '/targetDirectoryWillExists/');
        $filter->filter('falsefile');
        $this->assertTrue(is_dir($this->targetPath . '/targetDirectoryWillExists/'));
    }

    /**
     * @return void
     */
    public function testTargetTargetDirectoryWillNotBeCreatedWithBackSlash()
    {
        $filter = new RenameUploadFilter($this->targetPath . '/targetDirectoryWillExistsWithBackslash\\');
        $filter->filter('falsefile');
        $this->assertTrue(is_dir($this->targetPath . '/targetDirectoryWillExistsWithBackslash\\'));
    }

    /**
     * @return void
     */
    public function testGetSanitizedFile()
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
        $this->assertEquals($this->targetPathFile, $filered);
    }
}
