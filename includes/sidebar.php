<aside
    class="w-72 bg-[linear-gradient(180deg,_#0f2e1d_0%,_#174d2c_55%,_#0f2e1d_100%)] text-white min-h-screen sticky top-0 flex flex-col shadow-2xl">
    <div class="p-6 border-b border-emerald-900/50">
        <div class="flex items-center gap-3">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/95 p-2 shadow-lg">
                <img src="../img/logo_cooperante.png" alt="Cooperante" class="max-h-full max-w-full object-contain">
            </div>
            <div>
                <h1 class="text-lg font-extrabold tracking-wide">Cooperante</h1>
                <p class="text-xs text-emerald-100">Gestão de Frota</p>
            </div>
        </div>
    </div>
    <nav class="flex-1 p-4 space-y-2">
        <a href="index.php"
            class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/10 transition-colors <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-white/10 text-emerald-200' : 'text-emerald-50'; ?>">
            <i class="fa-solid fa-chart-line w-5"></i> Dashboard
        </a>
        <a href="vehicles.php"
            class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/10 transition-colors <?php echo basename($_SERVER['PHP_SELF']) == 'vehicles.php' ? 'bg-white/10 text-emerald-200' : 'text-emerald-50'; ?>">
            <i class="fa-solid fa-car w-5"></i> Veículos
        </a>

        <a href="reports.php"
            class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/10 transition-colors <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'bg-white/10 text-emerald-200' : 'text-emerald-50'; ?>">
            <i class="fa-solid fa-file-invoice-dollar w-5"></i> Custos Gerais
        </a>

        <a href="fuel.php"
            class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/10 transition-colors <?php echo basename($_SERVER['PHP_SELF']) == 'fuel.php' ? 'bg-white/10 text-emerald-200' : 'text-emerald-50'; ?>">
            <i class="fa-solid fa-gas-pump w-5"></i> Abastecimentos
        </a>

        <a href="lava_rapido.php"
            class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/10 transition-colors <?php echo basename($_SERVER['PHP_SELF']) == 'lava_rapido.php' ? 'bg-white/10 text-emerald-200' : 'text-emerald-50'; ?>">
            <i class="fa-solid fa-soap w-5"></i> Lava Rápido
        </a>
        <a href="veloe.php"
            class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/10 transition-colors <?php echo basename($_SERVER['PHP_SELF']) == 'veloe.php' ? 'bg-white/10 text-emerald-200' : 'text-emerald-50'; ?>">
            <i class="fa-solid fa-credit-card w-5"></i> Cartão Veloe
        </a>
        <a href="manutencao.php"
            class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/10 transition-colors <?php echo basename($_SERVER['PHP_SELF']) == 'manutencao.php' ? 'bg-white/10 text-emerald-200' : 'text-emerald-50'; ?>">
            <i class="fa-solid fa-screwdriver-wrench w-5"></i> Manutenção
        </a>
        <a href="import.php"
            class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/10 transition-colors <?php echo basename($_SERVER['PHP_SELF']) == 'import.php' ? 'bg-white/10 text-emerald-200' : 'text-emerald-50'; ?>">
            <i class="fa-solid fa-file-import w-5"></i> Importar
        </a>
    </nav>
    <div class="p-4 border-t border-emerald-900/50">
        <p class="text-xs text-emerald-100/70 text-center">v1.0.0 &copy; 2026</p>
    </div>
</aside>