<?php

namespace Matys333\LogHelperBundle\MessageHandler;

use Matys333\LogHelperBundle\Message\LogMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class LogMessageHandler
{
    public function __invoke(LogMessage $message): void
    {
        dump('OK');die;
    }
}