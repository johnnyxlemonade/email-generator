<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\Collection\CouponCollection;
use Lemonade\EmailGenerator\DTO\CouponData;
use Lemonade\EmailGenerator\Models\Coupon;

/**
 * Interface CouponCollectionServiceInterface
 * Defines the contract for managing coupon collections and items.
 */
interface CouponCollectionServiceInterface
{
    /**
     * Creates a new CouponCollection.
     *
     * @return CouponCollection A new instance of CouponCollection.
     */
    public function createCollection(): CouponCollection;

    /**
     * Creates a new Coupon item from CouponData and adds it to the collection.
     *
     * @param CouponCollection $collection The collection to which the coupon will be added.
     * @param CouponData $data Data Transfer Object (DTO) containing coupon information.
     * @return void
     */
    public function createItem(CouponCollection $collection, CouponData $data): void;

    /**
     * Retrieves all Coupon items from the given collection.
     *
     * @param CouponCollection $collection The collection from which to retrieve items.
     * @return Coupon[] An array of Coupon items.
     */
    public function getAllItems(CouponCollection $collection): array;
}