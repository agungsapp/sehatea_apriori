<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

class SidebarDrop extends Component
{
    public $title;
    public $icon;
    public $routes;
    public $menuItems;
    public $menuId;

    /**
     * Create a new component instance.
     */
    public function __construct($title = '', $icon = '', $routes = [], $menuItems = [], $menuId = null)
    {
        $this->title = $title;
        $this->icon = $icon;
        $this->routes = is_array($routes) ? $routes : [];
        $this->menuItems = is_array($menuItems) ? $menuItems : [];
        $this->menuId = $menuId ?? str_replace(' ', '-', strtolower($title));
    }

    public function isActive()
    {
        if (empty($this->routes)) {
            return false;
        }
        return in_array(Route::currentRouteName(), $this->routes);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sidebar-drop');
    }
}
