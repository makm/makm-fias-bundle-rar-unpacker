<?php

namespace Makm\FiasBundle\Component\Unpacker;

use InvalidArgumentException;
use Liquetsoft\Fias\Component\Exception\UnpackerException;
use Liquetsoft\Fias\Component\Unpacker\Unpacker;
use SplFileInfo;
use Throwable;

/**
 * Объект, который распаковывает файлы из rar архива.
 */
class RarArchiverUnpacker implements Unpacker
{
    /**
     * @inheritdoc
     *
     * @psalm-suppress TooFewArguments
     */
    public function unpack(SplFileInfo $source, SplFileInfo $destination): void
    {
        if (!$source->isFile() || !$source->isReadable()) {
            throw new InvalidArgumentException(
                "Can't find or read archive '".$source->getPath()."' to extract."
            );
        }

        if (!$destination->isDir() || !$destination->isWritable()) {
            throw new InvalidArgumentException(
                "Destination folder '".$destination->getPath()."' isn't writable or doesn't exist."
            );
        }

        try {
            if (!\is_dir($destination)) {
                if (!mkdir($destination, 0644) && !is_dir($destination)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $destination));
                }
            }

            ob_start();
            passthru("/usr/bin/unrar e {$source->getPathname()} {$destination}");
            ob_end_clean(); //Use this instead of ob_flush()
        } catch (Throwable $e) {
            $message = "Can't extract '{$source->getPathname()}' to '{$destination}'.";
            throw new UnpackerException($message, 0, $e);
        }
    }
}
