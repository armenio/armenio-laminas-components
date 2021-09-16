<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

declare(strict_types=1);

namespace Armenio\Filter\File;

use Laminas\Filter\Exception;
use Laminas\Filter\File\RenameUpload as VendorRenameUpload;
use Laminas\Stdlib\ErrorHandler;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Class RenameUpload
 *
 * @package Armenio\Filter\File
 */
class RenameUpload extends VendorRenameUpload
{
    /**
     * @param $source
     * @param $clientFileName
     *
     * @return string
     * @throws \ErrorException
     */
    protected function getFinalTarget($source, $clientFileName)
    {
        $target = $this->getTarget();

        if (null !== $target && '*' !== $target) {
            if (! is_dir($target) && ! file_exists($target)) {
                ErrorHandler::start();
                mkdir($target, 0755, true);
                $warningException = ErrorHandler::stop();
                if (! is_dir($target)) {
                    throw new Exception\RuntimeException(
                        sprintf('Could not create target directory: %s', $sourceFile),
                        0,
                        $warningException
                    );
                }
            }
        }

        return parent::getFinalTarget($source, $clientFileName);
    }

    /**
     * @param array|UploadedFileInterface|string $value
     *
     * @return array|mixed|UploadedFileInterface|string
     */
    public function filter($value)
    {
        if (is_array($value) && isset($value['tmp_name'])) {
            return $this->filterSapiUploadedFile($value);
        }

        return paren::filter($value);
    }

    /**
     * @param array $fileData
     *
     * @return array|mixed|string
     */
    private function filterSapiUploadedFile(array $fileData)
    {
        $sourceFile = $fileData['tmp_name'];

        if (isset($this->alreadyFiltered[$sourceFile])) {
            return $this->alreadyFiltered[$sourceFile];
        }

        $clientFilename = $fileData['name'];

        $targetFile = $this->getFinalTarget($sourceFile, $clientFilename);
        if ($sourceFile === $targetFile || ! file_exists($sourceFile)) {
            return $sourceFile;
        }

        $this->checkFileExists($targetFile);
        $this->moveUploadedFile($sourceFile, $targetFile);

        $this->alreadyFiltered[$sourceFile] = $targetFile;

        return $this->alreadyFiltered[$sourceFile];
    }
}
