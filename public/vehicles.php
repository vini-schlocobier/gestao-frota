<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
requireLogin();

$pageTitle = 'Gestão de Veículos';

// Search and Filters
$search = $_GET['search'] ?? '';
$filter_unit = $_GET['unit'] ?? '';
$filter_model = $_GET['model'] ?? '';
$sort = $_GET['sort'] ?? 'created_at';
$order = $_GET['order'] ?? 'DESC';

$query = "SELECT * FROM vehicles WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (plate LIKE ? OR driver_name LIKE ? OR model LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($filter_unit) {
    $query .= " AND unit_code = ?";
    $params[] = $filter_unit;
}

if ($filter_model) {
    $query .= " AND model = ?";
    $params[] = $filter_model;
}

// Allowed sort columns
$allowedSort = ['year', 'ipva_cost', 'insurance_cost', 'odometer', 'created_at'];
if (!in_array($sort, $allowedSort))
    $sort = 'created_at';
$query .= " ORDER BY $sort $order";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$vehicles = $stmt->fetchAll();

// Get unique units and models for filters
$units = $pdo->query("SELECT DISTINCT unit_code FROM vehicles WHERE unit_code IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
$models = $pdo->query("SELECT DISTINCT model FROM vehicles WHERE model IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);

// Handle Delete
if (isset($_GET['delete']) && isAdmin()) {
    $stmt = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: vehicles.php?msg=deleted');
    exit;
}

include '../includes/header.php';
?>

<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex items-center gap-4 flex-1">
        <form action="" method="GET" class="flex-1 max-w-md relative">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                placeholder="Buscar por placa, motorista ou modelo..."
                class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
        </form>

        <button onclick="document.getElementById('filterPanel').classList.toggle('hidden')"
            class="px-4 py-2 border rounded-lg bg-white hover:bg-gray-50 flex items-center gap-2 text-gray-700">
            <i class="fa-solid fa-filter"></i> Filtros
        </button>

        <a href="export.php"
            class="px-4 py-2 border rounded-lg bg-white hover:bg-gray-50 flex items-center gap-2 text-gray-700">
            <i class="fa-solid fa-file-export"></i> Exportar
        </a>

        <a href="import.php?tab=vehicles&template=csv"
            class="px-4 py-2 border rounded-lg bg-white hover:bg-gray-50 flex items-center gap-2 text-gray-700">
            <i class="fa-solid fa-file-arrow-down"></i> Modelo CSV
        </a>
    </div>

    <a href="vehicle_form.php"
        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center gap-2 transition-colors">
        <i class="fa-solid fa-plus"></i> Novo Veículo
    </a>
</div>

<!-- Filter Panel -->
<div id="filterPanel" class="hidden mb-6 p-4 bg-white border rounded-xl shadow-sm">
    <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Estabelecimento</label>
            <select name="unit" class="w-full p-2 border rounded-lg outline-none">
                <option value="">Todos</option>
                <?php foreach ($units as $unit): ?>
                    <option value="<?php echo $unit; ?>" <?php echo $filter_unit == $unit ? 'selected' : ''; ?>>
                        <?php echo $unit; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Modelo</label>
            <select name="model" class="w-full p-2 border rounded-lg outline-none">
                <option value="">Todos</option>
                <?php foreach ($models as $m): ?>
                    <option value="<?php echo $m; ?>" <?php echo $filter_model == $m ? 'selected' : ''; ?>><?php echo $m; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Ordenar por</label>
            <select name="sort" class="w-full p-2 border rounded-lg outline-none">
                <option value="created_at" <?php echo $sort == 'created_at' ? 'selected' : ''; ?>>Data Cadastro</option>
                <option value="year" <?php echo $sort == 'year' ? 'selected' : ''; ?>>Ano</option>
                <option value="odometer" <?php echo $sort == 'odometer' ? 'selected' : ''; ?>>Hodômetro</option>
                <option value="ipva_cost" <?php echo $sort == 'ipva_cost' ? 'selected' : ''; ?>>Custo IPVA</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit"
                class="flex-1 bg-gray-800 text-white py-2 rounded-lg hover:bg-gray-900 transition-colors">Aplicar</button>
            <a href="vehicles.php" class="px-4 py-2 border rounded-lg hover:bg-gray-50 transition-colors">Limpar</a>
        </div>
    </form>
</div>

<div class="bg-white border rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Veículo</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Unidade</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Motorista</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Ano</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-right">Custo Total</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Hodômetro</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php if (empty($vehicles)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">Nenhum veículo encontrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($vehicles as $v): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($v['plate']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($v['model']); ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($v['unit_code']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?php if ($v['driver_name']): ?>
                                    <?php echo htmlspecialchars($v['driver_name']); ?>
                                <?php else: ?>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Sem
                                        motorista</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo $v['year']; ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right font-medium">
                                R$ <?php echo number_format($v['ipva_cost'] + $v['insurance_cost'], 2, ',', '.'); ?>
                                <?php if (($v['ipva_cost'] + $v['insurance_cost']) > 5000): ?>
                                    <i class="fa-solid fa-triangle-exclamation text-orange-500 ml-1" title="Custo Elevado"></i>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?php if ($v['odometer'] > 0): ?>
                                    <?php echo number_format($v['odometer'], 0, '', '.'); ?> km
                                <?php else: ?>
                                    <span class="text-red-500 font-medium italic">Não informado</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="vehicle_form.php?id=<?php echo $v['id']; ?>"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <?php if (isAdmin()): ?>
                                        <a href="?delete=<?php echo $v['id']; ?>"
                                            onclick="return confirm('Deseja realmente excluir este veículo?')"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Excluir">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
