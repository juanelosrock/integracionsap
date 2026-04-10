<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class WebformLayout extends Component
{
    public function __construct(public string $titulo = 'Formulario de Pedido')
    {
    }

    public function render(): View
    {
        return view('webform.layout');
    }
}
