<?php
/**
 * @author Tomasz Gregorczyk <tomasz@silpion.com.pl>
 */
declare(strict_types=1);

namespace LCB\OmnibusDirective\Model\ResourceModel\LowestPrice;

use LCB\OmnibusDirective\Model\LowestPrice;
use LCB\OmnibusDirective\Model\ResourceModel\LowestPrice as LowestPriceResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @class Collection for lowest price storage
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(
            LowestPrice::class,
            LowestPriceResource::class
        );
    }
}
