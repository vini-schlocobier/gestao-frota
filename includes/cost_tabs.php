<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$costTabs = [
    'reports.php' => ['label' => 'Custos Gerais', 'icon' => 'fa-file-invoice-dollar'],
    'fuel.php' => ['label' => 'Combustível', 'icon' => 'fa-gas-pump'],
    'lava_rapido.php' => ['label' => 'Lava Rápido', 'icon' => 'fa-soap'],
    'veloe.php' => ['label' => 'Dados Cartão Veloe', 'icon' => 'fa-credit-card'],
    'manutencao.php' => ['label' => 'Manutenção', 'icon' => 'fa-screwdriver-wrench'],

];
?>
<div class="mb-6 overflow-x-auto">
    <div class="inline-flex min-w-full gap-2 rounded-2xl bg-white/90 p-2 shadow-sm ring-1 ring-emerald-100">
        <?php foreach ($costTabs as $href => $tab): ?>
            <?php $isActive = $currentPage === $href; ?>
            <a href="<?php echo $href; ?>"
                class="flex items-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold transition-colors whitespace-nowrap <?php echo $isActive ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-100' : 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-700'; ?>">
                <i class="fa-solid <?php echo $tab['icon']; ?>"></i>
                <?php echo $tab['label']; ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>