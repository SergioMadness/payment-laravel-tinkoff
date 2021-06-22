<?php namespace professionalweb\payment\interfaces\models;

/**
 * Interface for credit model
 * @package professionalweb\payment\interfaces\models
 */
interface Credit
{
    /**
     * Get request id
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Get link to request
     *
     * @return string
     */
    public function getLink(): string;
}