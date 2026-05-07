<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
requireLogin();

$filename = "frota_export_" . date('Y-m-d') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');
// UTF-8 BOM for Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Header
fputcsv($output, [
    'Estabelecimento', 'Placa', 'Nº Interno', 'RENAVAM', 'Modelo', 'Ano', 
    'Motorista', 'CPF Motorista', 'Capacidade Tanque', 'Custo IPVA', 
    'Custo Seguro', 'Hodômetro'
], ';');

$query = $pdo->query("SELECT * FROM vehicles ORDER BY plate ASC");
while ($row = $query->fetch()) {
    fputcsv($output, [
        $row['unit_code'],
        $row['plate'],
        $row['internal_number'],
        $row['renavam'],
        $row['model'],
        $row['year'],
        $row['driver_name'],
        $row['driver_cpf'],
        $row['tank_capacity'],
        $row['ipva_cost'],
        $row['insurance_cost'],
        $row['odometer']
    ], ';');
}

fclose($output);
exit;
?>
