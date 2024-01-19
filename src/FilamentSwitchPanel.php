<?php

/**
 * Represents a switch panel component in the Filament admin panel.
 * This component allows users to switch between different panels in the admin panel.
 */

namespace Akira\FilamentSwitchPanel;

use Closure;
use Filament\Panel;
use Filament\Support\Components\Component;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;

/**
 * Class FilamentSwitchPanel
 *
 * Represents a switch panel component in Filament.
 */
class FilamentSwitchPanel extends Component
{
    protected array | Closure $excludes = [];

    protected bool | Closure | null $visible = null;

    protected bool | Closure | null $canSwitchPanel = true;

    protected bool | Closure $isModalSlideOver = false;

    protected string | Closure | null $modalWidth = null;

    protected bool | Closure $isSimple = false;

    protected array | Closure $icons = [];

    protected int | Closure | null $iconSize = null;

    protected array | Closure $labels = [];

    protected bool $renderIconAsImage = false;

    protected string | Closure $modalHeading = 'Switch Panels';

    protected string $renderHook = 'panels::global-search.before';

    /**
     * Retrieves the view for the switch panel.
     *
     * @param  FilamentSwitchPanel  $static  The switch panel instance.
     * @return View The rendered view for the switch panel.
     */
    public function getSwitchPanelView(FilamentSwitchPanel $static): View
    {
        return view('filament-switch-panel::filament-switch-panel', [
            'currentPanel' => $static->getCurrentPanel(),
            'canSwitchPanels' => $static->isAbleToSwitchPanels(),
            'heading' => $static->getModalHeading(),
            'icons' => $static->getIcons(),
            'iconSize' => $static->getIconSize(),
            'isSimple' => $static->isSimple(),
            'isSlideOver' => $static->isModalSlideOver(),
            'labels' => $static->getLabels(),
            'modalWidth' => $static->getModalWidth(),
            'panels' => $static->getPanels(),
            'renderIconAsImage' => $static->getRenderIconAsImage(),
        ]);
    }

    /**
     * Determines whether the currently authenticated user can access the panel.
     *
     * @return bool Returns true if the user can access the panel, false otherwise.
     */
    public static function canUserAccessPanel(): bool
    {
        if (($user = auth()->user()) === null) {
            return false;
        }

        if (method_exists($user, 'canAccessPanel')) {
            return $user->canAccessPanel(filament()->getCurrentPanel() ?? filament()->getDefaultPanel());
        }

        return true;
    }

    /**
     * Retrieves the current panel.
     *
     * @return Panel The current panel.
     */
    public function getCurrentPanel(): Panel
    {
        return filament()->getCurrentPanel();
    }

    /**
     * Boot function for initializing the Filament switch panel.
     *
     * This function registers a render hook with FilamentView to render the switch panel.
     * The rendered view includes various parameters to customize the panel appearance.
     */
    public static function boot(): void
    {
        $static = static::make();

        FilamentView::registerRenderHook(
            name: $static->getRenderHookName(),
            hook: self::getRenderHook($static),
        );
    }

    /**
     * Create an instance of the current class and configure it.
     *
     * @return static Returns an instance of the current class.
     */
    public static function make(): static
    {
        return app(static::class)
            ->visible(fn () => [self::class, 'canUserAccessPanel'])
            ->configure();
    }

    /**
     * Returns the render hook for the component.
     *
     * @return string The render hook for the component.
     */
    public function getRenderHookName(): string
    {
        return $this->renderHook;
    }

    /**
     * Determines whether the element is visible or not.
     *
     * @return bool Returns true if the element is visible, false otherwise.
     */
    public function isVisible(): bool
    {
        return (bool) $this->evaluate($this->visible);
    }

    /**
     * Determines whether the currently authenticated user is able to switch panels.
     *
     * @return bool Returns true if the user is able to switch panels, false otherwise.
     */
    public function isAbleToSwitchPanels(): bool
    {
        if (($user = auth()->user()) === null) {
            return false;
        }

        if (method_exists($user, 'canSwitchPanels')) {
            return $user->canSwitchPanels();
        }

        return $this->evaluate($this->canSwitchPanel);
    }

    /**
     * Retrieves the modal heading.
     *
     * @return string The modal heading as a string.
     */
    public function getModalHeading(): string
    {
        return (string) $this->evaluate($this->modalHeading);
    }

    /**
     * Retrieves the list of icons.
     *
     * @return array Returns an array containing the list of icons.
     */
    public function getIcons(): array
    {
        return (array) $this->evaluate($this->icons);
    }

    /**
     * Retrieves the size of the icon.
     *
     * @return int The size of the icon.
     */
    public function getIconSize(): int
    {
        return $this->evaluate($this->iconSize) ?? 32;
    }

    /**
     * Checks if the object is simple.
     *
     * @return bool Returns true if the object is simple, false otherwise.
     */
    public function isSimple(): bool
    {
        return (bool) $this->evaluate($this->isSimple);
    }

    /**
     * Check if the modal should slide over or not.
     *
     * @return bool Returns true if modal should slide over, false otherwise.
     */
    public function isModalSlideOver(): bool
    {
        return (bool) $this->evaluate($this->isModalSlideOver);
    }

    /**
     * Returns the labels.
     *
     * This method returns an array of labels by evaluating the "labels" property using the "evaluate" method.
     *
     * @return array The array of labels.
     */
    public function getLabels(): array
    {
        return (array) $this->evaluate($this->labels);
    }

    /**
     * Retrieves the width of the modal.
     *
     * @return string The width of the modal. Can be either a string or 'screen' if not set.
     */
    public function getModalWidth(): string
    {
        return $this->evaluate($this->modalWidth) ?? 'screen';
    }

    /**
     * Retrieves the panels.
     *
     * This method returns an array containing all the panels obtained from the filament's
     * getPanels() method, after rejecting the panels with IDs present in the excludes array.
     *
     * @return array Returns an array of Panel objects that are not excluded.
     */
    public function getPanels(): array
    {
        return collect(filament()->getPanels())
            ->reject(fn (Panel $panel) => in_array($panel->getId(), $this->getExcludes()))
            ->toArray();
    }

    /**
     * Returns the value of the renderIconAsImage property.
     *
     * @return bool Returns the value of the renderIconAsImage property.
     */
    public function getRenderIconAsImage(): bool
    {
        return $this->renderIconAsImage;
    }

    /**
     * Sets the visibility of the element.
     *
     * @param  bool|Closure  $visible  The visibility of the element. Can be either a boolean or a closure.
     * @return static Returns the current instance of the class.
     */
    public function visible(bool | Closure $visible): static
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Sets the condition for whether or not panels can be switched.
     *
     * @param  bool|Closure  $condition  The condition for whether or not panels can be switched. Can be either a boolean or a closure.
     * @return static Returns the current instance of the class.
     */
    public function canSwitchPanels(bool | Closure $condition): static
    {
        $this->canSwitchPanel = $condition;

        return $this;
    }

    /**
     * Retrieves the list of excludes.
     *
     * This method retrieves the list of excludes by evaluating the value of the `$excludes` property using the `evaluate` method.
     *
     * @return array The list of excludes as an array.
     */
    public function getExcludes(): array
    {
        return (array) $this->evaluate($this->excludes);
    }

    /**
     * Sets the excluded panel IDs.
     *
     * @param  array|Closure  $panelIds  The excluded panel IDs. Can be either an array or a closure.
     * @return static Returns the current instance of the class.
     */
    public function excludes(array | Closure $panelIds): static
    {
        $this->excludes = $panelIds;

        return $this;
    }

    /**
     * Sets the heading for the modal.
     *
     * @param  string|Closure  $modalHeading  The heading for the modal. Can be either a string or a closure.
     * @return static Returns the current instance of the class.
     */
    public function modalHeading(string | Closure $modalHeading): static
    {
        $this->modalHeading = $modalHeading;

        return $this;
    }

    /**
     * Sets the icons for the object.
     *
     * @param  array|Closure  $icons  The icons to be set. Can be either an array or a closure.
     * @param  bool  $asImage  Whether to render the icons as images. Defaults to false.
     * @return static Returns the current instance of the class.
     *
     * @throws \Exception If $asImage is true and any of the icons is not a URL.
     */
    public function icons(array | Closure $icons, bool $asImage = false): static
    {
        if ($asImage) {
            foreach ($icons as $key => $icon) {
                if (! str($icon)->startsWith(['http://', 'https://'])) {
                    throw new \Exception('All icons must be URLs when $asImage is true.');
                }
            }
        }

        $this->renderIconAsImage = $asImage;

        $this->icons = $icons;

        return $this;
    }

    /**
     * Sets the size of the icon.
     *
     * @param  int|Closure|null  $size  The size of the icon. Can be either an integer, a closure or null.
     * @return static Returns the current instance of the class.
     */
    public function iconSize(int | Closure | null $size = null): static
    {
        $this->iconSize = $size;

        return $this;
    }

    /**
     * Sets the labels for the object.
     *
     * @param  array|Closure  $labels  The labels to be set. It can be an array or a closure.
     * @return static The object itself.
     */
    public function labels(array | Closure $labels): static
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * Sets the width of the modal.
     *
     * @param  string|Closure|null  $width  The width of the modal. Can be a string, a Closure or null.
     * @return $this Returns the instance of the class to allow method chaining.
     */
    public function modalWidth(string | Closure | null $width = null): static
    {
        $this->modalWidth = $width;

        return $this;
    }

    /**
     * Sets the hook for rendering.
     *
     * @param  string  $hook  The hook for rendering.
     * @return static The current instance of the class.
     */
    public function renderHook(string $hook): static
    {
        $this->renderHook = $hook;

        return $this;
    }

    /**
     * Sets the slide over condition for the element.
     *
     * @param  bool|Closure  $condition  The condition to determine if the element should slide over. Default is true.
     * @return static Returns the instance of the object.
     */
    public function slideOver(bool | Closure $condition = true): static
    {
        $this->isModalSlideOver = $condition;

        return $this;
    }

    /**
     * Sets the simplicity of the object.
     *
     * @param  bool|Closure  $condition  The condition to determine the simplicity of the object.
     *                                   If a boolean value is provided, it will be used directly.
     *                                   If a closure is provided, the closure will be executed and the result will be used.
     *                                   By default, the object is considered simple.
     * @return static The current object instance.
     */
    public function simple(bool | Closure $condition = true): static
    {
        $this->isSimple = $condition;

        return $this;
    }

    /**
     * Retrieves the render hook for the FilamentSwitchPanel.
     *
     * @param  FilamentSwitchPanel  $static  The FilamentSwitchPanel instance to retrieve the render hook for.
     * @return Closure The render hook for the FilamentSwitchPanel.
     */
    private static function getRenderHook(FilamentSwitchPanel $static): Closure
    {
        return function () use ($static) {

            if (! $static->isVisible()) {
                return '';
            }

            return $static->getSwitchPanelView($static);
        };
    }
}
