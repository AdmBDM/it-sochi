<?php

declare(strict_types=1);

namespace common\discovery\dto;

/**
 * DTO обнаруженного сетевого узла.
 *
 * Используется для накопления информации, полученной различными
 * сканерами сети. Не содержит бизнес-логики и является
 * исключительно контейнером данных.
 */
class NetworkNode
{
    /**
     * IP-адрес узла.
     */
    private ?string $ip = null;

    /**
     * MAC-адрес узла.
     */
    private ?string $mac = null;

    /**
     * Имя узла.
     */
    private ?string $hostname = null;

    /**
     * Источник обнаружения.
     */
    private ?string $source = null;

    /**
     * Дополнительные атрибуты узла.
     *
     * @var array<string, mixed>
     */
    private array $attributes = [];

    /**
     * Возвращает IP-адрес узла.
     *
     * @return string|null
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * Устанавливает IP-адрес узла.
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
     * Возвращает MAC-адрес узла.
     *
     * @return string|null
     */
    public function getMac(): ?string
    {
        return $this->mac;
    }

    /**
     * Устанавливает MAC-адрес узла.
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
     * Возвращает имя узла.
     *
     * @return string|null
     */
    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    /**
     * Устанавливает имя узла.
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
     * Возвращает источник обнаружения.
     *
     * @return string|null
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * Устанавливает источник обнаружения.
     *
     * @param string|null $source
     *
     * @return static
     */
    public function setSource(?string $source): static
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Возвращает все атрибуты.
     *
     * @return array<string, mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Устанавливает набор атрибутов.
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
     * Проверяет существование атрибута.
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
     * Возвращает значение атрибута.
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
     * Устанавливает значение атрибута.
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
     * Удаляет атрибут.
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
     * Очищает список атрибутов.
     *
     * @return static
     */
    public function clearAttributes(): static
    {
        $this->attributes = [];

        return $this;
    }
}
