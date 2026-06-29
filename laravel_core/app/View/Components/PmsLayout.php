<?php

namespace App\View\Components;

use Illuminate\View\Component;

class PmsLayout extends Component
{
    public function __construct(
        public string $pageTitle = 'Dashboard',
        public ?string $pageSubtitle = null,
    ) {}

    public function render()
    {
        return view('layouts.pms');
    }
}
