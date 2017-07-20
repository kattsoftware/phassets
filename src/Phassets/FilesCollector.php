<?php

namespace Phassets;

use Phassets\Exceptions\PhassetsInternalException;

/**
 * Files gatherer (by extensions)
 *
 * This content is released under the MIT License (MIT).
 * @see LICENSE file
 */
class FilesCollector
{
    /**
     * Parses a folder and returns the full paths of all files from there
     * having the extensions in $extensions array. Setting $deep to
     * true will make this collector to look in all sub-folders as well.
     *
     * @param string $dirPath Full path of a folder to be opened by a DirectoryIterator
     * @param array $extensions List of extensions for checking the files found against
     * @param bool $deep Whether to search in sub-folders or not
     * @param array $exclusions Array of filenames to be removed from processing
     * @return array List of the found files full paths
     * @throws PhassetsInternalException If the DirectoryIterator may fail
     */
    public function parse($dirPath, array $extensions, $deep = false, array $exclusions = array())
    {
        $files = [];

        try {
            $iterator = new \DirectoryIterator($dirPath);

            foreach ($iterator as $entry) {
                if ($deep
                    && $entry->isDir()
                    && !$entry->isDot()
                ) {
                    $files = array_merge($files, $this->parse($entry->getPathname(), $extensions, $deep));
                }

                if ($entry->isFile() && in_array($entry->getExtension(), $extensions, true)
                    && !in_array($entry->getFilename(), $exclusions)
                ) {
                    $files[] = $entry->getPathname();
                }
            }

        } catch (\UnexpectedValueException $e) {
            throw new PhassetsInternalException('UnexpectedValueException caught', 0, $e);
        } catch (\RuntimeException $e) {
            throw new PhassetsInternalException('RuntimeException caught', 0, $e);
        }

        return $files;
    }
}
