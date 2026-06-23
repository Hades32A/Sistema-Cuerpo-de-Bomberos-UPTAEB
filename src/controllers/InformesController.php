<?php

namespace App\Controllers;

class InformesController
{
    public function index($db)
    {
        // Por ahora cargamos la vista directamente para el diseño interactivo
        include 'src/views/modules/informes.php';
    }
}