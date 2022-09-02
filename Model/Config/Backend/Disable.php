<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Api53\Api53\Model\Config\Backend;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;

class Disable extends Field
{
    protected function _getElementHtml(AbstractElement $element)
    {
        //$element->setDisabled(true);
        return $element->getElementHtml();
    }
}