<?php

declare(strict_types=1);

namespace LCB\OmnibusDirective\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Extended product attributes interface.
 *
 * @api
 */
interface LowestPriceInterface extends ExtensibleDataInterface
{
    /**
     * @var string
     */
    public const SKU = 'sku';

    /**
     * @var string
     */
    public const PRICE = 'price';

    /**
     * Get product SKU
     *
     * @return string
     */
    public function getSku(): string;

    /**
     * Set Product SKU
     *
     * @param string $sku
     * @return $this
     */
    public function setSku(string $sku): self;

    /**
     * Get lowest price
     *
     * @return float
     */
    public function getPrice(): float;

    /**
     * Set lowest price
     *
     * @param string $price
     * @return $this
     */
    public function setPrice(float $price): self;
}
