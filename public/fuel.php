<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

requireLogin();

$pageTitle = 'Controle de Abastecimento';

// Handle fuel registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_fuel'])) {
    $vehicle_id = $_POST['vehicle_id'];
    $date = $_POST['date'];
    $liters = (float) $_POST['liters'];
    $cost = (float) $_POST['cost'];
    $odometer = (int) $_POST['odometer'];

    // Calculate KM/L (Optional advanced)
    // Get last odometer for this vehicle
    $stmt = $pdo->prepare("SELECT odometer FROM fuel_records WHERE vehicle_id = ? ORDER BY date DESC, odometer DESC LIMIT 1");
    $stmt->execute([$vehicle_id]);
    $lastOdo = $stmt->fetchColumn();

    $km_per_liter = null;
    if ($lastOdo && $odometer > $lastOdo) {
        $km_per_liter = ($odometer - $lastOdo) / $liters;
    }

    $stmt = $pdo->prepare("INSERT INTO fuel_records (vehicle_id, date, liters, cost, odometer, km_per_liter) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$vehicle_id, $date, $liters, $cost, $odometer, $km_per_liter]);

    // Update vehicle odometer
    $stmt = $pdo->prepare("UPDATE vehicles SET odometer = ? WHERE id = ?");
    $stmt->execute([$odometer, $vehicle_id]);

    header('Location: fuel.php?msg=success');
    exit;
}

// Get records
$records = $pdo->query("SELECT f.*, v.plate, v.model FROM fuel_records f JOIN vehicles v ON f.vehicle_id = v.id ORDER BY f.date DESC")->fetchAll();
$vehicles = $pdo->query("SELECT id, plate, model FROM vehicles ORDER BY plate ASC")->fetchAll();

include '../includes/header.php';
include '../includes/cost_tabs.php';
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Form Section -->
    <div class="lg:col-span-1">
        <div class="bg-white border rounded-xl shadow-sm overflow-hidden sticky top-24">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-blue-600"></i> Registrar Abastecimento
                </h3>
            </div>
            <form method="POST" class="p-6 space-y-4">
                <input type="hidden" name="add_fuel" value="1">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Veículo</label>
                    <select name="vehicle_id" required
                        class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Selecione o veículo</option>
                        <?php foreach ($vehicles as $v): ?>
                            <option value="<?php echo $v['id']; ?>"><?php echo $v['plate']; ?> - <?php echo $v['model']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                    <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required
                        class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Litros</label>
                        <input type="number" step="0.01" name="liters" required
                            class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Custo Total (R$)</label>
                        <input type="number" step="0.01" name="cost" required
                            class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hodômetro Atual (KM)</label>
                    <input type="number" name="odometer" required
                        class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition-colors shadow-lg shadow-blue-100">
                    Registrar
                </button>
            </form>
        </div>
    </div>

    <!-- History Section -->
    <div class="lg:col-span-2">
        <div class="bg-white border rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="font-bold text-gray-800">Histórico Recente</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Data</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Veículo</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Litros</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Custo</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-right">KM/L</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($records as $r): ?>
                            <tr class="hover:bg-gray-50 transition-colors text-sm">
                                <td class="px-4 py-3 text-gray-600"><?php echo date('d/m/Y', strtotime($r['date'])); ?></td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900"><?php echo $r['plate']; ?></div>
                                    <div class="text-xs text-gray-500"><?php echo $r['model']; ?></div>
                                </td>
                                <td class="px-4 py-3 text-right text-gray-600">
                                    <?php echo number_format($r['liters'], 2, ',', '.'); ?> L
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900">R$
                                    <?php echo number_format($r['cost'], 2, ',', '.'); ?>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <?php if ($r['km_per_liter']): ?>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            <?php echo number_format($r['km_per_liter'], 2, ',', '.'); ?> km/l
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($records)): ?>
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-gray-500">Nenhum abastecimento
                                    registrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>