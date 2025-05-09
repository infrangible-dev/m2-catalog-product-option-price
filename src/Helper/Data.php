<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionPrice\Helper;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Item\AbstractItem;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Data
{
    /** @var \Magento\Catalog\Helper\Data */
    protected $catalogHelper;

    public function __construct(\Magento\Catalog\Helper\Data $catalogHelper)
    {
        $this->catalogHelper = $catalogHelper;
    }

    public function getItemOptionPrices(AbstractItem $item): array
    {
        $itemOptionIds = $item->getOptionByCode('option_ids');

        $optionPrices = [];

        if ($itemOptionIds && $itemOptionIds->getValue()) {
            $product = $item->getProduct();

            $finalPrice = $product->getFinalPrice();

            $optionIds = explode(
                ',',
                $itemOptionIds->getValue()
            );

            foreach ($optionIds as $optionId) {
                $option = $product->getOptionById($optionId);

                if ($option) {
                    $itemOption = $item->getOptionByCode('option_' . $option->getId());

                    try {
                        $group = $option->groupFactory($option->getType());
                    } catch (LocalizedException $exception) {
                        continue;
                    }

                    $group->setOption($option);
                    $group->setData(
                        'configuration_item',
                        $item
                    );
                    $group->setData(
                        'configuration_item_option',
                        $itemOption
                    );

                    $optionPrice = $group->getOptionPrice(
                        $itemOption->getValue(),
                        $finalPrice
                    );

                    $optionPrice = $this->catalogHelper->getTaxPrice(
                        $option->getProduct(),
                        $optionPrice,
                        true
                    );

                    $optionPrices[ $optionId ] = $optionPrice;
                }
            }
        }

        return $optionPrices;
    }
}
