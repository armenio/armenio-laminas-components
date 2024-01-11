<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace ArmenioTest\Filter\File;

use Armenio\Filter\File\RenameUpload;

class RenameUploadMock extends RenameUpload
{
    /**
     * @param array|string $targetOrOptions
     */
    public function __construct($targetOrOptions = [])
    {
        parent::__construct($targetOrOptions);
    }

    /**
     * @param string $sourceFile
     * @param string $targetFile
     */
    protected function moveUploadedFile($sourceFile, $targetFile): bool
    {
        return rename($sourceFile, $targetFile);
    }
}
