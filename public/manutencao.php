<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
requireLogin();

$pageTitle = 'Manutenção';

$pdo->exec("
    CREATE TABLE IF NOT EXISTS manutencao_records (
        id INT AUTO_INCREMENT PRIMARY KEY,
        driver_name VARCHAR(100) NOT NULL,
        plate VARCHAR(10) NOT NULL,
        maintenance_type VARCHAR(150) NOT NULL,
        maintenance_value DECIMAL(10, 2) DEFAULT 0,
        maintenance_date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

if (isset($_GET['template']) && $_GET['template'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=manutencao_modelo.csv');
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
    fputcsv($output, [
        'MOTORISTA',
        'PLACA',
        'TIPO DE MANUTENÇÃO',
        'VALOR',
        'DATA',
    ], ';');
    fclose($output);
    exit;
}

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=manutencao_' . date('Y-m-d') . '.csv');
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
    fputcsv($output, [
        'MOTORISTA',
        'PLACA',
        'TIPO DE MANUTENÇÃO',
        'VALOR',
        'DATA',
    ], ';');

    $stmt = $pdo->query("SELECT * FROM manutencao_records ORDER BY maintenance_date DESC, id DESC");
    while ($row = $stmt->fetch()) {
        fputcsv($output, [
            $row['driver_name'],
            $row['plate'],
            $row['maintenance_type'],
            $row['maintenance_value'],
            $row['maintenance_date'],
        ], ';');
    }
    fclose($output);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_manutencao'])) {
    $stmt = $pdo->prepare("
        INSERT INTO manutencao_records (
            driver_name, plate, maintenance_type, maintenance_value, maintenance_date
        ) VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $_POST['driver_name'],
        strtoupper(trim($_POST['plate'])),
        $_POST['maintenance_type'],
        (float) $_POST['maintenance_value'],
        $_POST['maintenance_date'],
    ]);

    header('Location: manutencao.php?msg=success');
    exit;
}

$records = $pdo->query("SELECT * FROM manutencao_records ORDER BY maintenance_date DESC, id DESC")->fetchAll();

include '../includes/header.php';
?>

<?php include '../includes/cost_tabs.php'; ?>

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-end gap-3">
    <a href="manutencao.php?template=csv" class="inline-flex items-center justify-center gap-2 bg-white border border-emerald-200 text-emerald-700 px-4 py-2 rounded-lg hover:bg-emerald-50 transition-colors">
        <i class="fa-solid fa-file-arrow-down"></i> Baixar modelo CSV
    </a>
    <a href="manutencao.php?export=csv" class="inline-flex items-center justify-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors shadow-lg shadow-blue-100">
        <i class="fa-solid fa-file-export"></i> Exportar CSV
    </a>
</div>

<div class="grid grid-cols-1 xl:grid-cols-5 gap-8">
    <div class="xl:col-span-2">
        <div class="bg-white border rounded-2xl shadow-sm overflow-hidden sticky top-24">
            <div class="p-5 border-b bg-gray-50">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-screwdriver-wrench text-blue-600"></i> Registrar manutenção
                </h3>
            </div>
            <form method="POST" class="p-6 space-y-4">
                <input type="hidden" name="add_manutencao" value="1">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motorista</label>
                    <input type="text" name="driver_name" required class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Placa</label>
                    <input type="text" name="plate" required class="w-full p-2.5 border rounded-lg uppercase focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de manutenção</label>
                    <input type="text" name="maintenance_type" required class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Valor</label>
                    <input type="number" step="0.01" name="maintenance_value" required class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                    <input type="date" name="maintenance_date" value="<?php echo date('Y-m-d'); ?>" required class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition-colors shadow-lg shadow-blue-100">
                    Salvar registro
                </button>
            </form>
        </div>
    </div>

    <div class="xl:col-span-3">
        <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b bg-gray-50 flex items-center justify-between">
                <h3 class="font-bold text-gray-800">Histórico de manutenções</h3>
                <span class="text-xs text-gray-500"><?php echo count($records); ?> registros</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Motorista</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Placa</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Tipo de manutenção</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Valor</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($records as $row): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-gray-600"><?php echo htmlspecialchars($row['driver_name']); ?></td>
                                <td class="px-4 py-3 font-medium text-gray-900"><?php echo htmlspecialchars($row['plate']); ?></td>
                                <td class="px-4 py-3 text-gray-600"><?php echo htmlspecialchars($row['maintenance_type']); ?></td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">R$ <?php echo number_format($row['maintenance_value'], 2, ',', '.'); ?></td>
                                <td class="px-4 py-3 text-gray-600"><?php echo date('d/m/Y', strtotime($row['maintenance_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($records)): ?>
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-gray-500">Nenhum registro de manutenção encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
