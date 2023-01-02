<?php
/**
 * @author Tomasz Gregorczyk <tomasz@silpion.com.pl>
 */
declare(strict_types=1);

namespace LCB\OmnibusDirective\Plugin\Catalog\Model;

use LCB\OmnibusDirective\Model\LowestPriceFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ProductRepository;

/**
 * @class ProductRepositoryPlugin
 */
class ProductRepositoryPlugin
{

    /**
     * @var LowestPriceFactory
     */
    private LowestPriceFactory $lowestPriceFactory;

    /**
     * Initialize dependencies.
     *
     * @param LowestPriceFactory $lowestPriceFactory
     */
    public function __construct(
        LowestPriceFactory $lowestPriceFactory
    ) {
        $this->lowestPriceFactory = $lowestPriceFactory;
    }

    /**
     * Append Omnibus pricing
     *
     * @param ProductRepository $subject
     * @param ProductInterface $product
     * @return ProductInterface
     */
    public function afterGet(ProductRepository $subject, ProductInterface $product): ProductInterface
    {
        $lowestPrice  = $this->lowestPriceFactory->create()->load($product->getSku(), 'sku');
        if ($lowestPrice->getId()) {
            $extensionAttributes = $product->getExtensionAttributes();
            $extensionAttributes->setOmnibusPricing($lowestPrice);
        }

        return $product;
    }
}
