<?php

namespace Matys333\LogHelperBundle\MessageHandler;

use DateTimeImmutable;
use Matys333\LogHelperBundle\Message\LogMessage;
use Psr\Log\LogLevel;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use ZipArchive;

#[AsMessageHandler]
class LogMessageHandler
{
    public function __invoke(LogMessage $message): bool
    {
        $logger = new Logger(LogLevel::DEBUG, $message->getLogsPath() . '/' . $message->getSelfLogFileName() . '.log');
        $dateTime = new DateTimeImmutable();
        $fileSystem = new Filesystem();
        $zip = new ZipArchive();

        $month = $dateTime->format('m');
        $day = $dateTime->format('d');
        $backupFolderMonth = $message->getLogsBackupPath() . $month;
        $backupFolderDay = $backupFolderMonth . '/' . $day;

        $logger->info('Start ' . $dateTime->format('Y-m-d H:i:s'));

        // Otherwise try to create new backup folder
        if (empty(is_dir($backupFolderMonth))) {
            // First for month
            if (!mkdir($backupFolderMonth, 0777, true)) {
                $logger->error('Log backups month folder ' . $backupFolderMonth . ' failed to create!');
                return false;
            }
        }

        if (empty(is_dir($backupFolderDay))) {
            // Then for the day
            if (!mkdir($backupFolderDay)) {
                $logger->error('Log backups folder ' . $backupFolderDay . ' failed to create!');
                return false;
            }
        }

        // Foreach all defined logs
        foreach ($message->getLogs() as $log) {
            // Do not handle the log helper log file only
            if ($log === $message->getSelfLogFileName()) {
                continue;
            }

            // Get the log file path
            $logFileName = $log . '.log';
            $logFilePath = $message->getLogsPath() . $logFileName;
            // Get the backup log file path
            $backupFilename = $backupFolderDay . '/' . $log . '.zip';

            // If backup file already exist do nothing
            if (!empty(file_exists($backupFilename))) {
                $logger->warning('Log backup file ' . $backupFilename . ' already exists!');
                continue;
            }

            // Ensure the file exists before adding it to the zip archive
            if (file_exists($logFilePath)) {
                // Add log file to the zip archive if backup was true in config
                if ($message->getBackup()) {
                    if ($zip->open($backupFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                        if ($zip->addFile($logFilePath, basename($logFilePath))) {
                            $logger->info($logFilePath . ' was successfully backed up.');
                        } else {
                            $logger->error('There was an error while backing up file ' . $logFilePath);
                            continue;
                        }
                    } else {
                        $logger->error('Failed to create the zip file!');
                        continue;
                    }
                    // Close the zip archive
                    $zip->close();
                    // Remove the old log file
                    $fileSystem->remove($logFilePath);
                }
            } else {
                $logger->error('Log file ' . $logFilePath . ' does not exist!');
            }
        }

        $logger->info('Done ' . $dateTime->format('Y-m-d H:i:s'));
        return true;
    }
}