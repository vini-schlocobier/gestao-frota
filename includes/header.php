<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Frota</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap');
        :root {
            --brand-50: #effcf3;
            --brand-100: #d8f6e1;
            --brand-500: #169c54;
            --brand-600: #118449;
            --brand-700: #0e6d3d;
            --brand-shadow: rgba(17, 132, 73, 0.2);
        }
        body { font-family: 'Manrope', sans-serif; }
        .bg-blue-50 { background-color: var(--brand-50) !important; }
        .bg-blue-100 { background-color: var(--brand-100) !important; }
        .bg-blue-600 { background-color: var(--brand-600) !important; }
        .bg-blue-700 { background-color: var(--brand-700) !important; }
        .text-blue-100 { color: #d7f7e1 !important; }
        .text-blue-400, .text-blue-500, .text-blue-600, .text-blue-700, .text-blue-800 { color: var(--brand-600) !important; }
        .border-blue-100 { border-color: var(--brand-100) !important; }
        .border-blue-700 { border-color: var(--brand-700) !important; }
        .hover\:bg-blue-50:hover { background-color: var(--brand-50) !important; }
        .hover\:bg-blue-500:hover,
        .hover\:bg-blue-600:hover,
        .hover\:bg-blue-700:hover { background-color: var(--brand-700) !important; }
        .hover\:text-blue-500:hover,
        .hover\:text-blue-600:hover { color: var(--brand-600) !important; }
        .focus\:ring-blue-500:focus,
        .focus\:border-blue-500:focus {
            --tw-ring-color: rgba(22, 156, 84, 0.35) !important;
            border-color: var(--brand-500) !important;
        }
        .shadow-blue-100,
        .shadow-blue-200 { --tw-shadow-color: var(--brand-shadow) !important; }
    </style>
</head>
<body class="bg-[linear-gradient(180deg,_#f5fbf6_0%,_#eef7f1_100%)] flex text-gray-800">
<?php include 'sidebar.php'; ?>
<div class="flex-1 flex flex-col min-h-screen">
    <header class="bg-white/95 backdrop-blur border-b border-emerald-100 h-16 flex items-center justify-between px-8 sticky top-0 z-10">
        <h2 class="text-xl font-semibold text-gray-800"><?php echo $pageTitle ?? 'Dashboard'; ?></h2>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600">Olá, <strong><?php echo $_SESSION['username'] ?? 'Usuário'; ?></strong></span>
            <a href="logout.php" class="text-gray-500 hover:text-red-600 transition-colors">
                <i class="fa-solid fa-right-from-bracket"></i> Sair
            </a>
        </div>
    </header>
    <main class="p-8 flex-1">
