<?php

namespace App\Http\Controllers\Business\Servers;

class User
{
    private string $id;
    private ?int $limitIp = null;
    private null|int|string $expiryTime = null;
    private ?bool $enable = null;
    private ?string $userName = null;
    private ?string $type = null;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return User
     */
    public function setId(string $id): User
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserName(): ?string
    {
        return $this->userName;
    }

    /**
     * @param string|null $userName
     * @return User
     */
    public function setUserName(?string $userName): User
    {
        $this->userName = $userName;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLimitIp(): ?int
    {
        return $this->limitIp;
    }

    /**
     * @param int|null $limitIp
     * @return User
     */
    public function setLimitIp(?int $limitIp): User
    {
        $this->limitIp = $limitIp;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     * @return User
     */
    public function setType(?string $type): User
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int|string|null
     */
    public function getExpiryTime(): int|string|null
    {
        return $this->expiryTime;
    }

    /**
     * @param int|string|null $expiryTime
     * @return User
     */
    public function setExpiryTime(int|string|null $expiryTime): User
    {
        $this->expiryTime = $expiryTime;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function isEnable(): ?bool
    {
        return $this->enable;
    }

    /**
     * @param bool|null $enable
     * @return User
     */
    public function setEnable(?bool $enable): User
    {
        $this->enable = $enable;
        return $this;
    }
}
