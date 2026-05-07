<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Frota</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <?php if (!empty($useChartJs)): ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php endif; ?>
    <?php if (!empty($pageStyles) && is_array($pageStyles)): ?>
        <?php foreach ($pageStyles as $pageStyle): ?>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($pageStyle, ENT_QUOTES, 'UTF-8'); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<body class="bg-[linear-gradient(180deg,_#f5fbf6_0%,_#eef7f1_100%)] flex text-gray-800">
    <?php include 'sidebar.php'; ?>
    <div class="flex min-w-0 flex-1 flex-col min-h-screen">
        <header
            class="sticky top-0 z-10 flex min-h-16 items-center justify-between gap-3 border-b border-emerald-100 bg-white/95 px-4 py-3 backdrop-blur sm:px-6 lg:px-8">
            <div class="flex min-w-0 items-center gap-3">
                <button type="button" data-mobile-menu-button
                    class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-emerald-100 bg-emerald-50 text-emerald-700 lg:hidden">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h2 class="truncate text-base font-semibold text-gray-800 sm:text-xl">
                    <?php echo $pageTitle ?? 'Dashboard'; ?>
                </h2>
            </div>
            <div class="flex items-center gap-3 sm:gap-4">
                <span class="hidden text-sm text-gray-600 sm:inline">Olá!,
                    <strong><?php echo $_SESSION['username'] ?? 'UsuÃƒÂ¡rio'; ?></strong></span>
                <a href="logout.php" class="text-gray-500 transition-colors hover:text-red-600">
                    <i class="fa-solid fa-right-from-bracket"></i><span class="ml-2 hidden sm:inline">Sair</span>
                </a>
            </div>
        </header>
        <main class="flex-1 p-4 sm:p-6 lg:p-8">