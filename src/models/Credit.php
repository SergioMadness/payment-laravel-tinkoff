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
}