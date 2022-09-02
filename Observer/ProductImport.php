<?php
namespace Api53\Api53\Observer;

use Magento\Framework\Event\Observer;
use Api53\Api53\Model\Api as Api53ModelApi;

class ProductImport implements \Magento\Framework\Event\ObserverInterface
{

    public function execute(Observer $observer) { 
		switch ($observer->getEvent()->getName()) {
			case "catalog_product_import_bunch_save_after":
				$productImport=$observer->getEvent()->getData('bunch');
				$this->catalogProductImportBunchSaveAfter($productImport);
				break;
	
			case "catalog_product_import_bunch_delete_commit_before":
				$idsToDelete = $observer->getEvent()->getData('ids_to_delete');
				$this->catalogProductImportBunchDeleteCommitBefore($idsToDelete);
				break;
		}
    }

    /*
     * @itechinsiders
     * Product Trigger Import
     *
     * */
    private function catalogProductImportBunchSaveAfter($productImport) { 
		foreach($productImport as $importId){
			$productId= $this->product->getIdBySku($importId['sku']);
			// do your stuff here
		}
    }

    private function catalogProductImportBunchDeleteCommitBefore($idsToDelete) { 
       // do your stuff Here
    }
}
?>