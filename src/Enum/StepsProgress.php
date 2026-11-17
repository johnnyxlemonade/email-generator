<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Enum;

/**
 * Process phase used when determining the state of every step
 */
enum StepsProgress
{
    case NotStarted;
    case InProgress;
    case Completed;
}
