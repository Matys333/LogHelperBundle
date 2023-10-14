<?php

namespace Utils;

use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use TomAtom\JobQueueBundle\Message\LogMessage;

#[AsSchedule('log_backup')]
class LogBackupScheduleProvider implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        $message = new LogMessage();

        return (new Schedule())->add(
            RecurringMessage::every('1 day', $message)
        );
    }
}