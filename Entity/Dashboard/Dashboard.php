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

use Spipu\DashboardBundle\Exception\WidgetException;

class Dashboard
{
    /**
     * @var string
     */
    private string $code;

    /**
     * @var string
     */
    private string $route;

    /**
     * @var string[]
     */
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

    /**
     * @param string $code
     */
    public function __construct(string $code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $value
     * @return self
     */
    public function setTemplateShowMain(string $value): self
    {
        return $this->setTemplate('show', 'main', $value);
    }

    /**
     * @param string $value
     * @return self
     */
    public function setTemplateShowAll(string $value): self
    {
        return $this->setTemplate('show', 'all', $value);
    }

    /**
     * @param string $value
     * @return self
     */
    public function setTemplateShowJs(string $value): self
    {
        return $this->setTemplate('show', 'js', $value);
    }


    /**
     * @param string $value
     * @return self
     */
    public function setTemplateShowHeader(string $value): self
    {
        return $this->setTemplate('show', 'header', $value);
    }


    /**
     * @param string $value
     * @return self
     */
    public function setTemplateShowFilters(string $value): self
    {
        return $this->setTemplate('show', 'filters', $value);
    }


    /**
     * @param string $value
     * @return self
     */
    public function setTemplateShowPage(string $value): self
    {
        return $this->setTemplate('show', 'page', $value);
    }

    /**
     * @param string $value
     * @return self
     */
    public function setTemplateShowRow(string $value): self
    {
        return $this->setTemplate('show', 'row', $value);
    }

    /**
     * @param string $value
     * @return self
     */
    public function setTemplateShowCol(string $value): self
    {
        return $this->setTemplate('show', 'col', $value);
    }

    /**
     * @param string $value
     * @return self
     */
    public function setTemplateConfigureMain(string $value): self
    {
        return $this->setTemplate('configure', 'main', $value);
    }

    /**
     * @param string $value
     * @return self
     */
    public function setTemplateConfigureAll(string $value): self
    {
        return $this->setTemplate('configure', 'all', $value);
    }

    /**
     * @param string $value
     * @return self
     */
    public function setTemplateConfigureJs(string $value): self
    {
        return $this->setTemplate('configure', 'js', $value);
    }

    /**
     * @param string $value
     * @return self
     */
    public function setTemplateWidgetMain(string $value): self
    {
        return $this->setTemplate('widget', 'main', $value);
    }

    /**
     * @return string
     */
    public function getTemplateShowMain(): string
    {
        return $this->getTemplate('show', 'main');
    }

    /**
     * @return string
     */
    public function getTemplateShowAll(): string
    {
        return $this->getTemplate('show', 'all');
    }

    /**
     * @return string
     */
    public function getTemplateShowJs(): string
    {
        return $this->getTemplate('show', 'js');
    }

    /**
     * @return string
     */
    public function getTemplateShowHeader(): string
    {
        return $this->getTemplate('show', 'header');
    }

    /**
     * @return string
     */
    public function getTemplateShowFilters(): string
    {
        return $this->getTemplate('show', 'filters');
    }

    /**
     * @return string
     */
    public function getTemplateShowPage(): string
    {
        return $this->getTemplate('show', 'page');
    }

    /**
     * @return string
     */
    public function getTemplateShowCol(): string
    {
        return $this->getTemplate('show', 'col');
    }

    /**
     * @return string
     */
    public function getTemplateShowRow(): string
    {
        return $this->getTemplate('show', 'row');
    }

    /**
     * @return string
     */
    public function getTemplateConfigureMain(): string
    {
        return $this->getTemplate('configure', 'main');
    }

    /**
     * @return string
     */
    public function getTemplateConfigureAll(): string
    {
        return $this->getTemplate('configure', 'all');
    }

    /**
     * @return string
     */
    public function getTemplateConfigureJs(): string
    {
        return $this->getTemplate('configure', 'js');
    }

    /**
     * @return string
     */
    public function getTemplateWidgetAll(): string
    {
        return $this->getTemplate('widget', 'all');
    }

    /**
     * @param string $route
     * @return $this
     */
    public function setRoute(string $route): self
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @param string $action
     * @param string $code
     * @param string $value
     * @return $this
     * @throws WidgetException
     */
    private function setTemplate(string $action, string $code, string $value): self
    {
        if (!isset($this->templates[$action][$code])) {
            throw new WidgetException('Unknown template');
        }

        $this->templates[$action][$code] = $value;

        return $this;
    }

    /**
     * @param string $action
     * @param string $code
     * @return string
     * @throws WidgetException
     */
    private function getTemplate(string $action, string $code): string
    {
        if (!isset($this->templates[$action][$code])) {
            throw new WidgetException('Unknown template');
        }

        return $this->templates[$action][$code];
    }
}
