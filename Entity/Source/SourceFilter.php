<?php

/**
 * This file is part of a Spipu Bundle
 *
 * (c) Laurent Minguet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spipu\DashboardBundle\Entity\Source;

use Spipu\UiBundle\Form\Options\OptionsInterface;

class SourceFilter
{
    /**
     * @var string
     */
    private string $code;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $entityField;

    /**
     * @var OptionsInterface
     */
    private OptionsInterface $options;

    /**
     * @var bool
     */
    private bool $multiple = false;

    /**
     * @param string $code
     * @param string $name
     * @param string $entityField
     * @param OptionsInterface $options
     */
    public function __construct(
        string $code,
        string $name,
        string $entityField,
        OptionsInterface $options
    ) {
        $this->code = $code;
        $this->name = $name;
        $this->entityField = $entityField;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEntityField(): string
    {
        return $this->entityField;
    }

    /**
     * @return OptionsInterface
     */
    public function getOptions(): OptionsInterface
    {
        return $this->options;
    }

    /**
     * @return bool
     */
    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    /**
     * @param bool $multiple
     * @return SourceFilter
     */
    public function setMultiple(bool $multiple): SourceFilter
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * @param mixed $inputValue
     * @param mixed $value
     * @return bool
     */
    public function isSelected($inputValue, $value): bool
    {
        if (is_array($inputValue) && in_array($value, $inputValue)) {
            return true;
        }
        if (is_string($inputValue) && $inputValue === $value) {
            return true;
        }

        return false;
    }
}
