<?php
/**
 * @author Tomasz Gregorczyk <tomasz@silpion.com.pl>
 */
declare(strict_types=1);

namespace LCB\OmnibusDirective\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @class LowestPrice for lowest price storage
 */
class LowestPrice extends AbstractDb
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('lcb_omnibus_pricing', 'entity_id');
    }
}
