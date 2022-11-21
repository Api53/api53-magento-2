<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Api53\Api53\Model\Config\Source;

class UpdateStock implements \Magento\Framework\Data\OptionSourceInterface
{
 public function toOptionArray()
 {
  return [
    ['value' => '1', 'label' => __('When order is placed')],
    ['value' => '2', 'label' => __('When order is shipped')],
  ];
 }
}