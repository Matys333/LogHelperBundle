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
    private string $frequency;

    public function __construct(string $frequency)
    {
        $this->frequency = $frequency;
    }

    public function getSchedule(): Schedule
    {
        $message = new LogMessage();

        return (new Schedule())->add(
            RecurringMessage::every($this->frequency, $message)
        );
    }
}