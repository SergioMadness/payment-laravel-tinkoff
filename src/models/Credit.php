<?php namespace professionalweb\payment\models;

use professionalweb\payment\interfaces\models\Credit as ICredit;

/**
 * Credit model
 * @package professionalweb\payment\models
 */
class Credit implements ICredit
{

    /** @var string */
    private $id;

    /** @var string */
    private $link;

    /** @var string */
    private $status;

    /** @var \DateTime */
    private $createdAt;

    /** @var bool */
    private $commited;

    /** @var float */
    private $firstPayment;

    /** @var float */
    private $orderAmount;

    /** @var float */
    private $creditAmount;

    /** @var string */
    private $creditType;

    /** @var int */
    private $monthQty;

    /** @var float */
    private $monthlyPayment;

    /** @var string */
    private $firstName;

    /** @var string */
    private $middleName;

    /** @var string */
    private $lastName;

    /** @var string */
    private $phone;

    /** @var string */
    private $email;

    /** @var string */
    private $loanNumber;

    public function __construct(string $id, string $link)
    {
        $this->setId($id)->setLink($link);
    }

    /**
     * @param string $id
     *
     * @return Credit
     */
    public function setId(string $id): Credit
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string $link
     *
     * @return Credit
     */
    public function setLink(string $link): Credit
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get request id
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get link to request
     *
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return Credit
     */
    public function setStatus(string $status): Credit
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return Credit
     */
    public function setCreatedAt(\DateTime $createdAt): Credit
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCommitted(): bool
    {
        return $this->commited;
    }

    /**
     * @param bool $committed
     *
     * @return Credit
     */
    public function setIsCommitted(bool $committed): Credit
    {
        $this->commited = $committed;

        return $this;
    }

    /**
     * @return float
     */
    public function getFirstPayment(): float
    {
        return $this->firstPayment;
    }

    /**
     * @param float $firstPayment
     *
     * @return Credit
     */
    public function setFirstPayment(float $firstPayment): Credit
    {
        $this->firstPayment = $firstPayment;

        return $this;
    }

    /**
     * @return float
     */
    public function getOrderAmount(): float
    {
        return $this->orderAmount;
    }

    /**
     * @param float $orderAmount
     *
     * @return Credit
     */
    public function setOrderAmount(float $orderAmount): Credit
    {
        $this->orderAmount = $orderAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getCreditAmount(): float
    {
        return $this->creditAmount;
    }

    /**
     * @param float $creditAmount
     *
     * @return Credit
     */
    public function setCreditAmount(float $creditAmount): Credit
    {
        $this->creditAmount = $creditAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreditType(): string
    {
        return $this->creditType;
    }

    /**
     * @param string $creditType
     *
     * @return Credit
     */
    public function setCreditType(string $creditType): Credit
    {
        $this->creditType = $creditType;

        return $this;
    }

    /**
     * @return int
     */
    public function getMonthQty(): int
    {
        return $this->monthQty;
    }

    /**
     * @param int $monthQty
     *
     * @return Credit
     */
    public function setMonthQty(int $monthQty): Credit
    {
        $this->monthQty = $monthQty;

        return $this;
    }

    /**
     * @return float
     */
    public function getMonthlyPayment(): float
    {
        return $this->monthlyPayment;
    }

    /**
     * @param float $monthlyPayment
     *
     * @return Credit
     */
    public function setMonthlyPayment(float $monthlyPayment): Credit
    {
        $this->monthlyPayment = $monthlyPayment;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return Credit
     */
    public function setFirstName(string $firstName): Credit
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    /**
     * @param string $middleName
     *
     * @return Credit
     */
    public function setMiddleName(string $middleName): Credit
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return Credit
     */
    public function setLastName(string $lastName): Credit
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return Credit
     */
    public function setPhone(string $phone): Credit
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return Credit
     */
    public function setEmail(string $email): Credit
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getLoanNumber(): string
    {
        return $this->loanNumber;
    }

    /**
     * @param string $loanNumber
     *
     * @return Credit
     */
    public function setLoanNumber(string $loanNumber): Credit
    {
        $this->loanNumber = $loanNumber;

        return $this;
    }
}