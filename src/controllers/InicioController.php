<?php

namespace App\Controllers;

use PDO;

class InicioController
{
    public function index($db)
    {
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM personal WHERE estado_personal = 'Activo'");
        $stmt->execute();
        $personal = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare('SELECT COUNT(*) as total FROM insumos WHERE Estado = 0 AND Cantidad > 0');
        $stmt->execute();
        $insumos = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare('SELECT COUNT(*) as total FROM llamada WHERE Fecha = CURDATE()');
        $stmt->execute();
        $emergenciasHoy = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare('SELECT COUNT(*) as total FROM paciente');
        $stmt->execute();
        $pacientes = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("SELECT COUNT(*) as total FROM vehiculo WHERE Estado = 'Operativo'");
        $stmt->execute();
        $vehiculos = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare('
            SELECT COUNT(DISTINCT va.Id_llamadas) as total
            FROM vehiculos_asignados va
            INNER JOIN llamada l ON l.Id_llamadas = va.Id_llamadas
            WHERE l.Fecha = CURDATE()
        ');
        $stmt->execute();
        $operacionesActivas = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare('SELECT COUNT(*) as total FROM insumos WHERE Cantidad <= 5 AND Cantidad > 0');
        $stmt->execute();
        $insumosBajoStock = $stmt->fetch(PDO::FETCH_ASSOC);

        $dashboardData = [
            'personal_activo'     => (int) ($personal['total'] ?? 0),
            'insumos_disponibles' => (int) ($insumos['total'] ?? 0),
            'emergencias_hoy'     => (int) ($emergenciasHoy['total'] ?? 0),
            'pacientes_atendidos' => (int) ($pacientes['total'] ?? 0),
            'vehiculos_operativos'=> (int) ($vehiculos['total'] ?? 0),
            'operaciones_activas' => (int) ($operacionesActivas['total'] ?? 0),
            'insumos_bajo_stock'  => (int) ($insumosBajoStock['total'] ?? 0),
        ];

        include 'src/views/modules/inicio.php';
    }
}
