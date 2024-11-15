<?php
/**
 * @author Tomasz Gregorczyk <tomasz@silpion.com.pl>
 */
declare(strict_types=1);

namespace LCB\OmnibusDirective\Console\Command;

use LCB\OmnibusDirective\Model\LowestPriceFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @class ParseCatalogPrices
 */
class ParseCatalogPrices extends Command
{
    /**
     * @var LowestPriceFactory
     */
    private LowestPriceFactory $lowestPriceFactory;

    /**
     * @var ProductFactory
     */
    private ProductFactory $productFactory;

    /**
     * @var State
     */
    private State $state;

    /**
     * @param ProductFactory $productFactory
     * @param LowestPriceFactory $lowestPriceFactory
     * @param State $state
     */
    public function __construct(
        ProductFactory $productFactory,
        LowestPriceFactory $lowestPriceFactory,
        State $state
    ) {
        parent::__construct();
        $this->productFactory = $productFactory;
        $this->lowestPriceFactory = $lowestPriceFactory;
        $this->state = $state;
    }

    /**
     * Add task to Magento 2 commands
     */
    protected function configure()
    {
        $this->setName('lcb:omnibus:parse:catalog')->setDescription('Update omnibus pricing');
        parent::configure();
    }

    /**
     * Collect pricing into DB storage
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->setAreaCode(Area::AREA_ADMINHTML);
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }

        $productsCollection = $this->productFactory->create()->getCollection()
                ->addAttributeToSelect(['price', 'special_price']);

        $lowestPriceData = [];
        $lowestPriceCollection = $this->lowestPriceFactory->create()->getCollection();
        $lowestPriceCollection->addFieldToFilter('created_at', ['gteq' => date('Y-m-d H:i:s', strtotime('-1 month'))]);

        foreach ($lowestPriceCollection as $lowestPriceEntry) {
            $lowestPriceData[$lowestPriceEntry->getSku()] = (float) $lowestPriceEntry->getPrice();
        }

        $progressBar = new ProgressBar($output, $productsCollection->count());
        $progressBar->start();

        foreach ($productsCollection as $product) {
            try {
                $sku = $product->getSku();
                $finalPrice = (float) $product->getPriceInfo()->getPrice('final_price')->getValue();
                if (!isset($lowestPriceData[$sku]) || $lowestPriceData[$sku] > $finalPrice) {
                    $newEntry = $this->lowestPriceFactory->create();
                    $newEntry->setSku($sku);
                    $newEntry->setPrice($finalPrice);
                    $newEntry->setCreatedAt(date('Y-m-d H:i', time()));
                    $newEntry->save();
                }

                $progressBar->advance();
            } catch (\Exception $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
                if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                    $output->writeln($e->getTraceAsString());
                }
            }
        }

        return Cli::RETURN_SUCCESS;
    }

}
