<?php

namespace Akira\FilamentSwitchPanel;

use Closure;
use Exception;
use Filament\Panel;
use Filament\Support\Components\Component;
use Filament\Support\Facades\FilamentView;

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

    public function getCurrentPanel(): Panel
    {
        return filament()->getCurrentPanel();
    }

    public static function boot(): void
    {
        $static = static::make();

        FilamentView::registerRenderHook(
            name: $static->getRenderHook(),
            hook: function () use ($static) {
                if (! $static->isVisible()) {
                    return '';
                }

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
            },
        );
    }

    public static function make(): static
    {
        return app(static::class)
            ->visible(fn () => [self::class, 'canUserAccessPanel'])
            ->configure();
    }

    public function getRenderHook(): string
    {
        return $this->renderHook;
    }

    public function isVisible(): bool
    {
        return (bool) $this->evaluate($this->visible);
    }

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

    public function getModalHeading(): string
    {
        return (string) $this->evaluate($this->modalHeading);
    }

    public function getIcons(): array
    {
        return (array) $this->evaluate($this->icons);
    }

    public function getIconSize(): int
    {
        return $this->evaluate($this->iconSize) ?? 32;
    }

    public function isSimple(): bool
    {
        return (bool) $this->evaluate($this->isSimple);
    }

    public function isModalSlideOver(): bool
    {
        return (bool) $this->evaluate($this->isModalSlideOver);
    }

    public function getLabels(): array
    {
        return (array) $this->evaluate($this->labels);
    }

    public function getModalWidth(): string
    {
        return $this->evaluate($this->modalWidth) ?? 'screen';
    }

    /**
     * @return array<string, Panel>
     */
    public function getPanels(): array
    {
        return collect(filament()->getPanels())
            ->reject(fn (Panel $panel) => in_array($panel->getId(), $this->getExcludes()))
            ->toArray();
    }

    public function getRenderIconAsImage(): bool
    {
        return $this->renderIconAsImage;
    }

    public function visible(bool | Closure $visible): static
    {
        $this->visible = $visible;

        return $this;
    }

    public function canSwitchPanels(bool | Closure $condition): static
    {
        $this->canSwitchPanel = $condition;

        return $this;
    }

    public function getExcludes(): array
    {
        return (array) $this->evaluate($this->excludes);
    }

    public function excludes(array | Closure $panelIds): static
    {
        $this->excludes = $panelIds;

        return $this;
    }

    public function modalHeading(string | Closure $modalHeading): static
    {
        $this->modalHeading = $modalHeading;

        return $this;
    }

    /**
     * @throws Exception
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

    public function iconSize(int | Closure | null $size = null): static
    {
        $this->iconSize = $size;

        return $this;
    }

    public function labels(array | Closure $labels): static
    {
        $this->labels = $labels;

        return $this;
    }

    public function modalWidth(string | Closure | null $width = null): static
    {
        $this->modalWidth = $width;

        return $this;
    }

    public function renderHook(string $hook): static
    {
        $this->renderHook = $hook;

        return $this;
    }

    public function slideOver(bool | Closure $condition = true): static
    {
        $this->isModalSlideOver = $condition;

        return $this;
    }

    public function simple(bool | Closure $condition = true): static
    {
        $this->isSimple = $condition;

        return $this;
    }
}
