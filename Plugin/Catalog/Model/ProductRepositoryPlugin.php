<?php
/**
 * @author Tomasz Gregorczyk <tomasz@silpion.com.pl>
 */
declare(strict_types=1);

namespace LCB\OmnibusDirective\Plugin\Catalog\Model;

use LCB\OmnibusDirective\Model\LowestPriceFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Psr\Log\LoggerInterface;

/**
 * @class ProductRepositoryPlugin
 */
class ProductRepositoryPlugin
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var LowestPriceFactory
     */
    private LowestPriceFactory $lowestPriceFactory;

    /**
     * Initialize dependencies.
     *
     * @param LoggerInterface $logger
     * @param LowestPriceFactory $lowestPriceFactory
     */
    public function __construct(
        LoggerInterface $logger,
        LowestPriceFactory $lowestPriceFactory
    ) {
        $this->logger = $logger;
        $this->lowestPriceFactory = $lowestPriceFactory;
    }

    /**
     * Append Omnibus pricing
     *
     * @param ProductRepositoryInterface $subject
     * @param ProductInterface $product
     * @return ProductInterface
     */
    public function afterGet(ProductRepositoryInterface $subject, ProductInterface $product): ProductInterface
    {
        $lowestPrice  = $this->lowestPriceFactory->create()->load($product->getSku(), 'sku');
        if ($lowestPrice->getId()) {
            $extensionAttributes = $product->getExtensionAttributes();
            $extensionAttributes->setOmnibusPricing($lowestPrice);
        }

        return $product;
    }

    /**
    * afterSave is called after the original save method returns.
    *
    * @param ProductRepositoryInterface $subject
    * @param ProductInterface $result The saved product (return of the save method)
    * @param ProductInterface $product The original product
    * @return ProductInterface
    */
    public function afterSave(
        ProductRepositoryInterface $subject,
        ProductInterface $result,
        ProductInterface $product
    ) {
        $lowestPriceModelCollection = $this->lowestPriceFactory->create()
            ->getCollection()
            ->addFieldToFilter('created_at', ['gteq' => date('Y-m-d H:i:s', strtotime('-1 month'))])
            ->addFieldToFilter('sku', $product->getSku());

        $lowestPriceModelCollection->getSelect()->order('price', 'DESC');
        $lowestPriceModel = $lowestPriceModelCollection->getLastItem();

        $specialPrice = $product->getPriceInfo()->getPrice(FinalPrice::PRICE_CODE)->getValue();
        if ((float) $specialPrice < (float) $product->getPrice() && (float) $specialPrice !== $lowestPriceModel->getPrice()) {
            try {
                $newEntry = $this->lowestPriceFactory->create();
                $newEntry->setSku($product->getSku());
                $newEntry->setPrice($specialPrice);
                $newEntry->setCreatedAt(date('Y-m-d H:i', time()));
                $newEntry->save();
            } catch (\Exception $e) {
                $this->logger->crit($e->getMessage());
            }
        }

        return $result;
    }
}
