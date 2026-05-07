<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
requireLogin();

$id = $_GET['id'] ?? null;
$vehicle = null;
$pageTitle = $id ? 'Editar Veículo' : 'Novo Veículo';

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);
    $vehicle = $stmt->fetch();
    if (!$vehicle) {
        header('Location: vehicles.php');
        exit;
    }
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'unit_code'      => $_POST['unit_code'],
        'plate'          => strtoupper($_POST['plate']),
        'internal_number'=> $_POST['internal_number'],
        'renavam'        => $_POST['renavam'],
        'model'          => $_POST['model'],
        'year'           => (int)$_POST['year'],
        'driver_name'    => $_POST['driver_name'],
        'driver_cpf'     => $_POST['driver_cpf'],
        'tank_capacity'  => (float)$_POST['tank_capacity'],
        'ipva_cost'      => (float)$_POST['ipva_cost'],
        'insurance_cost' => (float)$_POST['insurance_cost'],
        'odometer'       => (int)$_POST['odometer']
    ];

    try {
        if ($id) {
            $sql = "UPDATE vehicles SET 
                    unit_code = :unit_code, plate = :plate, internal_number = :internal_number, 
                    renavam = :renavam, model = :model, year = :year, 
                    driver_name = :driver_name, driver_cpf = :driver_cpf, 
                    tank_capacity = :tank_capacity, ipva_cost = :ipva_cost, 
                    insurance_cost = :insurance_cost, odometer = :odometer 
                    WHERE id = :id";
            $data['id'] = $id;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            $success = "Veículo atualizado com sucesso!";
        } else {
            $sql = "INSERT INTO vehicles (unit_code, plate, internal_number, renavam, model, year, driver_name, driver_cpf, tank_capacity, ipva_cost, insurance_cost, odometer) 
                    VALUES (:unit_code, :plate, :internal_number, :renavam, :model, :year, :driver_name, :driver_cpf, :tank_capacity, :ipva_cost, :insurance_cost, :odometer)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            header('Location: vehicles.php?msg=created');
            exit;
        }
        // Refresh data after update
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
            $stmt->execute([$id]);
            $vehicle = $stmt->fetch();
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = "Erro: A placa informada já está cadastrada.";
        } else {
            $error = "Erro ao salvar: " . $e->getMessage();
        }
    }
}

include '../includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <a href="vehicles.php" class="text-gray-500 hover:text-gray-800 flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Voltar para listagem
        </a>
    </div>

    <div class="bg-white border rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800"><?php echo $id ? 'Dados do Veículo: ' . $vehicle['plate'] : 'Preencha as informações do novo veículo'; ?></h3>
        </div>

        <?php if ($error): ?>
            <div class="m-6 p-4 bg-red-50 text-red-700 rounded-lg flex items-center gap-2 border border-red-100">
                <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="m-6 p-4 bg-green-50 text-green-700 rounded-lg flex items-center gap-2 border border-green-100">
                <i class="fa-solid fa-circle-check"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Seção 1: Identificação -->
                <div class="md:col-span-3">
                    <h4 class="text-sm font-bold text-blue-600 uppercase tracking-wider mb-4 border-b pb-2">Identificação do Veículo</h4>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Placa *</label>
                    <input type="text" name="plate" value="<?php echo htmlspecialchars($vehicle['plate'] ?? ''); ?>" required maxlength="10"
                        class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none uppercase">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cód. Unidade (Estabelecimento)</label>
                    <input type="text" name="unit_code" value="<?php echo htmlspecialchars($vehicle['unit_code'] ?? ''); ?>"
                        class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nº Interno</label>
                    <input type="text" name="internal_number" value="<?php echo htmlspecialchars($vehicle['internal_number'] ?? ''); ?>"
                        class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RENAVAM</label>
                    <input type="text" name="renavam" value="<?php echo htmlspecialchars($vehicle['renavam'] ?? ''); ?>"
                        class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Modelo</label>
                    <input type="text" name="model" value="<?php echo htmlspecialchars($vehicle['model'] ?? ''); ?>"
                        class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ano</label>
                    <input type="number" name="year" value="<?php echo htmlspecialchars($vehicle['year'] ?? date('Y')); ?>"
                        class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <!-- Seção 2: Operacional -->
                <div class="md:col-span-3 pt-4">
                    <h4 class="text-sm font-bold text-blue-600 uppercase tracking-wider mb-4 border-b pb-2">Operacional e Motorista</h4>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motorista Responsável</label>
                    <input type="text" name="driver_name" value="<?php echo htmlspecialchars($vehicle['driver_name'] ?? ''); ?>"
                        class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CPF do Motorista</label>
                    <input type="text" name="driver_cpf" value="<?php echo htmlspecialchars($vehicle['driver_cpf'] ?? ''); ?>"
                        class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="000.000.000-00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hodômetro Atual (KM)</label>
                    <input type="number" name="odometer" value="<?php echo htmlspecialchars($vehicle['odometer'] ?? 0); ?>"
                        class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <!-- Seção 3: Custos e Técnica -->
                <div class="md:col-span-3 pt-4">
                    <h4 class="text-sm font-bold text-blue-600 uppercase tracking-wider mb-4 border-b pb-2">Custos e Capacidade</h4>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Capacidade Tanque (L)</label>
                    <input type="number" step="0.01" name="tank_capacity" value="<?php echo htmlspecialchars($vehicle['tank_capacity'] ?? ''); ?>"
                        class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Custo IPVA / Licenciamento</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">R$</span>
                        <input type="number" step="0.01" name="ipva_cost" value="<?php echo htmlspecialchars($vehicle['ipva_cost'] ?? 0); ?>"
                            class="w-full pl-10 pr-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Custo Seguro</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">R$</span>
                        <input type="number" step="0.01" name="insurance_cost" value="<?php echo htmlspecialchars($vehicle['insurance_cost'] ?? 0); ?>"
                            class="w-full pl-10 pr-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t flex justify-end gap-4">
                <a href="vehicles.php" class="px-6 py-2.5 border rounded-lg hover:bg-gray-50 transition-colors text-gray-700 font-medium">Cancelar</a>
                <button type="submit" class="px-10 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-lg shadow-blue-200 transition-all">
                    <?php echo $id ? 'Salvar Alterações' : 'Cadastrar Veículo'; ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
