<?php namespace professionalweb\payment\interfaces\models;

/**
 * Interface for credit model
 * @package professionalweb\payment\interfaces\models
 */
interface Credit
{
    public const STATUS_APPROVED = 'approved';

    public const STATUS_IN_PROGRESS = 'inprogress';

    public const STATUS_SIGNED = 'signed';

    public const STATUS_ISSUED = 'issued';

    public const STATUS_CANCELED = 'canceled';

    public const STATUS_NEW = 'new';

    public const STATUS_REJECTED = 'rejected';

    public const TYPE_CREDIT = 'credit';

    public const TYPE_INSTALLMENT = 'installment_credit';

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

    /**
     * Get request status
     *
     * @return string
     */
    public function getStatus(): string;

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime;

    /**
     * Check request is commited
     *
     * @return bool
     */
    public function isCommitted(): bool;

    /**
     * Get first payment amount
     *
     * @return float
     */
    public function getFirstPayment(): float;

    /**
     * Get order amount
     *
     * @return float
     */
    public function getOrderAmount(): float;

    /**
     * Get credit amount
     *
     * @return float
     */
    public function getCreditAmount(): float;

    /**
     * Get credit type
     *
     * @return string
     */
    public function getCreditType(): string;

    /**
     * Get credit length
     *
     * @return int
     */
    public function getMonthQty(): int;

    /**
     * Get monthly amount
     *
     * @return float
     */
    public function getMonthlyPayment(): float;

    /**
     * Get user first name
     *
     * @return string
     */
    public function getFirstName(): string;

    /**
     * Get user last name
     *
     * @return string
     */
    public function getLastName(): string;

    /**
     * Get user middle name
     *
     * @return string
     */
    public function getMiddleName(): string;

    /**
     * Get user phone
     *
     * @return string
     */
    public function getPhone(): string;

    /**
     * Get user e-mail
     *
     * @return string
     */
    public function getEmail(): string;

    /**
     * Get loan number
     *
     * @return string
     */
    public function getLoanNumber(): string;
}