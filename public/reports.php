<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
requireLogin();

$pageTitle = 'Relatório Geral de Custos';

// Get costs grouped by vehicle
$stmt = $pdo->query("
    SELECT 
        v.plate, v.model, v.unit_code,
        v.ipva_cost, v.insurance_cost,
        (v.ipva_cost + v.insurance_cost) as total_fixed_cost,
        IFNULL(SUM(f.cost), 0) as total_fuel_cost,
        (v.ipva_cost + v.insurance_cost + IFNULL(SUM(f.cost), 0)) as grand_total
    FROM vehicles v
    LEFT JOIN fuel_records f ON v.id = f.vehicle_id
    GROUP BY v.id
    ORDER BY grand_total DESC
");
$reportData = $stmt->fetchAll();

$totalFixed = 0;
$totalFuel = 0;
foreach ($reportData as $row) {
    $totalFixed += $row['total_fixed_cost'];
    $totalFuel += $row['total_fuel_cost'];
}

include '../includes/header.php';
?>

<?php include '../includes/cost_tabs.php'; ?>

<div class="mb-6 flex justify-between items-center">
    <div class="flex gap-4">
        <div class="bg-white px-6 py-4 rounded-xl border shadow-sm">
            <p class="text-xs font-bold text-gray-500 uppercase">Total Custos Fixos</p>
            <p class="text-xl font-bold text-gray-800">R$ <?php echo number_format($totalFixed, 2, ',', '.'); ?></p>
        </div>
        <div class="bg-white px-6 py-4 rounded-xl border shadow-sm">
            <p class="text-xs font-bold text-gray-500 uppercase">Total Combustível</p>
            <p class="text-xl font-bold text-gray-800">R$ <?php echo number_format($totalFuel, 2, ',', '.'); ?></p>
        </div>
        <div class="bg-blue-600 px-6 py-4 rounded-xl border border-blue-700 shadow-sm text-white">
            <p class="text-xs font-bold text-blue-100 uppercase">Custo Geral</p>
            <p class="text-xl font-bold">R$ <?php echo number_format($totalFixed + $totalFuel, 2, ',', '.'); ?></p>
        </div>
    </div>
    
    <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-gray-900 transition-colors">
        <i class="fa-solid fa-print"></i> Imprimir / PDF
    </button>
</div>

<div class="bg-white border rounded-xl shadow-sm overflow-hidden print:border-0 print:shadow-none">
    <div class="p-6 border-b bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Detalhamento por Veículo</h3>
        <span class="text-xs text-gray-500">Gerado em: <?php echo date('d/m/Y H:i'); ?></span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Veículo / Unidade</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-right">IPVA + Seguro</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-right">Combustível</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-right">Custo Total</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-center">Impacto</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php foreach ($reportData as $row): 
                    $impact = ($totalFixed + $totalFuel > 0) ? ($row['grand_total'] / ($totalFixed + $totalFuel)) * 100 : 0;
                ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900"><?php echo $row['plate']; ?></div>
                            <div class="text-xs text-gray-500"><?php echo $row['model']; ?> | <?php echo $row['unit_code']; ?></div>
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-600">R$ <?php echo number_format($row['total_fixed_cost'], 2, ',', '.'); ?></td>
                        <td class="px-6 py-4 text-right text-sm text-gray-600">R$ <?php echo number_format($row['total_fuel_cost'], 2, ',', '.'); ?></td>
                        <td class="px-6 py-4 text-right font-bold text-gray-900">R$ <?php echo number_format($row['grand_total'], 2, ',', '.'); ?></td>
                        <td class="px-6 py-4">
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo min(100, $impact); ?>%"></div>
                            </div>
                            <p class="text-[10px] text-center mt-1 text-gray-500"><?php echo number_format($impact, 1); ?>% do total</p>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
@media print {
    aside, header, button { display: none !important; }
    main { padding: 0 !important; }
    .print\:border-0 { border: 0 !important; }
    .print\:shadow-none { shadow: none !important; }
}
</style>

<?php include '../includes/footer.php'; ?>
