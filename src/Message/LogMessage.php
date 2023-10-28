<?php

namespace Matys333\LogHelperBundle\Message;

class LogMessage
{
    private ?string $frequency;
    private ?string $logsPath;
    private ?string $selfLogFileName;
    private ?array $logs;
    private ?bool $backup;
    private ?int $removeWaitDays;
    private ?string $logsBackupPath;
    private ?bool $removeBackups;
    private ?int $removeBackupsWaitDays;

    public function __construct(?string $frequency,
                                ?string $logsPath,
                                ?string $selfLogFileName,
                                ?string $logs,
                                ?bool   $backup,
                                ?int    $removeWaitDays,
                                ?string $logsBackupPath,
                                ?bool   $removeBackups,
                                ?int    $removeBackupsWaitDays)
    {
        // Ensure that the configurations are in a right format
        $this->frequency = $frequency;
        $this->logsPath = trim($logsPath, '/') . '/';
        $this->selfLogFileName = rtrim($selfLogFileName, '.log');
        $this->logs = array_map('trim', explode(',', trim($logs, ',')));
        $this->backup = $backup;
        $this->removeWaitDays = $removeWaitDays;
        $this->logsBackupPath = trim($logsBackupPath, '/') . '/';
        $this->removeBackups = $removeBackups;
        $this->removeBackupsWaitDays = $removeBackupsWaitDays;
    }

    public function getFrequency(): ?string
    {
        return $this->frequency;
    }

    public function getLogsPath(): ?string
    {
        return $this->logsPath;
    }

    public function getSelfLogFileName(): ?string
    {
        return $this->selfLogFileName;
    }

    public function getLogs(): ?array
    {
        return $this->logs;
    }

    public function getBackup(): ?bool
    {
        return $this->backup;
    }

    public function getRemoveWaitDays(): ?int
    {
        return $this->removeWaitDays;
    }

    public function getLogsBackupPath(): ?string
    {
        return $this->logsBackupPath;
    }

    public function getRemoveBackups(): ?bool
    {
        return $this->removeBackups;
    }

    public function getRemoveBackupsWaitDays(): ?int
    {
        return $this->removeBackupsWaitDays;
    }
}