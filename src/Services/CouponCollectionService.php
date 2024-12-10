<?php

namespace Lemonade\EmailGenerator\Services;

use Lemonade\EmailGenerator\Collection\CouponCollection;
use Lemonade\EmailGenerator\DTO\CouponData;
use Lemonade\EmailGenerator\Factories\CouponFactory;

/**
 * Class CouponCollectionService
 * Provides functionalities for managing collections of coupons.
 */
class CouponCollectionService extends AbstractCollectionService implements CouponCollectionServiceInterface
{
    /**
     * @var CouponFactory Factory for creating Coupon instances.
     */
    private CouponFactory $couponFactory;

    /**
     * Constructor for CouponCollectionService.
     * Initializes the service with a CouponFactory.
     *
     * @param CouponFactory $couponFactory Factory for creating Coupon instances.
     */
    public function __construct(CouponFactory $couponFactory)
    {
        $this->couponFactory = $couponFactory;
    }

    /**
     * Creates a new CouponCollection.
     *
     * @return CouponCollection A new instance of CouponCollection.
     */
    public function createCollection(): CouponCollection
    {
        return new CouponCollection();
    }

    /**
     * Creates a new Coupon item from CouponData and adds it to the collection.
     *
     * @param CouponCollection $collection The collection to which the coupon will be added.
     * @param CouponData $data Data Transfer Object (DTO) containing coupon information.
     */
    public function createItem(CouponCollection $collection, CouponData $data): void
    {
        // Use the factory to create a coupon from the DTO data
        $coupon = $this->couponFactory->createFromDTO($data);

        // Add the coupon to the collection
        $collection->add($coupon);
    }
}
