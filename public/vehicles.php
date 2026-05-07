<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
requireLogin();

$pageTitle = 'Gestão de Veículos';
$pageScripts = ['assets/js/vehicles.js'];

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

$allowedSort = ['year', 'ipva_cost', 'insurance_cost', 'odometer', 'created_at'];
if (!in_array($sort, $allowedSort)) {
    $sort = 'created_at';
}
$query .= " ORDER BY $sort $order";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$vehicles = $stmt->fetchAll();

$units = $pdo->query("SELECT DISTINCT unit_code FROM vehicles WHERE unit_code IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
$models = $pdo->query("SELECT DISTINCT model FROM vehicles WHERE model IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);

if (isset($_GET['delete']) && isAdmin()) {
    $stmt = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: vehicles.php?msg=deleted');
    exit;
}

include '../includes/header.php';
?>

<div class="mb-6 flex flex-col justify-between gap-4 xl:flex-row xl:items-center">
    <div class="flex flex-col gap-3 xl:flex-1 xl:flex-row xl:items-center xl:gap-4">
        <form action="" method="GET" class="relative w-full xl:max-w-md">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                placeholder="Buscar por placa, motorista ou modelo..."
                class="w-full rounded-lg border py-2 pl-10 pr-4 outline-none focus:ring-2 focus:ring-blue-500">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
        </form>

        <button type="button" data-toggle-filter-panel
            class="flex items-center justify-center gap-2 rounded-lg border bg-white px-4 py-2 text-gray-700 hover:bg-gray-50">
            <i class="fa-solid fa-filter"></i> Filtros
        </button>

        <a href="export.php"
            class="flex items-center justify-center gap-2 rounded-lg border bg-white px-4 py-2 text-gray-700 hover:bg-gray-50">
            <i class="fa-solid fa-file-export"></i> Exportar
        </a>

        <a href="import.php?tab=vehicles&template=csv"
            class="flex items-center justify-center gap-2 rounded-lg border bg-white px-4 py-2 text-gray-700 hover:bg-gray-50">
            <i class="fa-solid fa-file-arrow-down"></i> Modelo CSV
        </a>
    </div>

    <a href="vehicle_form.php"
        class="flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-6 py-2 text-white transition-colors hover:bg-blue-700">
        <i class="fa-solid fa-plus"></i> Novo Veículo
    </a>
</div>

<div id="filterPanel" class="hidden mb-6 rounded-xl border bg-white p-4 shadow-sm">
    <form action="" method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Estabelecimento</label>
            <select name="unit" class="w-full rounded-lg border p-2 outline-none">
                <option value="">Todos</option>
                <?php foreach ($units as $unit): ?>
                    <option value="<?php echo $unit; ?>" <?php echo $filter_unit == $unit ? 'selected' : ''; ?>>
                        <?php echo $unit; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Modelo</label>
            <select name="model" class="w-full rounded-lg border p-2 outline-none">
                <option value="">Todos</option>
                <?php foreach ($models as $m): ?>
                    <option value="<?php echo $m; ?>" <?php echo $filter_model == $m ? 'selected' : ''; ?>><?php echo $m; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Ordenar por</label>
            <select name="sort" class="w-full rounded-lg border p-2 outline-none">
                <option value="created_at" <?php echo $sort == 'created_at' ? 'selected' : ''; ?>>Data Cadastro</option>
                <option value="year" <?php echo $sort == 'year' ? 'selected' : ''; ?>>Ano</option>
                <option value="odometer" <?php echo $sort == 'odometer' ? 'selected' : ''; ?>>HodÃ´metro</option>
                <option value="ipva_cost" <?php echo $sort == 'ipva_cost' ? 'selected' : ''; ?>>Custo IPVA</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit"
                class="flex-1 rounded-lg bg-gray-800 py-2 text-white transition-colors hover:bg-gray-900">Aplicar</button>
            <a href="vehicles.php" class="rounded-lg border px-4 py-2 transition-colors hover:bg-gray-50">Limpar</a>
        </div>
    </form>
</div>

<div class="overflow-hidden rounded-xl border bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="mobile-table-cards w-full text-left">
            <thead class="border-b bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">VeÃ­culo</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">Unidade</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">Motorista</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">Ano</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase text-gray-500">Custo Total</th>
                    <th class="px-6 py-4 text-xs font-semibold uppercase text-gray-500">HodÃ´metro</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold uppercase text-gray-500">AÃ§Ãµes</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php if (empty($vehicles)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">Nenhum veÃ­culo encontrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($vehicles as $v): ?>
                        <tr class="transition-colors hover:bg-gray-50">
                            <td class="px-6 py-4" data-label="Veiculo">
                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($v['plate']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($v['model']); ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600" data-label="Unidade">
                                <?php echo htmlspecialchars($v['unit_code']); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600" data-label="Motorista">
                                <?php if ($v['driver_name']): ?>
                                    <?php echo htmlspecialchars($v['driver_name']); ?>
                                <?php else: ?>
                                    <span
                                        class="inline-flex items-center rounded bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">Sem
                                        motorista</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600" data-label="Ano"><?php echo $v['year']; ?></td>
                            <td class="px-6 py-4 text-right text-sm font-medium text-gray-900" data-label="Custo total">
                                R$ <?php echo number_format($v['ipva_cost'] + $v['insurance_cost'], 2, ',', '.'); ?>
                                <?php if (($v['ipva_cost'] + $v['insurance_cost']) > 5000): ?>
                                    <i class="fa-solid fa-triangle-exclamation ml-1 text-orange-500" title="Custo Elevado"></i>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600" data-label="Hodometro">
                                <?php if ($v['odometer'] > 0): ?>
                                    <?php echo number_format($v['odometer'], 0, '', '.'); ?> km
                                <?php else: ?>
                                    <span class="font-medium italic text-red-500">NÃ£o informado</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center" data-label="Acoes">
                                <div class="flex justify-start gap-2 lg:justify-center">
                                    <a href="vehicle_form.php?id=<?php echo $v['id']; ?>"
                                        class="rounded-lg p-2 text-blue-600 transition-colors hover:bg-blue-50" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <?php if (isAdmin()): ?>
                                        <a href="?delete=<?php echo $v['id']; ?>"
                                            data-confirm-delete="Deseja realmente excluir este veÃ­culo?"
                                            class="rounded-lg p-2 text-red-600 transition-colors hover:bg-red-50" title="Excluir">
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