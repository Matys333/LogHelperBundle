<?php

namespace Matys333\LogHelperBundle\MessageHandler;

use DateTimeImmutable;
use Matys333\LogHelperBundle\Message\LogMessage;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use ZipArchive;

#[AsMessageHandler]
class LogMessageHandler
{
    private ?string $logsPath;
    private ?string $selfLogFileName;
    private ?array $logs;
    private ?bool $backup;
    private ?int $removeWaitDays;
    private ?string $logsBackupPath;
    private ?bool $removeBackups;
    private ?int $removeBackupsWaitDays;
    private LoggerInterface $logger;

    public function __construct(?string $logsPath = null,
                                ?string $selfLogFileName = null,
                                ?string $logs = null,
                                ?bool   $backup = null,
                                ?int    $removeWaitDays = null,
                                ?string $logsBackupPath = null,
                                ?bool   $removeBackups = null,
                                ?int    $removeBackupsWaitDays = null)
    {
        // Ensure that the configurations are in a right format
        $this->logsPath = trim($logsPath, '/') . '/';
        $this->selfLogFileName = rtrim($selfLogFileName, '.log');
        $this->logs = array_map('trim', explode(',', trim($logs, ',')));
        $this->backup = $backup;
        $this->removeWaitDays = $removeWaitDays;
        $this->logsBackupPath = trim($logsBackupPath, '/') . '/';
        $this->removeBackups = $removeBackups;
        $this->removeBackupsWaitDays = $removeBackupsWaitDays;
        $this->logger = new Logger(LogLevel::DEBUG, $this->logsPath . '/' . $this->selfLogFileName . '.log');
    }

    public function __invoke(LogMessage $message): bool
    {
        $dateTime = new DateTimeImmutable();
        $fileSystem = new Filesystem();
        $zip = new ZipArchive();

        $month = $dateTime->format('m');
        $day = $dateTime->format('d');
        $backupFolderMonth = $this->logsBackupPath . $month;
        $backupFolderDay = $backupFolderMonth . '/' . $day;

        $this->logger->info('Start ' . $dateTime->format('Y-m-d H:i:s'));

        // Otherwise try to create new backup folder
        if (empty(is_dir($backupFolderMonth))) {
            // First for month
            if (!mkdir($backupFolderMonth, 0777, true)) {
                $this->logger->error('Log backups month folder ' . $backupFolderMonth . ' failed to create!');
                return false;
            }
        }

        if (empty(is_dir($backupFolderDay))) {
            // Then for the day
            if (!mkdir($backupFolderDay)) {
                $this->logger->error('Log backups folder ' . $backupFolderDay . ' failed to create!');
                return false;
            }
        }

        // Foreach all defined logs
        foreach ($this->logs as $log) {
            // Do not handle the log helper log file only
            if ($log === $this->selfLogFileName) {
                continue;
            }

            // Get the log file path
            $logFileName = $log . '.log';
            $logFilePath = $this->logsPath . $logFileName;
            // Get the backup log file path
            $backupFilename = $backupFolderDay . '/' . $log . '.zip';

            // If backup file already exist do nothing
            if (!empty(file_exists($backupFilename))) {
                $this->logger->warning('Log backup file ' . $backupFilename . ' already exists!');
                continue;
            }

            // Ensure the file exists before adding it to the zip archive
            if (file_exists($logFilePath)) {
                // Add log file to the zip archive if backup was true in config
                if ($this->backup) {
                    if ($zip->open($backupFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                        if ($zip->addFile($logFilePath, basename($logFilePath))) {
                            $this->logger->info($logFilePath . ' was successfully backed up.');
                        } else {
                            $this->logger->error('There was an error while backing up file ' . $logFilePath);
                            continue;
                        }
                    } else {
                        $this->logger->error('Failed to create the zip file!');
                        continue;
                    }
                    // Close the zip archive
                    $zip->close();
                    // Remove the old log file
                    $fileSystem->remove($logFilePath);
                }
            } else {
                $this->logger->error('Log file ' . $logFilePath . ' does not exist!');
            }
        }

        $this->logger->info('Done ' . $dateTime->format('Y-m-d H:i:s'));
        return true;
    }
}