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

namespace Spipu\DashboardBundle\Entity\Dashboard;

class Dashboard
{
    private string $code;
    private string $route;
    private array $templates = [
        'show' => [
            'main'    => '@SpipuDashboard/main/dashboard-show.html.twig',
            'all'     => '@SpipuDashboard/show/all.html.twig',
            'js'      => '@SpipuDashboard/show/js.html.twig',
            'header'  => '@SpipuDashboard/show/header.html.twig',
            'filters' => '@SpipuDashboard/show/filters.html.twig',
            'page'    => '@SpipuDashboard/show/page.html.twig',
            'row'     => '@SpipuDashboard/show/row.html.twig',
            'col'     => '@SpipuDashboard/show/col.html.twig',
        ],
        'configure' => [
            'main'    => '@SpipuDashboard/main/dashboard-configure.html.twig',
            'all'     => '@SpipuDashboard/configure/all.html.twig',
            'js'      => '@SpipuDashboard/configure/js.html.twig',
        ],
        'widget' => [
            'all'     => '@SpipuDashboard/widget/all.html.twig',
        ],
    ];

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setTemplateShowMain(string $value): self
    {
        return $this->setTemplate('show', 'main', $value);
    }

    public function setTemplateShowAll(string $value): self
    {
        return $this->setTemplate('show', 'all', $value);
    }

    public function setTemplateShowJs(string $value): self
    {
        return $this->setTemplate('show', 'js', $value);
    }

    public function setTemplateShowHeader(string $value): self
    {
        return $this->setTemplate('show', 'header', $value);
    }

    public function setTemplateShowFilters(string $value): self
    {
        return $this->setTemplate('show', 'filters', $value);
    }

    public function setTemplateShowPage(string $value): self
    {
        return $this->setTemplate('show', 'page', $value);
    }

    public function setTemplateShowRow(string $value): self
    {
        return $this->setTemplate('show', 'row', $value);
    }

    public function setTemplateShowCol(string $value): self
    {
        return $this->setTemplate('show', 'col', $value);
    }

    public function setTemplateConfigureMain(string $value): self
    {
        return $this->setTemplate('configure', 'main', $value);
    }

    public function setTemplateConfigureAll(string $value): self
    {
        return $this->setTemplate('configure', 'all', $value);
    }

    public function setTemplateConfigureJs(string $value): self
    {
        return $this->setTemplate('configure', 'js', $value);
    }

    public function setTemplateWidgetMain(string $value): self
    {
        return $this->setTemplate('widget', 'main', $value);
    }

    public function getTemplateShowMain(): string
    {
        return $this->getTemplate('show', 'main');
    }

    public function getTemplateShowAll(): string
    {
        return $this->getTemplate('show', 'all');
    }

    public function getTemplateShowJs(): string
    {
        return $this->getTemplate('show', 'js');
    }

    public function getTemplateShowHeader(): string
    {
        return $this->getTemplate('show', 'header');
    }

    public function getTemplateShowFilters(): string
    {
        return $this->getTemplate('show', 'filters');
    }

    public function getTemplateShowPage(): string
    {
        return $this->getTemplate('show', 'page');
    }

    public function getTemplateShowCol(): string
    {
        return $this->getTemplate('show', 'col');
    }

    public function getTemplateShowRow(): string
    {
        return $this->getTemplate('show', 'row');
    }

    public function getTemplateConfigureMain(): string
    {
        return $this->getTemplate('configure', 'main');
    }

    public function getTemplateConfigureAll(): string
    {
        return $this->getTemplate('configure', 'all');
    }

    public function getTemplateConfigureJs(): string
    {
        return $this->getTemplate('configure', 'js');
    }

    public function getTemplateWidgetAll(): string
    {
        return $this->getTemplate('widget', 'all');
    }

    public function setRoute(string $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    private function setTemplate(string $action, string $code, string $value): self
    {
        $this->templates[$action][$code] = $value;

        return $this;
    }

    private function getTemplate(string $action, string $code): string
    {
        return $this->templates[$action][$code];
    }
}
