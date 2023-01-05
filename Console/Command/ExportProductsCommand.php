<?php

declare(strict_types=1);

namespace Api53\Api53\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Api53\Api53\Helper\Data as Api53Helper;
use Api53\Api53\Model\Api as Api53ModelApi;

class ExportProductsCommand extends Command
{
    /**
     * @var EmarsysModelApiApi
     */
	protected $collectionFactory;

    /**
     * @var EmarsysModelApiApi
     */
	protected $productFactory;

     /**
     * @var EmarsysModelApiApi
     */
    protected $api53Helper;

     /**
     * @var EmarsysModelApiApi
     */
    protected $api;

    public function __construct(
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		Api53Helper $api53Helper,
        Api53ModelApi $api
    ) {
		$this->collectionFactory = $collectionFactory;
		$this->productFactory = $productFactory;
        $this->api53Helper = $api53Helper;
        $this->api = $api;
        parent::__construct('api53:export-products');
    }

    protected function configure(): void
    {
        $this->setName('api53:export-products');
        $this->setDescription('Sends initial catalog to API53 backend.');
        
        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
     protected function execute(InputInterface $input, OutputInterface $output): int
     {
         $exitCode = 0;

         //$output->writeln('<comment>Some comment.</comment>');

         try {
            if ($this->api53Helper->isEnabled()) {
                
                // Load initial catalog
                $apiKey = $this->api53Helper->getApiKey();
                $skuAmount = $this->api53Helper->getSkuAmount();
                $this->api->setApiKey($apiKey);

                $response = $this->api->checkSubscriptionSKU();
                if ($response->getStatusCode() != 200) {
                    $msg = "";
                    if ($response->getStatusCode() == 401) {
                        $msg = __("Unauthorized - Your Shop API key is wrong.");
                    }
                    elseif ($response->getStatusCode() == 403) {
                        $msg = __("Forbidden.");
                    }
                    throw new \Magento\Framework\Exception\LocalizedException($msg);
                }

                $output->writeln(sprintf("Start exporting products to Api53. This could take a while.\nPlease do not interrupt the execution!"));
        
                // Limit products 
                $productCollection = $this->collectionFactory->create();		
                $productCollection->addAttributeToSelect('*');
                $productCollection->addAttributeToFilter('type_id', \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $productCollection->getSelect()->limit($skuAmount);
        
                // Build data array
                $chunkSize = 25;
                $total = 0;
                $count = 0;
                $data = [];		
                foreach ($productCollection as $_product) {
                    $total++;
                    $count++;
                    $product = $this->productFactory->create()->load($_product->getId());
                    $data[] = $this->api53Helper->getProductData($product);
                    if ($count == $chunkSize) {
                        $this->api->updateOrCreateProduct($data);
                        $count = 0;
                        $data = [];		
                    }
                }
                if ($count > 0 ) {
                    $this->api->updateOrCreateProduct($data);
                }
                $output->writeln(sprintf(
                    '<error>%s products exported successfully.</error>',
                    $total
                ));
            }

         } catch (Exception $e) {
             $output->writeln(sprintf(
                 '<error>%s</error>',
                 $e->getMessage()
             ));
             $exitCode = 1;
         }
         
         return $exitCode;
     }
}