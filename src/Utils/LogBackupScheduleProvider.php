<?php

namespace Matys333\LogHelperBundle\Utils;

use Matys333\LogHelperBundle\Message\LogMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('log_backup')]
class LogBackupScheduleProvider implements ScheduleProviderInterface
{
    private ?LogMessage $message;

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
        $this->message = new LogMessage($frequency, $logsPath, $selfLogFileName, $logs, $backup, $removeWaitDays, $logsBackupPath, $removeBackups, $removeBackupsWaitDays);
    }

    public function getSchedule(): Schedule
    {
        return (new Schedule())->add(
            RecurringMessage::every($this->message->getFrequency(), $this->message)
        );
    }
}