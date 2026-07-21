<?php

declare(strict_types=1);

namespace common\discovery\dto;

/**
 * DTO обнаруженного устройства.
 *
 * Содержит сведения об устройстве, идентифицированном Probe.
 * Используется для передачи информации в подсистему инвентаризации.
 */
class DiscoveredDevice
{
    /**
     * Тип устройства.
     */
    private ?string $type = null;

    /**
     * Производитель.
     */
    private ?string $vendor = null;

    /**
     * Модель.
     */
    private ?string $model = null;

    /**
     * Серийный номер.
     */
    private ?string $serialNumber = null;

    /**
     * MAC-адрес.
     */
    private ?string $mac = null;

    /**
     * IP-адрес.
     */
    private ?string $ip = null;

    /**
     * Имя устройства.
     */
    private ?string $hostname = null;

    /**
     * Дополнительные свойства устройства.
     *
     * @var array<string, mixed>
     */
    private array $attributes = [];

    /**
     * Возвращает тип устройства.
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Устанавливает тип устройства.
     *
     * @param string|null $type
     *
     * @return static
     */
    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Возвращает производителя.
     *
     * @return string|null
     */
    public function getVendor(): ?string
    {
        return $this->vendor;
    }

    /**
     * Устанавливает производителя.
     *
     * @param string|null $vendor
     *
     * @return static
     */
    public function setVendor(?string $vendor): static
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * Возвращает модель устройства.
     *
     * @return string|null
     */
    public function getModel(): ?string
    {
        return $this->model;
    }

    /**
     * Устанавливает модель устройства.
     *
     * @param string|null $model
     *
     * @return static
     */
    public function setModel(?string $model): static
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Возвращает серийный номер.
     *
     * @return string|null
     */
    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    /**
     * Устанавливает серийный номер.
     *
     * @param string|null $serialNumber
     *
     * @return static
     */
    public function setSerialNumber(?string $serialNumber): static
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    /**
     * Возвращает MAC-адрес.
     *
     * @return string|null
     */
    public function getMac(): ?string
    {
        return $this->mac;
    }

    /**
     * Устанавливает MAC-адрес.
     *
     * @param string|null $mac
     *
     * @return static
     */
    public function setMac(?string $mac): static
    {
        $this->mac = $mac;

        return $this;
    }

    /**
     * Возвращает IP-адрес.
     *
     * @return string|null
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * Устанавливает IP-адрес.
     *
     * @param string|null $ip
     *
     * @return static
     */
    public function setIp(?string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Возвращает имя устройства.
     *
     * @return string|null
     */
    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    /**
     * Устанавливает имя устройства.
     *
     * @param string|null $hostname
     *
     * @return static
     */
    public function setHostname(?string $hostname): static
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * Возвращает все дополнительные свойства.
     *
     * @return array<string, mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Устанавливает дополнительные свойства.
     *
     * @param array<string, mixed> $attributes
     *
     * @return static
     */
    public function setAttributes(array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Проверяет существование свойства.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        return array_key_exists($name, $this->attributes);
    }

    /**
     * Возвращает значение свойства.
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * Устанавливает значение свойства.
     *
     * @param string $name
     * @param mixed $value
     *
     * @return static
     */
    public function setAttribute(string $name, mixed $value): static
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Удаляет свойство.
     *
     * @param string $name
     *
     * @return static
     */
    public function removeAttribute(string $name): static
    {
        unset($this->attributes[$name]);

        return $this;
    }

    /**
     * Очищает список дополнительных свойств.
     *
     * @return static
     */
    public function clearAttributes(): static
    {
        $this->attributes = [];

        return $this;
    }
}
