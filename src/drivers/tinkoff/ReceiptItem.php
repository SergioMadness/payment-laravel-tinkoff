<?php namespace professionalweb\payment\drivers\tinkoff;

use professionalweb\payment\drivers\receipt\ReceiptItem as IReceiptItem;

/**
 * Receipt item
 * @package professionalweb\payment\drivers\tinkoff
 */
class ReceiptItem extends IReceiptItem
{
    /**
     * без НДС
     */
    public const TAX_NO_VAT = 'none';

    /**
     * НДС по ставке 0%
     */
    public const TAX_VAT_0 = 'vat0';

    /**
     * НДС чека по ставке 10%
     */
    public const TAX_VAT_10 = 'vat10';

    /**
     * НДС чека по ставке 18%
     */
    public const TAX_VAT_18 = 'vat18';

    /**
     * НДС чека по расчетной ставке 10/110
     */
    public const TAX_VAT_110 = 'vat110';

    /**
     * НДС чека по расчетной ставке 18/118
     */
    public const TAX_VAT_118 = 'vat118';

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'Name'     => mb_substr($this->getName(), 0, 128),
            'Price'    => $this->getPrice() * 100,
            'Quantity' => $this->getQty(),
            'Amount'   => $this->getPrice() * 100 * $this->getQty(),
            'Tax'      => $this->getTax(),
        ];
    }
}