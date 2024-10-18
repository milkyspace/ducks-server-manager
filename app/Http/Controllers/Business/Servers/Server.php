<?php

namespace App\Http\Controllers\Business\Servers;

class Server
{
    private string $address;
    private string $user;
    private string $password;
    private string $defaultProtocol = 'vless';
    private string $defaultHeader = 'google.com';
    private string $defaultTransmission = 'tcp';
    private bool $isSniffingSwitchOn = true;
    private array $sniffingProtocols = ['http', 'tls', 'quic'];

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return Server
     */
    public function setAddress(string $address): Server
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @param string $user
     * @return Server
     */
    public function setUser(string $user): Server
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return Server
     */
    public function setPassword(string $password): Server
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultProtocol(): string
    {
        return $this->defaultProtocol;
    }

    /**
     * @param string $defaultProtocol
     * @return Server
     */
    public function setDefaultProtocol(string $defaultProtocol): Server
    {
        $this->defaultProtocol = $defaultProtocol;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultHeader(): string
    {
        return $this->defaultHeader;
    }

    /**
     * @param string $defaultHeader
     * @return Server
     */
    public function setDefaultHeader(string $defaultHeader): Server
    {
        $this->defaultHeader = $defaultHeader;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultTransmission(): string
    {
        return $this->defaultTransmission;
    }

    /**
     * @param string $defaultTransmission
     * @return Server
     */
    public function setDefaultTransmission(string $defaultTransmission): Server
    {
        $this->defaultTransmission = $defaultTransmission;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSniffingSwitchOn(): bool
    {
        return $this->isSniffingSwitchOn;
    }

    /**
     * @param bool $isSniffingSwitchOn
     * @return Server
     */
    public function setIsSniffingSwitchOn(bool $isSniffingSwitchOn): Server
    {
        $this->isSniffingSwitchOn = $isSniffingSwitchOn;
        return $this;
    }

    /**
     * @return array
     */
    public function getSniffingProtocols(): array
    {
        return $this->sniffingProtocols;
    }

    /**
     * @param array $sniffingProtocols
     * @return Server
     */
    public function setSniffingProtocols(array $sniffingProtocols): Server
    {
        $this->sniffingProtocols = $sniffingProtocols;
        return $this;
    }
}
