<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Composition;

use Lemonade\EmailGenerator\Component\ButtonGroup;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Contract\CompositionInterface;
use Lemonade\EmailGenerator\Exception\CompositionException;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;

/**
 * Snapshots logical compositions while preserving visual groups and columns
 */
final class ComponentExpander
{
    /**
     * Expands compositions while retaining container boundaries and shared limits
     * Leaf components are retained unchanged; renderer support is checked during rendering
     *
     * @param iterable<ComponentInterface> $components
     * @return list<ComponentInterface>
     */
    public static function expand(iterable $components): array
    {
        $visited = 0;

        return self::expandBranch($components, [], $visited);
    }

    /**
     * Recursively expands a branch while enforcing cycle, depth and shared size limits
     *
     * @param iterable<ComponentInterface> $components
     * @param list<ComponentInterface> $ancestors
     * @return list<ComponentInterface>
     */
    private static function expandBranch(iterable $components, array $ancestors, int &$visited): array
    {
        if (count($ancestors) > 32) {
            throw new CompositionException(
                ErrorCode::CompositionDepthExceeded,
                'Composition nesting exceeds 32 levels.',
            );
        }

        $result = [];

        foreach ($components as $component) {
            if (++$visited > 10000) {
                throw new CompositionException(
                    ErrorCode::CompositionSizeExceeded,
                    'A composition expansion may contain at most 10000 nodes.',
                );
            }

            if (in_array($component, $ancestors, true)) {
                throw new CompositionException(
                    ErrorCode::CompositionCycle,
                    'Cyclic email composition.',
                );
            }

            if (!$component instanceof ComponentInterface) {
                throw new InvalidInputException(
                    ErrorCode::ComponentUnsupported,
                    'Composition entries must implement ComponentInterface.',
                );
            }

            if ($component instanceof Group) {
                $result[] = new Group(
                    children: self::expandBranch($component->children(), [...$ancestors, $component], $visited),
                    backgroundColor: $component->backgroundColor(),
                    padding: $component->padding(),
                    borderColor: $component->borderColor(),
                );

                continue;
            }

            if ($component instanceof Columns) {
                $result[] = new Columns(
                    first: self::expandBranch($component->first(), [...$ancestors, $component], $visited),
                    second: self::expandBranch($component->second(), [...$ancestors, $component], $visited),
                    gap: $component->gap(),
                    firstWidth: $component->firstWidth(),
                    mobileFirstWidth: $component->mobileFirstWidth(),
                );

                continue;
            }

            if ($component instanceof ButtonGroup) {
                $visited += count($component->buttons());

                if ($visited > 10000) {
                    throw new CompositionException(
                        ErrorCode::CompositionSizeExceeded,
                        'A composition expansion may contain at most 10000 nodes.',
                    );
                }

                $result[] = new ButtonGroup(
                    buttons: $component->buttons(),
                    gap: $component->gap(),
                    alignment: $component->alignment(),
                );

                continue;
            }

            if ($component instanceof CompositionInterface) {
                array_push(
                    $result,
                    ...self::expandBranch($component->components(), [...$ancestors, $component], $visited),
                );

                continue;
            }

            $result[] = $component;
        }

        return $result;
    }
}
