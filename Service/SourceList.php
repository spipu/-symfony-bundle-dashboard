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

use Spipu\DashboardBundle\Entity\Source\SourceFilter;
use Spipu\DashboardBundle\Exception\SourceException;
use Spipu\DashboardBundle\Source\SourceDefinitionInterface;
use Spipu\DashboardBundle\Source\SourceProxy;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class SourceList
{
    /**
     * @var Security
     */
    private Security $security;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @var SourceDefinitionInterface[]
     */
    private array $sources = [];

    /**
     * @param Security $security
     * @param TranslatorInterface $translator
     * @param iterable $sources
     */
    public function __construct(
        Security $security,
        TranslatorInterface $translator,
        iterable $sources
    ) {
        $this->translator = $translator;
        $this->security = $security;

        foreach ($sources as $source) {
            $this->addSource($source);
        }
    }

    /**
     * @param SourceDefinitionInterface $source
     * @return void
     */
    private function addSource(SourceDefinitionInterface $source): void
    {
        if ($this->isUserGranted($source)) {
            $this->sources[$source->getDefinition()->getCode()] = (new SourceProxy())->setSource($source);
        }
    }

    /**
     * @return SourceDefinitionInterface[]
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    /**
     * @return string[]
     */
    public function getSourceLabels(): array
    {
        $labels = [];
        foreach ($this->sources as $source) {
            $labels[$source->getDefinition()->getCode()] = $this->getSourceLabel($source);
        }
        asort($labels);

        return $labels;
    }

    /**
     * @param string $code
     * @return SourceDefinitionInterface
     * @throws SourceException
     */
    public function getSource(string $code): SourceDefinitionInterface
    {
        if (!array_key_exists($code, $this->sources)) {
            throw new SourceException('Unknown source code');
        }

        return $this->sources[$code];
    }

    /**
     * @param SourceDefinitionInterface $source
     * @return string
     */
    public function getSourceLabel(SourceDefinitionInterface $source): string
    {
        $code = 'spipu.dashboard.source.' . $source->getDefinition()->getCode() . '.label';

        $value = $this->translator->trans($code);
        if ($value === $code) {
            $value = ucwords(str_replace('-', ' ', $source->getDefinition()->getCode()));
        }

        return $value;
    }

    /**
     * @return array
     */
    public function getDefinitions(): array
    {
        $definition = [];

        foreach ($this->sources as $source) {
            $definition[$source->getDefinition()->getCode()] = [
                'code'            => $source->getDefinition()->getCode(),
                'label'           => $this->getSourceLabel($source),
                'filters'         => $this->getSourceFilters($source),
                'needPeriod'      => ($source->getDefinition()->needPeriod() ? 1 : 0),
                'specificDisplay' => $source->getDefinition()->getSpecificDisplayIcon(),
            ];
        }

        uasort(
            $definition,
            function (array $rowA, array $rowB) {
                return $rowA['label'] <=> $rowB['label'];
            }
        );

        return $definition;
    }

    /**
     * @param SourceDefinitionInterface $source
     * @return array
     */
    public function getSourceFilters(
        SourceDefinitionInterface $source
    ): array {
        if (!$source->getDefinition()->hasFilters()) {
            return [];
        }

        return array_map(
            fn(SourceFilter $filter) => [
                'name' => $this->translator->trans($filter->getName()),
                'options' => $filter->getOptions()->getOptions(),
                'multiple' => $filter->isMultiple(),
            ],
            $source->getDefinition()->getFilters()
        );
    }


    /**
     * @param SourceDefinitionInterface $sourceDefinition
     * @return bool
     */
    private function isUserGranted(SourceDefinitionInterface $sourceDefinition): bool
    {
        if (empty($sourceDefinition->getRolesNeeded())) {
            return true;
        }
        foreach ($sourceDefinition->getRolesNeeded() as $role) {
            if ($this->security->isGranted($role)) {
                return true;
            }
        }
        return false;
    }
}
