<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace Armenio\Filter\File;

use Laminas\Filter\Exception;
use Laminas\Filter\File\RenameUpload as VendorRenameUpload;
use Laminas\Filter\FilterChain;
use Laminas\Filter\StringToLower;
use Laminas\Filter\Word\SeparatorToSeparator;
use Laminas\I18n\Filter\Alnum;
use Laminas\Stdlib\ErrorHandler;

class RenameUpload extends VendorRenameUpload
{
    public function getSanitize(): bool
    {
        return $this->options['sanitize'];
    }

    public function setSanitize(bool $flag = true): RenameUpload
    {
        $this->options['sanitize'] = $flag;
        return $this;
    }

    /**
     * @param array|string $targetOrOptions
     */
    public function __construct($targetOrOptions = [])
    {
        $this->options = array_merge($this->options, [
            'sanitize' => false,
        ]);

        parent::__construct($targetOrOptions);
    }

    /**
     * @param string $source
     * @param string|null $clientFileName
     *
     * @return string
     * @throws \ErrorException
     */
    protected function getFinalTarget($source, $clientFileName): string
    {
        $target = $this->getTarget();
        if ($target === null || $target === '*') {
            $target = $source;
        }

        if (! is_dir($target)) {
            ErrorHandler::start();
            mkdir($target, 0755, true);
            $warningException = ErrorHandler::stop();
            if (! is_dir($target)) {
                throw new Exception\RuntimeException(
                    sprintf('Could not create target directory: %s', $target),
                    0,
                    $warningException
                );
            }
        }

        if ($this->getUseUploadName() && $this->getSanitize()) {
            $clientFileName = $this->sanitizeFilename($clientFileName);
        }

        return parent::getFinalTarget($source, $clientFileName);
    }

    protected function sanitizeFilename(string $filename): string
    {
        $info = pathinfo($filename);

        $filename = (new FilterChain())
            ->attach(new Alnum(true))
            ->attach(new SeparatorToSeparator(' ', '_'))
            ->attach(new StringToLower())
            ->filter($info['filename']);

        $extension = '';
        if (isset($info['extension'])) {
            $extension .= '.' . $info['extension'];
        }

        return $filename . $extension;
    }
}
