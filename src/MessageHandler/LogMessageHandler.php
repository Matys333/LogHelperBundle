<?php

namespace Matys333\LogHelperBundle\MessageHandler;

use DateTimeImmutable;
use Matys333\LogHelperBundle\Message\LogMessage;
use Psr\Log\LogLevel;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use ZipArchive;

#[AsMessageHandler]
class LogMessageHandler
{
    public function __invoke(LogMessage $message): bool
    {
        $dateTime = new DateTimeImmutable();
        $fileSystem = new Filesystem();
        $zip = new ZipArchive();
        $logger = new Logger(LogLevel::DEBUG, $message->getLogsPath() . '/' . $message->getSelfLogFileName() . '.log');

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
            // if (!empty(file_exists($backupFilename))) {
            //    $logger->warning('Log backup file ' . $backupFilename . ' already exists!');
            //    continue;
            //}

            // Ensure the file exists before adding it to the zip archive
            if (file_exists($logFilePath)) {
                // Add log file to the zip archive if backup was true in config and the backup file does not exist yet
                if ($message->getBackup() && file_exists($logFilePath) && !file_exists($backupFilename)) {
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

                // Remove the backups if it is in configuration
                if ($message->getRemoveBackups()) {
                    // Get all the zip files from the backup directory and its subdirectories
                    $backupFiles = [];
                    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($message->getLogsBackupPath()));
                    /** @var SplFileInfo $file */
                    foreach ($iterator as $file) {
                        // Add backup if it's valid ZIP file and not a backup directory from current day
                        if ($file->isFile() && $file->getExtension() === 'zip' && $file->getPath() !== $backupFolderDay) {
                            $backupFiles[] = $file->getPathname();
                        }
                    }

                    foreach ($backupFiles as $backupFile) {
                        // Get the last modification of the log backup timestamp and convert it to DateTimeImmutable
                        $fileLastModificationTime = filemtime($backupFile);
                        $fileLastModificationTime = (new DateTimeImmutable())->setTimestamp($fileLastModificationTime);
                        // Get the days since last modification of the log file
                        $daysSinceLastModification = $dateTime->diff($fileLastModificationTime)->days;

                        // If the days for backup removal are same or lower as the days since last modification remove the backup file
                        if ($message->getRemoveBackupsWaitDays() <= $daysSinceLastModification) {
                            $fileSystem->remove($backupFile);
                            $logger->info('Deleted backup log: ' . $backupFile);
                            $backupFilePath = explode('/', $backupFile);
                            $backupFileDay = str_replace(end($backupFilePath), '', $backupFile);
                            $backupFileMonth = preg_replace('/\d+\/$/', '', $backupFileDay);
                            // Remove the backup folders if they are empty
                            if ($this->removeBackupFolder($fileSystem, $backupFileDay)) {
                                $logger->info('Deleted backup log folder: ' . $backupFileDay);
                            }
                            if ($this->removeBackupFolder($fileSystem, $backupFileDay)) {
                                $logger->info('Deleted backup log folder: ' . $backupFileMonth);
                            }
                        }
                    }
                }
            } else {
                $logger->error('Log file ' . $logFilePath . ' does not exist!');
            }
        }

        $logger->info('Done ' . $dateTime->format('Y-m-d H:i:s'));
        return true;
    }

    /**
     * @param Filesystem $filesystem
     * @param string $backupFolderPath
     * @return bool
     */
    private function removeBackupFolder(Filesystem $filesystem, string $backupFolderPath): bool
    {
        if (count(glob($backupFolderPath . '*')) === 0) {
            $filesystem->remove($backupFolderPath);
            return true;
        }
        return false;
    }
}