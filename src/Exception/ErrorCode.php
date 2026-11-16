<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Exception;

/**
 * Stable machine-readable codes independent of human-readable exception messages
 */
enum ErrorCode: string
{
    case ColorInvalid = 'color.invalid';
    case UrlInvalid = 'url.invalid';
    case TextInvalid = 'text.invalid';
    case TextListInvalid = 'text_list.invalid';
    case ValueOutOfRange = 'value.out_of_range';
    case LocaleInvalid = 'locale.invalid';
    case TranslationKeyInvalid = 'translation.key_invalid';
    case TranslationReplacementsInvalid = 'translation.replacements_invalid';
    case GroupChildrenInvalid = 'group.children_invalid';
    case GroupWidthInvalid = 'group.width_invalid';
    case ColumnsChildrenInvalid = 'columns.children_invalid';
    case ColumnsWidthInvalid = 'columns.width_invalid';
    case TableInvalid = 'table.invalid';
    case ComponentUnsupported = 'component.unsupported';
    case ConfigurationUnknownOption = 'config.unknown_option';
    case ConfigurationInvalidType = 'config.invalid_type';
    case WidthInvalid = 'width.invalid';
    case CompositionCycle = 'composition.cycle';
    case CompositionDepthExceeded = 'composition.depth_exceeded';
    case CompositionSizeExceeded = 'composition.size_exceeded';
}
