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
}