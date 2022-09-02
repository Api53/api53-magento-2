<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Api53\Api53\Model\Config\Backend;

use Api53\Api53\Helper\Data as Api53Helper;
use Api53\Api53\Model\Api as Api53ModelApi;

/**
 * Backend model for domain config value
 */
class SkuAmount extends \Magento\Framework\App\Config\Value
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
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,	
		Api53Helper $api53Helper,
        Api53ModelApi $api,
        array $data = []
    ) {
		$this->collectionFactory = $collectionFactory;
        $this->api53Helper = $api53Helper;
        $this->api = $api;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

   /**
     * Validate a domain name value
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
		$apiKey = $this->getData('groups/settings/fields/api_key/value');

		if ($apiKey != "") {
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
	
			$json = json_decode($response->getBody()->getContents());
			$this->setValue($json->skuAmound);
		}
		else {
			$this->setValue('0');
		}

        return parent::beforeSave();
    }

    /**
     * @return $this
     */
    public function afterSave() 
    { 
		$this->api53Helper->setCronEnabled('1');
        return parent::afterSave();
    }	
}