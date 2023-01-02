<?php
/**
 * @author Tomasz Gregorczyk <tomasz@silpion.com.pl>
 */
declare(strict_types=1);

namespace LCB\OmnibusDirective\Model;

use LCB\OmnibusDirective\Api\Data\LowestPriceInterface;
use LCB\OmnibusDirective\Model\ResourceModel\LowestPrice as LowestPriceResource;
use Magento\Framework\Model\AbstractModel;

/**
 * @class LowestPrice for lowest price storage
 */
class LowestPrice extends AbstractModel implements LowestPriceInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(LowestPriceResource::class);
    }

    /**
     * @inheritdoc
     */
    public function getSku(): string
    {
        return $this->getData(self::SKU);
    }

    /**
     * @inheritdoc
     */
    public function setSku(string $sku): self
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * @inheritdoc
     */
    public function getPrice(): float
    {
        return (float) $this->getData(self::PRICE);
    }

    /**
     * @inheritdoc
     */
    public function setPrice(float $price): self
    {
        return $this->setData(self::PRICE, $price);
    }
}
