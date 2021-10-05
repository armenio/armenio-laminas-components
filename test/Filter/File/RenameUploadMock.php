<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace ArmenioTest\Filter\File;

use Armenio\Filter\File\RenameUpload;

/**
 * Class RenameUploadMock
 *
 * @package ArmenioTest\Filter\File
 */
class RenameUploadMock extends RenameUpload
{
    /**
     * RenameUploadMock constructor.
     *
     * @param array $targetOrOptions
     */
    public function __construct($targetOrOptions = [])
    {
        parent::__construct($targetOrOptions);
    }

    /**
     * @param string $sourceFile
     * @param string $targetFile
     *
     * @return bool
     */
    protected function moveUploadedFile($sourceFile, $targetFile)
    {
        return rename($sourceFile, $targetFile);
    }
}
