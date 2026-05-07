<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
requireLogin();

$pageTitle = 'Importar Dados';
$error = '';
$success = '';

$importTabs = [
    'vehicles' => [
        'label' => 'Veículos',
        'icon' => 'fa-car',
        'title' => 'Importação de Veículos (CSV)',
        'description' => 'Importe ou atualize veículos em massa.',
        'columns' => ['Unidade', 'Placa', 'Nº Interno', 'RENAVAM', 'Modelo', 'Ano', 'Motorista', 'CPF', 'Cap. Tanque', 'IPVA', 'Seguro', 'Hodômetro'],
    ],
    'lava_rapido' => [
        'label' => 'Lava Rápido',
        'icon' => 'fa-soap',
        'title' => 'Importação de Lava Rápido (CSV)',
        'description' => 'Importe movimentações de lava rápido em lote.',
        'columns' => ['Data/hora da transação', 'Nome do EC', 'Cidade do EC', 'Placa do veículo', 'Nome do motorista', 'Mercadoria', 'Quantidade de mercadoria', 'Valor unitário da mercadoria (R$)', 'Valor da transação (R$)', 'Hodômetro anterior', 'Hodômetro da transação'],
    ],
    'veloe' => [
        'label' => 'Cartão Veloe',
        'icon' => 'fa-credit-card',
        'title' => 'Importação de Dados Cartão Veloe (CSV)',
        'description' => 'Importe transações do cartão Veloe em lote.',
        'columns' => ['Data/hora da transação', 'Nome do EC', 'Cidade do EC', 'Placa do veículo', 'Nome do motorista', 'Mercadoria', 'Quantidade de mercadoria', 'Valor unitário da mercadoria (R$)', 'Valor da transação (R$)', 'Hodômetro anterior', 'Hodômetro da transação'],
    ],
    'manutencao' => [
        'label' => 'Manutenção',
        'icon' => 'fa-screwdriver-wrench',
        'title' => 'Importação de Manutenção (CSV)',
        'description' => 'Importe registros de manutenção em lote.',
        'columns' => ['MOTORISTA', 'PLACA', 'TIPO DE MANUTENÇÃO', 'VALOR', 'DATA'],
    ],
];

$activeTab = $_GET['tab'] ?? 'vehicles';
if (!isset($importTabs[$activeTab])) {
    $activeTab = 'vehicles';
}

$pdo->exec("
    CREATE TABLE IF NOT EXISTS lava_rapido_records (
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
    $tab = $_GET['tab'] ?? 'vehicles';
    if (!isset($importTabs[$tab])) {
        $tab = 'vehicles';
    }

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $tab . '_modelo.csv');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
    fputcsv($output, $importTabs[$tab]['columns'], ';');
    fclose($output);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $tab = $_POST['tab'] ?? 'vehicles';
    if (!isset($importTabs[$tab])) {
        $tab = 'vehicles';
    }
    $activeTab = $tab;

    $file = $_FILES['file']['tmp_name'];
    $handle = fopen($file, 'r');

    if ($handle) {
        fgetcsv($handle, 0, ';');
        $imported = 0;

        try {
            $pdo->beginTransaction();

            if ($tab === 'vehicles') {
                $stmt = $pdo->prepare("
                    INSERT INTO vehicles (
                        unit_code, plate, internal_number, renavam, model, year,
                        driver_name, driver_cpf, tank_capacity, ipva_cost, insurance_cost, odometer
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        unit_code = VALUES(unit_code),
                        internal_number = VALUES(internal_number),
                        model = VALUES(model),
                        year = VALUES(year),
                        driver_name = VALUES(driver_name),
                        driver_cpf = VALUES(driver_cpf),
                        tank_capacity = VALUES(tank_capacity),
                        ipva_cost = VALUES(ipva_cost),
                        insurance_cost = VALUES(insurance_cost),
                        odometer = VALUES(odometer)
                ");

                while (($data = fgetcsv($handle, 0, ';')) !== false) {
                    if (count($data) >= 12) {
                        $data[1] = strtoupper(trim($data[1]));
                        $stmt->execute($data);
                        $imported++;
                    }
                }
            }

            if ($tab === 'lava_rapido') {
                $stmt = $pdo->prepare("
                    INSERT INTO lava_rapido_records (
                        transaction_datetime, ec_name, ec_city, plate, driver_name, merchandise,
                        merchandise_quantity, merchandise_unit_value, transaction_value,
                        previous_odometer, transaction_odometer
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");

                while (($data = fgetcsv($handle, 0, ';')) !== false) {
                    if (count($data) >= 11) {
                        $stmt->execute([
                            $data[0],
                            $data[1],
                            $data[2],
                            strtoupper(trim($data[3])),
                            $data[4],
                            $data[5],
                            (float) str_replace(',', '.', $data[6]),
                            (float) str_replace(',', '.', $data[7]),
                            (float) str_replace(',', '.', $data[8]),
                            (int) $data[9],
                            (int) $data[10],
                        ]);
                        $imported++;
                    }
                }
            }

            if ($tab === 'veloe') {
                $stmt = $pdo->prepare("
                    INSERT INTO veloe_records (
                        transaction_datetime, ec_name, ec_city, plate, driver_name, merchandise,
                        merchandise_quantity, merchandise_unit_value, transaction_value,
                        previous_odometer, transaction_odometer
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");

                while (($data = fgetcsv($handle, 0, ';')) !== false) {
                    if (count($data) >= 11) {
                        $stmt->execute([
                            $data[0],
                            $data[1],
                            $data[2],
                            strtoupper(trim($data[3])),
                            $data[4],
                            $data[5],
                            (float) str_replace(',', '.', $data[6]),
                            (float) str_replace(',', '.', $data[7]),
                            (float) str_replace(',', '.', $data[8]),
                            (int) $data[9],
                            (int) $data[10],
                        ]);
                        $imported++;
                    }
                }
            }

            if ($tab === 'manutencao') {
                $stmt = $pdo->prepare("
                    INSERT INTO manutencao_records (
                        driver_name, plate, maintenance_type, maintenance_value, maintenance_date
                    ) VALUES (?, ?, ?, ?, ?)
                ");

                while (($data = fgetcsv($handle, 0, ';')) !== false) {
                    if (count($data) >= 5) {
                        $stmt->execute([
                            $data[0],
                            strtoupper(trim($data[1])),
                            $data[2],
                            (float) str_replace(',', '.', $data[3]),
                            $data[4],
                        ]);
                        $imported++;
                    }
                }
            }

            $pdo->commit();
            $success = $imported . ' registros importados com sucesso em ' . $importTabs[$tab]['label'] . '!';
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Erro na importação: ' . $e->getMessage();
        }

        fclose($handle);
    } else {
        $error = 'Não foi possível abrir o arquivo enviado.';
    }
}

include '../includes/header.php';
?>

<div class="max-w-6xl mx-auto space-y-6">
    <div class="overflow-x-auto">
        <div class="inline-flex min-w-full gap-2 rounded-2xl bg-white/90 p-2 shadow-sm ring-1 ring-emerald-100">
            <?php foreach ($importTabs as $tabKey => $tab): ?>
                <?php $isActive = $activeTab === $tabKey; ?>
                <a
                    href="import.php?tab=<?php echo $tabKey; ?>"
                    class="flex items-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold transition-colors whitespace-nowrap <?php echo $isActive ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-100' : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-700'; ?>"
                >
                    <i class="fa-solid <?php echo $tab['icon']; ?>"></i>
                    <?php echo $tab['label']; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="bg-white border rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b bg-gray-50">
            <h3 class="font-bold text-gray-800"><?php echo $importTabs[$activeTab]['title']; ?></h3>
            <p class="text-sm text-gray-500 mt-1"><?php echo $importTabs[$activeTab]['description']; ?></p>
        </div>

        <div class="p-8">
            <?php if ($error): ?>
                <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-lg flex items-center gap-2 border border-red-100">
                    <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-lg flex items-center gap-2 border border-green-100">
                    <i class="fa-solid fa-circle-check"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <div class="mb-8 p-4 bg-blue-50 border border-blue-100 rounded-lg">
                <h4 class="text-blue-800 font-bold text-sm mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info"></i> Instruções
                </h4>
                <ul class="text-sm text-blue-700 space-y-1 list-disc ml-5">
                    <li>O arquivo deve estar no formato <strong>CSV</strong>, separado por ponto e vírgula `;`.</li>
                    <li>A primeira linha deve conter o cabeçalho.</li>
                    <li>As colunas devem seguir exatamente esta ordem: <?php echo implode('; ', $importTabs[$activeTab]['columns']); ?>.</li>
                </ul>
                <a href="import.php?tab=<?php echo $activeTab; ?>&template=csv" class="inline-block mt-4 text-sm font-bold text-blue-600 hover:underline">
                    <i class="fa-solid fa-download"></i> Baixar Planilha Modelo
                </a>
            </div>

            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="tab" value="<?php echo $activeTab; ?>">
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-10 text-center hover:border-blue-500 transition-colors group cursor-pointer" onclick="document.getElementById('fileInput').click()">
                    <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-400 group-hover:text-blue-500 mb-4 transition-colors"></i>
                    <p class="text-gray-600 font-medium">Clique para selecionar ou arraste o arquivo CSV</p>
                    <p class="text-xs text-gray-400 mt-2">Tamanho máximo: 5MB</p>
                    <input type="file" id="fileInput" name="file" accept=".csv" class="hidden" onchange="this.form.submit()">
                </div>

                <div class="flex justify-center">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold shadow-lg shadow-blue-100 transition-all">
                        Processar Arquivo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
