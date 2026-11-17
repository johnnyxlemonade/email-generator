<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Enum\StepsProgress;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Validation\InputValidator;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * Ordered process steps with one current position and derived states
 */
final readonly class Steps implements ComponentInterface
{
    /**
     * @var list<string|Translation>
     */
    private array $steps;

    /**
     * Validates step names, the one-based current position and accessible state labels
     *
     * @param list<string|Translation> $steps
     */
    public function __construct(
        array $steps,
        private ?int $currentStep = 1,
        private StepsProgress $progress = StepsProgress::InProgress,
        private string|Translation $completedLabel = 'Completed',
        private string|Translation $currentLabel = 'Current',
        private string|Translation $pendingLabel = 'Pending',
    ) {
        if (!array_is_list($steps) || $steps === []) {
            throw new InvalidInputException(ErrorCode::StepsInvalid, 'Steps must be a non-empty list of names.');
        }

        $snapshot = [];

        foreach ($steps as $step) {
            if (!is_string($step) && !$step instanceof Translation) {
                throw new InvalidInputException(
                    ErrorCode::StepsInvalid,
                    'Step names must be strings or Translation objects.',
                );
            }

            InputValidator::text($step);

            $snapshot[] = $step;
        }

        if (
            $progress === StepsProgress::InProgress
            && ($currentStep === null || $currentStep < 1 || $currentStep > count($snapshot))
        ) {
            throw new InvalidInputException(
                ErrorCode::StepsCurrentInvalid,
                'An in-progress process requires a one-based current position in the steps list.',
            );
        }

        if ($progress !== StepsProgress::InProgress && $currentStep !== null) {
            throw new InvalidInputException(
                ErrorCode::StepsCurrentInvalid,
                'A process that has not started or is complete cannot have a current step.',
            );
        }

        InputValidator::text($completedLabel);
        InputValidator::text($currentLabel);
        InputValidator::text($pendingLabel);

        $this->steps = $snapshot;
    }

    /**
     * Returns step names in process order
     *
     * @return list<string|Translation>
     */
    public function steps(): array
    {
        return $this->steps;
    }

    /**
     * Returns the one-based current position
     */
    public function currentStep(): ?int
    {
        return $this->currentStep;
    }

    /**
     * Returns whether the process has not started, is active or is complete
     */
    public function progress(): StepsProgress
    {
        return $this->progress;
    }

    /**
     * Returns the completed-state label
     */
    public function completedLabel(): string|Translation
    {
        return $this->completedLabel;
    }

    /**
     * Returns the current-state label
     */
    public function currentLabel(): string|Translation
    {
        return $this->currentLabel;
    }

    /**
     * Returns the pending-state label
     */
    public function pendingLabel(): string|Translation
    {
        return $this->pendingLabel;
    }
}
