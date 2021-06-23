<?php namespace professionalweb\payment\interfaces;

use professionalweb\payment\contracts\PayProtocol;
use professionalweb\payment\interfaces\models\Credit;

/**
 * Interface for Tinkoff protocol
 * @package professionalweb\payment\interfaces
 */
interface TinkoffProtocol extends PayProtocol
{
    /**
     * Payment by card token
     *
     * @param array $data
     *
     * @return array
     */
    public function paymentByToken(array $data): array;

    /**
     * Create credit request
     *
     * @param array $data
     * @param bool  $isDemo
     *
     * @return Credit
     */
    public function createCredit(array $data, bool $isDemo = false): Credit;

    /**
     * Get credit info
     *
     * @param string $id
     *
     * @return Credit
     */
    public function getCreditInfo(string $id): Credit;

    /**
     * Commit credit
     *
     * @param string $id
     *
     * @return Credit
     */
    public function commitCredit(string $id): Credit;

    /**
     * Cancel request
     *
     * @param string $id
     *
     * @return Credit
     */
    public function cancelCredit(string $id): Credit;
}