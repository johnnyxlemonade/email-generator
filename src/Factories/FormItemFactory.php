<?php

namespace Lemonade\EmailGenerator\Factories;

use Lemonade\EmailGenerator\DTO\FormItemData;
use Lemonade\EmailGenerator\Models\FormItem;

class FormItemFactory
{
    /**
     * Creates an instance of `FormItem` based on the data from `FormItemData`.
     *
     * @param FormItemData $data The data for creating the form item.
     * @return FormItem A new instance of FormItem.
     */
    public static function createFromDTO(FormItemData $data): FormItem
    {
        return new FormItem($data->name, $data->value);
    }
}
