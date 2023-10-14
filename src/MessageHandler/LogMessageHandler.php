<?php

namespace Matys333\LogHelperBundle\MessageHandler;

use DateTimeImmutable;
use Matys333\LogHelperBundle\Message\LogMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use ZipArchive;

#[AsMessageHandler]
class LogMessageHandler
{
    private LoggerInterface $logger;

    const LOGS_PATH = 'var/log/';

    const LOGS_BACKUP_PATH = self::LOGS_PATH . 'backup/';

    const LOGS = [
        'dev',
        'prod'
    ];

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(LogMessage $message): bool
    {
        $dateTime = new DateTimeImmutable();
        $month = $dateTime->format('m');
        $day = $dateTime->format('d');

        // Foreach all defined logs
        foreach (self::LOGS as $log) {
            $fileSystem = new Filesystem();
            $zip = new ZipArchive();
            $logFileName = $log . '.log';
            $logFilePath = self::LOGS_PATH . $logFileName;
            $backupFolder = self::LOGS_BACKUP_PATH . $month . '/' . $day;

            // Create new backup folder if it does not exist
            if (empty(is_dir($backupFolder))) {
                mkdir($backupFolder, 0777, true);
            }

            // Add log file to the zip archive
            $backupFilename = $backupFolder . '/' . $log . '.zip';
            if ($zip->open($backupFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                // Ensure the file exists before adding it to the zip archive
                if (file_exists($logFilePath)) {
                    if ($zip->addFile($logFilePath, basename($logFilePath))) {
                        $this->logger->info($logFilePath . ' was successfully backed up.');
                    } else {
                        $this->logger->error('There was an error while backing up file ' . $logFilePath);
                        return false;
                    }
                } else {
                    $this->logger->error('Log file does not exist!');
                    return false;
                }
                // Close the zip archive
                $zip->close();
                // Remove the old log file
                $fileSystem->remove($logFilePath);
            } else {
                $this->logger->error('Failed to create the zip file!');
                return false;
            }
        }

        return true;
    }
}