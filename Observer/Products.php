<?php

namespace Api53\Api53\Observer;

use Magento\Framework\Event\Observer;
use Api53\Api53\Helper\Data as Api53Helper;
use Api53\Api53\Model\Api as Api53ModelApi;

class Products implements \Magento\Framework\Event\ObserverInterface
{
	 protected $product;
	 protected $attributeRepository;
	 protected $categoryRepository;
	 protected $productResource;
	 protected $searchCriteriaBuilder;
     protected $stockStateInterface;
     protected $stockRegistry;
     protected $api53Helper;
     protected $api;
	 
	 public function __construct(
         \Magento\Catalog\Model\Product $product,
		  Api53Helper $api53Helper,
          Api53ModelApi $api
     ) {
         $this->product = $product;
         $this->api53Helper = $api53Helper;
         $this->api = $api;
     }

	public function execute(Observer $observer) { 
		if ($this->api53Helper->isEnabled()) {
			$product = $observer->getEvent()->getProduct();			
			switch ($observer->getEvent()->getName()) {
				case "catalog_product_save_before":
					$this->catalogProductSaveBefore($product);
					break;
				case "catalog_product_save_after":
					$this->catalogProductSaveAfter($product);
					break;
				case "catalog_product_delete_before":
					$this->catalogProductDeleteBefore($product);
					break;
			}
		}
	}
		
	/*
	* @itechInsider
	* Product Trigger Before Save
	* */
	private function catalogProductSaveBefore($product) { 	
		// 
	}
	
	/*
	* @itechInsider
	* Product Trigger After Save
	* */
	private function catalogProductSaveAfter($product) { 
		$data = [
			$this->api53Helper->getProductData($product),
		];
		$apiKey = $this->api53Helper->getApiKey();
		$this->api->setApiKey($apiKey);
		$this->api->updateOrCreateProduct($data);
	}
	
	/*
	* @itechinsiders
	* Product Trigger Delete Before
	* */
	
	private function catalogProductDeleteBefore($product) {
		$data = [
			["sku" => $product->getSku()],
		];
		$apiKey = $this->api53Helper->getApiKey();
		$this->api->setApiKey($apiKey);
		$this->api->deleteProduct($data);
	}
}