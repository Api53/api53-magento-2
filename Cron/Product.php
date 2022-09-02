<?php

namespace Api53\Api53\Cron;

use Api53\Api53\Helper\Data as Api53Helper;
use Api53\Api53\Model\Api as Api53ModelApi;

class Product
{
    /**
     * @var EmarsysModelApiApi
     */
	protected $collectionFactory;

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
		Api53Helper $api53Helper,
        Api53ModelApi $api,
        array $data = []
    ) {
		$this->collectionFactory = $collectionFactory;
        $this->api53Helper = $api53Helper;
        $this->api = $api;
        //parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }
	
	public function execute()
	{
		if ($this->api53Helper->isEnabled() && $this->api53Helper->isCronEnabled()) {

			// Load initial catalog
			$apiKey = $this->api53Helper->getApiKey();
			$skuAmount = $this->api53Helper->getSkuAmount();
			$this->api->setApiKey($apiKey);
			
			// Limit products 
			$productCollection = $this->collectionFactory->create();		
			$productCollection->addAttributeToSelect('*');
			$productCollection->getSelect()->limit($skuAmount);
	
			// Build data array
			$chunkSize = 25;
			$count = 0;
			$data = [];		
			foreach ($productCollection as $product) {
				$count++;
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
			$this->api53Helper->setCronEnabled('0');
			return $this;
		}

	}
}