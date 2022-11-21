<?php

namespace Api53\Api53\Observer;

use Magento\Framework\Event\ObserverInterface;
use Api53\Api53\Helper\Data as Api53Helper;
use Api53\Api53\Model\Api as Api53ModelApi;

class SalesOrderPlaceAfter implements ObserverInterface
{
     protected $api53Helper;
     protected $api;

	 public function __construct(
		  Api53Helper $api53Helper,
          Api53ModelApi $api
     ) {
         $this->api53Helper = $api53Helper;
         $this->api = $api;
     } 

	 public function execute(\Magento\Framework\Event\Observer $observer)
    {
		if ($this->api53Helper->getUpdateStock() == '1') {
	        $order= $observer->getEvent()->getData('order');
			// Do some things
			$orderItems = $order->getAllItems();
			
			$data = [];		
			foreach ($orderItems as $item) {
				$data[] = $this->api53Helper->getProductData($item->getProduct(), $item->getQtyOrdered());
			}
	
			if (count($data) > 0) {
				$apiKey = $this->api53Helper->getApiKey();
				$this->api->setApiKey($apiKey);
				$this->api->updateOrCreateProduct($data);
			}
			//throw new \Magento\Framework\Exception\LocalizedException(__(print_r($data, true)));
		}
	}
}