<?php

namespace Matys333\LogHelperBundle\MessageHandler;

use DateTimeImmutable;
use Matys333\LogHelperBundle\Message\LogMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use ZipArchive;

#[AsMessageHandler]
class LogMessageHandler
{
    private string $logsPath = 'var/log/';
    private string $logsBackupPath = 'var/log/backup/';
    private string $selfLogFileName = 'log_helper';
    private array $logs = [
        'dev',
        'prod'
    ];
    private LoggerInterface $logger;

    public function __construct(?string $logsPath = null, ?string $logsBackupPath = null, ?string $selfLogFileName = null, ?array $logs = null)
    {
        $this->logsPath = $logsPath ?? $this->logsPath;
        $this->logsBackupPath = $logsBackupPath ?? $this->logsBackupPath;
        $this->selfLogFileName = $selfLogFileName ?? $this->selfLogFileName;
        $this->logs = $logs ?? $this->logs;
        $this->logger = new Logger(null, $this->logsPath . '/' . $this->selfLogFileName . '.log');
    }

    public function __invoke(LogMessage $message): bool
    {
        $dateTime = new DateTimeImmutable();
        $month = $dateTime->format('m');
        $day = $dateTime->format('d');
        $fileSystem = new Filesystem();
        $zip = new ZipArchive();
        $backupFolderMonth = $this->logsBackupPath . $month;
        $backupFolderDay = $backupFolderMonth . '/' . $day;

        // If backup folder already exist we do not want to do anything
        if (!empty(is_dir($backupFolderDay))) {
            $this->logger->warning('Log backups folder ' . $backupFolderDay . ' already exists!');
            return false;
        }

        // Otherwise try to create new backup folder
        if (empty(is_dir($backupFolderMonth))) {
            // First for month
            if (!mkdir($backupFolderMonth, 0777, true)) {
                $this->logger->error('Log backups month folder ' . $backupFolderMonth . ' failed to create!');
                return false;
            }
        }

        if (!mkdir($backupFolderDay)) {
            // Then for the day
            $this->logger->error('Log backups folder ' . $backupFolderDay . ' failed to create!');
            return false;
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
            // Ensure the file exists before adding it to the zip archive
            if (file_exists($logFilePath)) {
                // Get the backup log file path
                $backupFilename = $backupFolderDay . '/' . $log . '.zip';
                // Add log file to the zip archive
                if ($zip->open($backupFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    if ($zip->addFile($logFilePath, basename($logFilePath))) {
                        $this->logger->info($logFilePath . ' was successfully backed up.');
                    } else {
                        $this->logger->error('There was an error while backing up file ' . $logFilePath);
                        return false;
                    }
                } else {
                    $this->logger->error('Failed to create the zip file!');
                    return false;
                }
                // Close the zip archive
                $zip->close();
                // Remove the old log file
                $fileSystem->remove($logFilePath);
            } else {
                $this->logger->error('Log file does not exist!');
                return false;
            }
        }

        return true;
    }
}