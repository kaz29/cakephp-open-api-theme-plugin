<?php
declare(strict_types=1);

namespace OpenApiTheme\Utility;

use Bake\Utility\TemplateRenderer as BaseTemplateRenderer;
use Bake\View\BakeView;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\View\View;

class TemplateRenderer extends BaseTemplateRenderer
{
    /**
     * Constructor
     *
     * @param string $theme The template theme/plugin to use.
     */
    public function __construct(?string $theme = 'OpenApiTheme')
    {
        $this->theme = $theme ?? '';
    }

    /**
     * Get view instance
     *
     * @return \Cake\View\View
     * @triggers Bake.initialize $view
     */
    public function getView(): View
    {
        if ($this->view) {
            return $this->view;
        }

        $this->viewBuilder()
            ->setHelpers(['Bake.Bake', 'Bake.DocBlock', 'OpenApiTheme.OpenApiDocBlock', 'OpenApiTheme.OpenApiText'])
            ->setTheme($this->theme);

        $view = $this->createView(BakeView::class);
        $event = new Event('Bake.initialize', $view);
        EventManager::instance()->dispatch($event);
        /** @var \Bake\View\BakeView $view */
        $view = $event->getSubject();
        $this->view = $view;

        return $this->view;
    }
}
