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

    public function __construct(?string $frequency = null,
                                ?string $logsPath = null,
                                ?string $selfLogFileName = null,
                                ?string $logs = null,
                                ?bool   $backup = null,
                                ?int    $removeWaitDays = null,
                                ?string $logsBackupPath = null,
                                ?bool   $removeBackups = null,
                                ?int    $removeBackupsWaitDays = null)
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

    public function setFrequency(?string $frequency): void
    {
        $this->frequency = $frequency;
    }

    public function getLogsPath(): ?string
    {
        return $this->logsPath;
    }

    public function setLogsPath(?string $logsPath): void
    {
        $this->logsPath = $logsPath;
    }

    public function getSelfLogFileName(): ?string
    {
        return $this->selfLogFileName;
    }

    public function setSelfLogFileName(?string $selfLogFileName): void
    {
        $this->selfLogFileName = $selfLogFileName;
    }

    public function getLogs(): ?array
    {
        return $this->logs;
    }

    public function setLogs(?array $logs): void
    {
        $this->logs = $logs;
    }

    public function getBackup(): ?bool
    {
        return $this->backup;
    }

    public function setBackup(?bool $backup): void
    {
        $this->backup = $backup;
    }

    public function getRemoveWaitDays(): ?int
    {
        return $this->removeWaitDays;
    }

    public function setRemoveWaitDays(?int $removeWaitDays): void
    {
        $this->removeWaitDays = $removeWaitDays;
    }

    public function getLogsBackupPath(): ?string
    {
        return $this->logsBackupPath;
    }

    public function setLogsBackupPath(?string $logsBackupPath): void
    {
        $this->logsBackupPath = $logsBackupPath;
    }

    public function getRemoveBackups(): ?bool
    {
        return $this->removeBackups;
    }

    public function setRemoveBackups(?bool $removeBackups): void
    {
        $this->removeBackups = $removeBackups;
    }

    public function getRemoveBackupsWaitDays(): ?int
    {
        return $this->removeBackupsWaitDays;
    }

    public function setRemoveBackupsWaitDays(?int $removeBackupsWaitDays): void
    {
        $this->removeBackupsWaitDays = $removeBackupsWaitDays;
    }
}