<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
requireLogin();

$pageTitle = 'Dashboard';

// Statistics
$totalVehicles = $pdo->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();
$totalCosts = $pdo->query("SELECT SUM(ipva_cost + insurance_cost) FROM vehicles")->fetchColumn();
$avgTank = $pdo->query("SELECT AVG(tank_capacity) FROM vehicles")->fetchColumn();

// Alerts
$noDriver = $pdo->query("SELECT COUNT(*) FROM vehicles WHERE driver_name IS NULL OR driver_name = ''")->fetchColumn();
$noOdometer = $pdo->query("SELECT COUNT(*) FROM vehicles WHERE odometer = 0 OR odometer IS NULL")->fetchColumn();

// Data for Chart (Costs per Model)
$costsPerModel = $pdo->query("SELECT model, SUM(ipva_cost + insurance_cost) as total FROM vehicles GROUP BY model LIMIT 5")->fetchAll();
$chartLabels = json_encode(array_column($costsPerModel, 'model'));
$chartData = json_encode(array_column($costsPerModel, 'total'));

include '../includes/header.php';
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Stat Card 1 -->
    <div class="bg-white p-6 rounded-xl border shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-car text-xl"></i>
            </div>
            <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded">Ativa</span>
        </div>
        <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Total de Veículos</h3>
        <p class="text-3xl font-bold text-gray-800"><?php echo $totalVehicles; ?></p>
    </div>

    <!-- Stat Card 2 -->
    <div class="bg-white p-6 rounded-xl border shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-dollar-sign text-xl"></i>
            </div>
            <span class="text-xs font-bold text-gray-500 bg-gray-50 px-2 py-1 rounded">Anual</span>
        </div>
        <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Custo Total (IPVA + Seguro)</h3>
        <p class="text-3xl font-bold text-gray-800">R$ <?php echo number_format($totalCosts, 2, ',', '.'); ?></p>
    </div>

    <!-- Stat Card 3 -->
    <div class="bg-white p-6 rounded-xl border shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-gas-pump text-xl"></i>
            </div>
        </div>
        <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Média Tanque</h3>
        <p class="text-3xl font-bold text-gray-800"><?php echo number_format($avgTank, 1, ',', '.'); ?> L</p>
    </div>

    <!-- Stat Card 4 -->
    <div class="bg-white p-6 rounded-xl border shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </div>
            <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 rounded">Pendências</span>
        </div>
        <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Alertas Ativos</h3>
        <p class="text-3xl font-bold text-gray-800"><?php echo $noDriver + $noOdometer; ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Chart Section -->
    <div class="lg:col-span-2 bg-white p-6 rounded-xl border shadow-sm">
        <h3 class="text-lg font-bold text-gray-800 mb-6">Distribuição de Custos por Modelo</h3>
        <canvas id="costsChart" height="150"></canvas>
    </div>

    <!-- Alerts Section -->
    <div class="bg-white p-6 rounded-xl border shadow-sm">
        <h3 class="text-lg font-bold text-gray-800 mb-6">Alertas Críticos</h3>
        <div class="space-y-4">
            <?php if ($noDriver > 0): ?>
                <div class="flex items-start gap-3 p-3 bg-red-50 border border-red-100 rounded-lg">
                    <i class="fa-solid fa-user-slash text-red-500 mt-1"></i>
                    <div>
                        <p class="text-sm font-bold text-red-800"><?php echo $noDriver; ?> Veículos sem motorista</p>
                        <p class="text-xs text-red-600">É necessário atribuir um responsável.</p>
                        <a href="vehicles.php?search=sem+motorista" class="text-xs font-bold text-red-700 underline mt-1 block">Ver veículos</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($noOdometer > 0): ?>
                <div class="flex items-start gap-3 p-3 bg-orange-50 border border-orange-100 rounded-lg">
                    <i class="fa-solid fa-gauge-high text-orange-500 mt-1"></i>
                    <div>
                        <p class="text-sm font-bold text-orange-800"><?php echo $noOdometer; ?> Hodômetros zerados</p>
                        <p class="text-xs text-orange-600">Veículos precisam de atualização de KM.</p>
                        <a href="vehicles.php" class="text-xs font-bold text-orange-700 underline mt-1 block">Atualizar agora</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($noDriver == 0 && $noOdometer == 0): ?>
                <div class="text-center py-10">
                    <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-check text-2xl"></i>
                    </div>
                    <p class="text-gray-500">Tudo em dia!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('costsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo $chartLabels; ?>,
            datasets: [{
                label: 'Custo Total (R$)',
                data: <?php echo $chartData; ?>,
                backgroundColor: 'rgba(37, 99, 235, 0.6)',
                borderColor: 'rgb(37, 99, 235)',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>

<?php include '../includes/footer.php'; ?>
