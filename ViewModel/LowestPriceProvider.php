<?php
/**
 * @author Tomasz Gregorczyk <tomasz@silpion.com.pl>
 */
declare(strict_types=1);

namespace LCB\OmnibusDirective\ViewModel;

use LCB\OmnibusDirective\Model\LowestPrice;
use LCB\OmnibusDirective\Model\LowestPriceFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * @class LowestPriceProvider for lowest price render
 */
class LowestPriceProvider implements ArgumentInterface
{
    /**
     * @var CatalogHelper
     */
    protected CatalogHelper $catalogHelper;

    /**
     * @var LowestPriceFactory
     */
    protected LowestPriceFactory $lowestPriceFactory;

    /**
     * @var PriceHelper
     */
    protected PriceHelper $priceHelper;

    /**
     * @param CatalogHelper $catalogHelper
     * @param LowestPriceFactory $lowestPriceFactory
     * @param PriceHelper $priceHelper
     */
    public function __construct(
        CatalogHelper $catalogHelper,
        LowestPriceFactory $lowestPriceFactory,
        PriceHelper $priceHelper
    ) {
        $this->catalogHelper = $catalogHelper;
        $this->lowestPriceFactory = $lowestPriceFactory;
        $this->priceHelper = $priceHelper;
    }

    /**
     * Get current product
     *
     * @return ProductInterface
     */
    public function getProduct()
    {
        return $this->catalogHelper->getProduct();
    }

    /**
     * Get historical price model
     *
     * @return LowestPrice
     */
    public function getLowestPriceModel(): LowestPrice
    {
        return $this->lowestPriceFactory->create()->load($this->getProduct()->getSku(), 'sku');
    }

    /**
     * Check if block can be shown
     *
     * @return bool
     */
    public function canShowPrice(): bool
    {
        return (float) $this->getProduct()->getPriceInfo()->getPrice(FinalPrice::PRICE_CODE)->getValue()
                < (float) $this->getProduct()->getPrice()
                && $this->getLowestPriceModel()->getPrice();
    }

    /**
     * Format price by currency
     *
     * @param float $price
     * @return string
     */
    public function formatPrice(float $price): string
    {
        return $this->priceHelper->currency($price, true, false);
    }
}
