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

namespace Spipu\DashboardBundle\Service;

use Spipu\DashboardBundle\Entity\Source\Source;
use Spipu\DashboardBundle\Exception\SourceException;
use Spipu\DashboardBundle\Exception\TypeException;
use Spipu\DashboardBundle\Service\Ui\SourceManager;
use Spipu\DashboardBundle\Service\Ui\WidgetManager;
use Symfony\Contracts\Translation\TranslatorInterface;

class WidgetTypeService
{
    public const TYPE_VALUE_SINGLE = 'value_single';
    public const TYPE_VALUE_COMPARE = 'value_compare';
    public const TYPE_GRAPH = 'graph';
    public const TYPE_SPECIFIC = 'specific';

    /**
     * @var string[]
     */
    private array $types = [
        self::TYPE_VALUE_SINGLE,
        self::TYPE_VALUE_COMPARE,
        self::TYPE_GRAPH,
        self::TYPE_SPECIFIC,
    ];

    /**
     * @var string[]
     */
    private array $typesWithoutPeriod = [
        self::TYPE_VALUE_SINGLE,
        self::TYPE_SPECIFIC,
    ];

    /**
     * @var SourceManager
     */
    private SourceManager $sourceManager;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @param SourceManager $sourceManager
     * @param TranslatorInterface $translator
     */
    public function __construct(
        SourceManager $sourceManager,
        TranslatorInterface $translator
    ) {
        $this->sourceManager = $sourceManager;
        $this->translator = $translator;
    }

    /**
     * @return string[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @return array[]
     * @throws TypeException
     */
    public function getDefinitions(): array
    {
        $definition = [];

        foreach ($this->types as $type) {
            $definition[$type] = $this->getDefinition($type);
        }

        return $definition;
    }

    /**
     * @param string $type
     * @return array
     * @throws TypeException
     */
    public function getDefinition(string $type): array
    {
        if (!in_array($type, $this->types)) {
            throw new TypeException('Unknown type');
        }

        return [
            'code' => $type,
            'label' => $this->translator->trans('spipu.dashboard.type.' . $type),
            'height' => $this->getHeight($type),
            'needPeriod' => !in_array($type, $this->typesWithoutPeriod),
        ];
    }

    /**
     * @param string $type
     * @return int
     */
    public function getHeight(string $type): int
    {
        if (in_array($type, [self::TYPE_VALUE_SINGLE, self::TYPE_VALUE_COMPARE])) {
            return 1;
        }

        return 2;
    }

    /**
     * @param WidgetManager $widgetManager
     * @return void
     * @throws SourceException
     * @throws TypeException
     */
    public function initValues(WidgetManager $widgetManager): void
    {
        $widget = $widgetManager->getDefinition();
        if (!in_array($widget->getType(), $this->getAvailableWidgetTypes($widget->getSource()))) {
            throw new TypeException('this type is not allowed');
        }

        $startTime = microtime(true);
        switch ($widget->getType()) {
            case self::TYPE_VALUE_SINGLE:
                $widget->setValues($this->getValuesTypeValueSingle($widgetManager));
                break;
            case self::TYPE_VALUE_COMPARE:
                $widget->setValues($this->getValuesTypeValueCompare($widgetManager));
                break;
            case self::TYPE_GRAPH:
                $widget->setValues($this->getValuesTypeValues($widgetManager));
                break;
            case self::TYPE_SPECIFIC:
                $widget->setValues($this->getValuesTypeSpecific($widgetManager));
                break;
            default:
                throw new TypeException('unknown widget type code');
        }
        $widget->setGenerationTime((int) (1000000. * (microtime(true) - $startTime)));
    }

    /**
     * @param WidgetManager $widgetManager
     * @return array
     * @throws SourceException
     */
    private function getValuesTypeValueSingle(WidgetManager $widgetManager): array
    {
        return [
            'value' => $this->sourceManager->convertValue(
                $widgetManager->getDefinition()->getSource(),
                $widgetManager->getDataProvider()->getValue()
            ),
        ];
    }

    /**
     * @param WidgetManager $widgetManager
     * @return array
     * @throws SourceException
     */
    private function getValuesTypeValueCompare(WidgetManager $widgetManager): array
    {
        return [
            'value' => $this->sourceManager->convertValue(
                $widgetManager->getDefinition()->getSource(),
                $widgetManager->getDataProvider()->getValue()
            ),
            'previous' => $this->sourceManager->convertValue(
                $widgetManager->getDefinition()->getSource(),
                $widgetManager->getDataProvider()->getPreviousValue()
            ),
        ];
    }

    /**
     * @param WidgetManager $widgetManager
     * @return array
     */
    private function getValuesTypeValues(WidgetManager $widgetManager): array
    {
        $values = $widgetManager->getDataProvider()->getValues();

        foreach ($values as $key => $row) {
            $value = $row['v'];
            if ($value !== null) {
                $values[$key]['v'] = (float)$value;
            }
        }

        return $values;
    }

    /**
     * @param WidgetManager $widgetManager
     * @return array
     */
    private function getValuesTypeSpecific(WidgetManager $widgetManager): array
    {
        return $widgetManager->getDataProvider()->getSpecificValues();
    }

    /**
     * @param Source $source
     * @return array
     */
    public function getAvailableWidgetTypes(Source $source): array
    {
        return $source->getDateField() === null ? $this->typesWithoutPeriod : $this->types;
    }
}
