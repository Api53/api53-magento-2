<?php
namespace Api53\Api53\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Backend\Model\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $backendUrl;

    protected $allCategories;

	protected $cacheTypeList;

    const XML_API53_ENABLED = 'api53/settings/enabled';

    const CHUNK_SIZE = 25;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        UrlInterface $backendUrl,
        StoreManagerInterface $storeManager,
        ProductMetadataInterface $productMetadata,
		\Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory,
		 \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
		 \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
		 \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,		 
		 \Magento\Catalog\Model\ResourceModel\Product $productResource,
		 \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
         \Magento\CatalogInventory\Api\StockStateInterface $stockStateInterface,
         \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
		 \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $configCollection
		
    ) {
         $this->httpContext = $httpContext;
         $this->backendUrl = $backendUrl;
         $this->storeManager = $storeManager;
         $this->productMetadata = $productMetadata;
		 $this->productRepositoryFactory = $productRepositoryFactory;
		 $this->attributeRepository = $attributeRepository;
		 $this->categoryRepository = $categoryRepository;
		 $this->configWriter = $configWriter;		 
		 $this->productResource = $productResource;
		 $this->searchCriteriaBuilder = $searchCriteriaBuilder;
		 $this->stockStateInterface = $stockStateInterface;
		 $this->stockRegistry = $stockRegistry;
		 $this->configCollection = $configCollection;

         parent::__construct($context);
    }

    /*
     * Check if module is enabled or not
     * @return boolean
     */
    public function isEnabled(){
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_API53_ENABLED,
                    ScopeInterface::SCOPE_STORE);
        
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
        
    }
    
    /*
     * Get Max Allowed Weight
     * @return integer
     */    
    public function getApiKey(){
        return $this->scopeConfig->getValue('api53/settings/api_key',
                    ScopeInterface::SCOPE_STORE);
    }
    
    /*
     * Get Max Allowed Weight
     * @return integer
     */    
    public function getSkuAmount(){
        return $this->scopeConfig->getValue('api53/settings/sku_amount',
                    ScopeInterface::SCOPE_STORE);
    }   
    
    /*
     * Get Max Allowed Weight
     * @return integer
     */    
    public function getUpdateStock(){
        return $this->scopeConfig->getValue('api53/settings/update_stock',
                    ScopeInterface::SCOPE_STORE);
    }   
    
    /*
     * Get Max Allowed Weight
     * @return integer
     */    
    public function isCronEnabled(){

        $isEnabled = true;
		
		$collection = $this->configCollection->create();
        $collection->addFieldToFilter("path",['eq'=>"api53/settings/cron_enabled"]);

        if ($collection->count() > 0) { 
            $enabled = $collection->getFirstItem()->getData()['value'];
			if ($enabled == null || $enabled == '0') {
				$isEnabled = false;
			}
        }

        return $isEnabled;
    }   
    
    /*
     * Get Max Allowed Weight
     * @return integer
     */    
    public function setCronEnabled($enable){
        return $this->configWriter->save('api53/settings/cron_enabled', $enable);

    }   
    
    /*
     * Get Max Allowed Weight
     * @return integer
     */    
    public function getProductData($product, $qtyShipped=0){
			
		$sku  =  $product->getSku();
		
		$imageUrl  = null;
		$categories = [];
		$customAttributes = [];
		$data = [];
		
		// product stock
		$stockItem = $this->stockRegistry->getStockItemBySku($sku);

		if ($stockItem->getQty() - $qtyShipped > 0) {
			// all categories
			if ($this->allCategories == null){
				$category_id = 2; //default category
				$allCategories = [];
				$category = $this->categoryRepository->get($category_id);
				$level1Categories = $category->getChildrenCategories();
				foreach ($level1Categories as $level1Category) {
					$tempCategories = [];
					$tempCategories[$level1Category->getId()] = $level1Category->getName();
					$level2Categories = $level1Category->getChildrenCategories();
					foreach ($level2Categories as $level2Category) {
						$tempCategories[$level2Category->getId()] = $level2Category->getName();
						$level3Categories = $level2Category->getChildrenCategories();
						foreach ($level3Categories as $level3Category) {
							$tempCategories[$level3Category->getId()] = $level3Category->getName();
						}
					}
					$allCategories[] =  $tempCategories;
				}
				$this->allCategories = $allCategories;
			}
			
			// selected categories
			$categories = [];
			$categoryIds = $product->getCategoryIds();
			foreach ($categoryIds as $categoryId) {
				$category = $this->categoryRepository->get($categoryId);
				if ($parentCategories = $category->getParentCategories()) { 
					foreach ($parentCategories as $parentCategory) {
						$categories[$parentCategory->getId()] = $parentCategory->getName(); 
					}
				}
			}
			
			// final categories
			$finalCategories = [];
			foreach($this->allCategories as $allCategory) {
				$finalCategory = array_values(array_intersect_key($allCategory, $categories));
				if (count($finalCategory) > 0) {
					$finalCategories[] = $finalCategory;
				}
			}

			// product custom attributes
			$searchCriteria = $this->searchCriteriaBuilder->addFilter('is_user_defined', 1)->create();
			$attributeRepository = $this->attributeRepository->getList(
				\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE,
				$searchCriteria
			);
			foreach ($attributeRepository->getItems() as $items) {
			$customAttributes[$items->getFrontendLabel()] = $this->productResource->getAttribute($items->getAttributeCode())->getFrontend()->getValue($product);
			}
			$customAttributes['short_description'] = $product->getShortDescription();
					
			//$store = $this->storeManager->getStore();
			//$imageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' .$product->getData('image');
			
			// build data array
			$data = [
				"name" => $product->getName(),
				"sku" => $product->getSku(),
				"price" => $product->getPrice(),
				"quantity" => ($stockItem->getQty() - $qtyShipped),
				"brand" => $product->getAttributeText('manufacturer'),
				//"imageBase" => $imageUrl,
				"weight" => $product->getWeight(),
				"description" => $product->getDescription(),
				"category" => json_encode($finalCategories),
				"customAttributes" => json_encode($customAttributes)
			];
			$images1 = $product->getMediaGalleryImages('images')->setOrder('position','ASC');
			$images2 = [];
			foreach ($images1 as $image) {
				$images2[$image->getPosition()] = $image->getUrl();
			}
			ksort($images2);
			$i = 1;
			foreach ($images2 as $image) {
				$data["image".$i] = $image;
				$i++;
			}			
		}
		
		return $data;
    }   
}