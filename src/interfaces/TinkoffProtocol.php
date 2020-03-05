<?php namespace professionalweb\payment\interfaces;

use professionalweb\payment\contracts\PayProtocol;

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
}