<?php

declare(strict_types=1);

namespace App\Logs;

use DateTime;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class LogAccessor
{
    public function __construct(private string $logDirectory) {}

    public function storeLog(array $contents): UuidInterface
    {
        $uuid = Uuid::uuid4();

        file_put_contents(
            $this->logDirectory . '/' . $uuid,
            json_encode($contents),
        );

        return $uuid;
    }

    public function getRequests(): array
    {
        $handle = opendir($this->logDirectory);
        if (false === $handle) {
            return [];
        }

        $files = [];

        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != ".." && $entry != ".gitignore") {
                $filename = $this->logDirectory . '/' . $entry;

                $fileInfo = new \SplFileInfo($filename);

                $files[] = [
                    'filename' => $fileInfo->getFilename(),
                    'createdAt' => DateTime::createFromTimestamp($fileInfo->getCTime())->format('Y-m-d H:i:s'),
                ];
            }
        }

        closedir($handle);

        return $files;
    }

    public function getRequest(string $uuid): ?array
    {
        $filename = $this->logDirectory . '/' . $uuid;

        $contents = file_get_contents(
            $filename,
        );
        if (false === $contents) {
            return null;
        }

        $fileInfo = new \SplFileInfo($filename);

        return [
            'filename' => $fileInfo->getFilename(),
            'createdAt' => DateTime::createFromTimestamp($fileInfo->getCTime())->format('Y-m-d H:i:s'),
            'contents' => json_decode($contents, true),
        ];
    }
}
