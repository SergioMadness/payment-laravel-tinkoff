<?php namespace professionalweb\payment\drivers\tinkoff;

use professionalweb\payment\drivers\receipt\Receipt as IReceipt;

/**
 * Receipt
 * @package professionalweb\payment\drivers\tinkoff
 */
class Receipt extends IReceipt
{
    /**
     * общая СН
     */
    public const TAX_SYSTEM_COMMON = 'osn';

    /**
     * упрощенная СН (доходы)
     */
    public const TAX_SYSTEM_SIMPLE_INCOME = 'usn_income';

    /**
     * упрощенная СН (доходы минус расходы)
     */
    public const TAX_SYSTEM_SIMPLE_NO_OUTCOME = 'usn_income_outcome';

    /**
     * единый налог на вмененный доход
     */
    public const TAX_SYSTEM_SIMPLE_UNIFIED = 'envd';

    /**
     * единый сельскохозяйственный налог
     */
    public const TAX_SYSTEM_SIMPLE_AGRO = 'esn';

    /**
     * патентная СН
     */
    public const TAX_SYSTEM_SIMPLE_PATENT = 'patent';

    /**
     * Phone number
     *
     * @var string
     */
    private $email;

    /**
     * Receipt constructor.
     *
     * @param string     $phone
     * @param string     $email
     * @param array|null $items
     * @param int        $taxSystem
     */
    public function __construct(?string $phone = null, ?string $email = null, array $items = [], ?string $taxSystem = null)
    {
        parent::__construct($phone, $items, $taxSystem);
        $this->setEmail($email);
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param $email
     *
     * @return $this
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Receipt to array
     *
     * @return array
     */
    public function toArray()
    {
        $items = array_map(function ($item) {
            /** @var ReceiptItem $item */
            return $item->toArray();
        }, $this->getItems());

        $result = [
            'Phone' => $this->getPhone(),
            'Email' => $this->getEmail(),
            'Items' => $items,
        ];
        if (($taxSystem = $this->getTaxSystem()) !== null) {
            $result['Taxation'] = $taxSystem;
        }

        return $result;
    }

    /**
     * Receipt to json
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->toArray());
    }
}