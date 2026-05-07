<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
requireLogin();

$pageTitle = 'Dados Cartão Veloe';

$pdo->exec("
    CREATE TABLE IF NOT EXISTS veloe_records (
        id INT AUTO_INCREMENT PRIMARY KEY,
        transaction_datetime DATETIME NOT NULL,
        ec_name VARCHAR(150) NOT NULL,
        ec_city VARCHAR(100) NOT NULL,
        plate VARCHAR(10) NOT NULL,
        driver_name VARCHAR(100) NOT NULL,
        merchandise VARCHAR(150) NOT NULL,
        merchandise_quantity DECIMAL(10, 2) DEFAULT 0,
        merchandise_unit_value DECIMAL(10, 2) DEFAULT 0,
        transaction_value DECIMAL(10, 2) DEFAULT 0,
        previous_odometer INT DEFAULT 0,
        transaction_odometer INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

if (isset($_GET['template']) && $_GET['template'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=veloe_modelo.csv');
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
    fputcsv($output, [
        'Data/hora da transação',
        'Nome do EC',
        'Cidade do EC',
        'Placa do veículo',
        'Nome do motorista',
        'Mercadoria',
        'Quantidade de mercadoria',
        'Valor unitário da mercadoria (R$)',
        'Valor da transação (R$)',
        'Hodômetro anterior',
        'Hodômetro da transação',
    ], ';');
    fclose($output);
    exit;
}

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=veloe_' . date('Y-m-d') . '.csv');
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
    fputcsv($output, [
        'Data/hora da transação',
        'Nome do EC',
        'Cidade do EC',
        'Placa do veículo',
        'Nome do motorista',
        'Mercadoria',
        'Quantidade de mercadoria',
        'Valor unitário da mercadoria (R$)',
        'Valor da transação (R$)',
        'Hodômetro anterior',
        'Hodômetro da transação',
    ], ';');

    $stmt = $pdo->query("SELECT * FROM veloe_records ORDER BY transaction_datetime DESC, id DESC");
    while ($row = $stmt->fetch()) {
        fputcsv($output, [
            $row['transaction_datetime'],
            $row['ec_name'],
            $row['ec_city'],
            $row['plate'],
            $row['driver_name'],
            $row['merchandise'],
            $row['merchandise_quantity'],
            $row['merchandise_unit_value'],
            $row['transaction_value'],
            $row['previous_odometer'],
            $row['transaction_odometer'],
        ], ';');
    }
    fclose($output);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_veloe'])) {
    $stmt = $pdo->prepare("
        INSERT INTO veloe_records (
            transaction_datetime, ec_name, ec_city, plate, driver_name, merchandise,
            merchandise_quantity, merchandise_unit_value, transaction_value,
            previous_odometer, transaction_odometer
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $_POST['transaction_datetime'],
        $_POST['ec_name'],
        $_POST['ec_city'],
        strtoupper(trim($_POST['plate'])),
        $_POST['driver_name'],
        $_POST['merchandise'],
        (float) $_POST['merchandise_quantity'],
        (float) $_POST['merchandise_unit_value'],
        (float) $_POST['transaction_value'],
        (int) $_POST['previous_odometer'],
        (int) $_POST['transaction_odometer'],
    ]);

    header('Location: veloe.php?msg=success');
    exit;
}

$records = $pdo->query("SELECT * FROM veloe_records ORDER BY transaction_datetime DESC, id DESC")->fetchAll();

include '../includes/header.php';
?>

<?php include '../includes/cost_tabs.php'; ?>

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-end gap-3">
    <a href="veloe.php?template=csv" class="inline-flex items-center justify-center gap-2 bg-white border border-emerald-200 text-emerald-700 px-4 py-2 rounded-lg hover:bg-emerald-50 transition-colors">
        <i class="fa-solid fa-file-arrow-down"></i> Baixar modelo CSV
    </a>
    <a href="veloe.php?export=csv" class="inline-flex items-center justify-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors shadow-lg shadow-blue-100">
        <i class="fa-solid fa-file-export"></i> Exportar CSV
    </a>
</div>

<div class="grid grid-cols-1 xl:grid-cols-5 gap-8">
    <div class="xl:col-span-2">
        <div class="bg-white border rounded-2xl shadow-sm overflow-hidden sticky top-24">
            <div class="p-5 border-b bg-gray-50">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-credit-card text-blue-600"></i> Registrar transação Veloe
                </h3>
            </div>
            <form method="POST" class="p-6 space-y-4">
                <input type="hidden" name="add_veloe" value="1">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data/hora da transação</label>
                    <input type="datetime-local" name="transaction_datetime" required class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome do EC</label>
                        <input type="text" name="ec_name" required class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cidade do EC</label>
                        <input type="text" name="ec_city" required class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Placa do veículo</label>
                        <input type="text" name="plate" required class="w-full p-2.5 border rounded-lg uppercase focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome do motorista</label>
                        <input type="text" name="driver_name" required class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mercadoria</label>
                    <input type="text" name="merchandise" required class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade de mercadoria</label>
                        <input type="number" step="0.01" name="merchandise_quantity" required class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Valor unitário da mercadoria (R$)</label>
                        <input type="number" step="0.01" name="merchandise_unit_value" required class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Valor da transação (R$)</label>
                    <input type="number" step="0.01" name="transaction_value" required class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hodômetro anterior</label>
                        <input type="number" name="previous_odometer" required class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hodômetro da transação</label>
                        <input type="number" name="transaction_odometer" required class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
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
                <h3 class="font-bold text-gray-800">Histórico do cartão Veloe</h3>
                <span class="text-xs text-gray-500"><?php echo count($records); ?> registros</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Data/hora da transação</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Nome do EC</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Cidade do EC</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Placa do veículo</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Nome do motorista</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Mercadoria</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Quantidade de mercadoria</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Valor unitário da mercadoria (R$)</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Valor da transação (R$)</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Hodômetro anterior</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Hodômetro da transação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($records as $row): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-gray-600"><?php echo date('d/m/Y H:i', strtotime($row['transaction_datetime'])); ?></td>
                                <td class="px-4 py-3 font-medium text-gray-900"><?php echo htmlspecialchars($row['ec_name']); ?></td>
                                <td class="px-4 py-3 text-gray-600"><?php echo htmlspecialchars($row['ec_city']); ?></td>
                                <td class="px-4 py-3 text-gray-600"><?php echo htmlspecialchars($row['plate']); ?></td>
                                <td class="px-4 py-3 text-gray-600"><?php echo htmlspecialchars($row['driver_name']); ?></td>
                                <td class="px-4 py-3 text-gray-600"><?php echo htmlspecialchars($row['merchandise']); ?></td>
                                <td class="px-4 py-3 text-right text-gray-600"><?php echo number_format($row['merchandise_quantity'], 2, ',', '.'); ?></td>
                                <td class="px-4 py-3 text-right text-gray-600">R$ <?php echo number_format($row['merchandise_unit_value'], 2, ',', '.'); ?></td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">R$ <?php echo number_format($row['transaction_value'], 2, ',', '.'); ?></td>
                                <td class="px-4 py-3 text-right text-gray-600"><?php echo number_format($row['previous_odometer'], 0, ',', '.'); ?></td>
                                <td class="px-4 py-3 text-right text-gray-600"><?php echo number_format($row['transaction_odometer'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($records)): ?>
                            <tr>
                                <td colspan="11" class="px-4 py-10 text-center text-gray-500">Nenhum registro do cartão Veloe encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
