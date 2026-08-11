<?php
/**
 * @var string $cedula
 * @var string $last_connection
 * @var string $page
 * @var string $totalPages
 * @var array $staffList
 * @var array $departamentos
 * @var array $conditions
 * @var array $contractTypes
 * @var array $userRoles
 */

$flash = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Gestión de Personal - UPTVal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        uptval: {
                            orange: '#d97b29',
                            dark: '#a35715',
                            grey: '#808285'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Glassmorphism */
        .glass-panel {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        
        /* Scrollbar personalizado */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #334155; }

        /* Menú móvil */
        .mobile-menu {
            transition: transform 0.3s ease-in-out;
            transform: translateX(-100%);
        }
        .mobile-menu.open { transform: translateX(0); }
        body.menu-open { overflow: hidden; }

        /* ==========================================
           CIRUGÍA 1: Animación del Gráfico
           ========================================== */
        @keyframes drawCircle {
            from { stroke-dasharray: 0 100; }
        }
        .chart-circle {
            animation: drawCircle 1.5s ease-out forwards;
        }

        @keyframes shrink {
            from { width: 100%; }
            to { width: 0%; }
        }
        .animate-shrink {
            animation: shrink 4s linear forwards;
        }
    </style>
</head>
<body class="bg-slate-950 h-screen text-white font-sans flex flex-col overflow-hidden">

    <div id="toast" class="fixed top-6 right-6 z-[200] transform transition-all duration-500 translate-x-[150%] opacity-0 w-full max-w-sm bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0" id="toast-icon-container"></div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p id="toast-title" class="text-sm font-medium text-gray-900"></p>
                    <p id="toast-message" class="mt-1 text-sm text-gray-500"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button type="button" onclick="hideToast()" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-uptval-orange transition-colors">
                        <span class="sr-only">Cerrar</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gray-50 w-full">
            <div id="toast-progress" class="h-full w-full"></div>
        </div>
    </div>

        <nav class="glass-panel border-b border-gray-800 px-4 sm:px-6 py-3 sm:py-4 flex justify-between items-center z-50 shrink-0">
            <div class="flex items-center gap-2">
                <button id="menuToggle" class="md:hidden text-gray-300 hover:text-white focus:outline-none mr-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                
                <h1 class="text-xl sm:text-2xl font-bold tracking-wider">
                    <span class="text-uptval-orange">UPT</span>Val
                </h1>
                <span class="text-xs text-gray-500 uppercase tracking-widest hidden md:inline ml-4 border-l border-gray-700 pl-4">Gestión Académica</span>
            </div>
            
            <div class="flex items-center gap-2 sm:gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-medium text-gray-300">Usuario Activo</p>
                    <p class="text-xs text-uptval-orange font-bold">C.I: <?php echo htmlspecialchars($cedula ?? ''); ?></p>
                    <?php if (!empty($last_connection)): ?>
                        <p class="text-[11px] text-gray-500 mt-1">Última conexión: <?php echo htmlspecialchars($last_connection); ?></p>
                    <?php endif; ?>
                </div>
                
                <form action="/logout" method="POST" class="m-0">
                    <button type="submit" class="bg-red-500/10 hover:bg-red-500/20 text-red-500 border border-red-500/20 transition-colors duration-300 px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium flex items-center gap-1 sm:gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="hidden sm:inline">Salir</span>
                    </button>
                </form>
            </div>
        </nav>

        <div class="flex flex-1 overflow-hidden relative">
            
            <div class="absolute top-[-10%] left-[20%] w-96 h-96 bg-uptval-orange rounded-full mix-blend-screen filter blur-[150px] opacity-10 pointer-events-none z-0"></div>

            <aside class="w-64 glass-panel border-r border-gray-800 flex flex-col py-6 px-4 shrink-0 overflow-y-auto hidden md:flex relative z-10">
                <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-4 px-4">Menú Principal</p>
            
                <nav class="space-y-1.5 flex-1">
        
        <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
            <i class="ph ph-squares-four text-xl"></i>
            <span class="font-medium text-sm">Inicio</span>
        </a>

        <?php if (in_array('Administrador', $userRoles)): ?>
        <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
            <i class="ph ph-buildings text-xl"></i>
            <span class="font-medium text-sm">Gestión de Departamentos</span>
        </a>
        <?php endif; ?>

        <?php if (in_array('Administrador', $userRoles)): ?>
        <a href="/personal" class="flex items-center gap-3 px-4 py-3 text-uptval-orange bg-uptval-orange/10 rounded-xl transition-all duration-300 border-l-2 border-uptval-orange shadow-[inset_0px_0px_20px_rgba(217,123,41,0.05)]">
            <i class="ph ph-users text-xl"></i>
            <span class="font-medium text-sm">Gestión de Personal</span>
        </a>
        <?php endif; ?>

        <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
            <i class="ph ph-student text-xl"></i>
            <span class="font-medium text-sm">Estudiantes</span>
        </a>

        <?php if (in_array('Administrador', $userRoles)): ?>
        <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
            <i class="ph ph-calendar-check text-xl"></i>
            <span class="font-medium text-sm">Control de Asistencia</span>
        </a>
        <?php endif; ?>
    </nav>

                <?php if (in_array('Administrador', $userRoles)): ?>
                <div class="mt-8">
                    <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-4 px-4">Sistema</p>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
                        <i class="ph ph-gear text-xl"></i>
                        <span class="font-medium text-sm">Configuración</span>
                    </a>
                </div>
                <?php endif; ?>
            </aside>

            <div id="mobileSidebar" class="mobile-menu fixed top-0 left-0 h-full w-64 glass-panel border-r border-gray-800 flex flex-col py-6 px-4 z-50 md:hidden shadow-2xl">
                <div class="flex justify-between items-center mb-6 px-4">
                    <h2 class="text-lg font-bold text-white">Menú</h2>
                    <button id="closeMenu" class="text-gray-400 hover:text-white">
                        <i class="ph ph-x text-2xl"></i>
                    </button>
                </div>
                
                <nav class="space-y-1.5 flex-1">
                    <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300">
                        <i class="ph ph-squares-four text-xl"></i>
                        <span class="font-medium text-sm">Inicio</span>
                    </a>
                    <?php if (in_array('Administrador', $userRoles)): ?>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300">
                        <i class="ph ph-buildings text-xl"></i>
                        <span class="font-medium text-sm">Gestión de Departamentos</span>
                    </a>
                    <a href="/personal" class="flex items-center gap-3 px-4 py-3 text-uptval-orange bg-uptval-orange/10 rounded-xl transition-all duration-300 border-l-2 border-uptval-orange">
                        <i class="ph ph-users text-xl"></i>
                        <span class="font-medium text-sm">Gestión de Personal</span>
                    </a>
                    <?php endif; ?>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300">
                        <i class="ph ph-student text-xl"></i>
                        <span class="font-medium text-sm">Estudiantes</span>
                    </a>
                    <?php if (in_array('Administrador', $userRoles)): ?>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300">
                        <i class="ph ph-calendar-check text-xl"></i>
                        <span class="font-medium text-sm">Control de Asistencia</span>
                    </a>
                    
                    <div class="pt-6 mt-6 border-t border-gray-800">
                        <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300">
                            <i class="ph ph-gear text-xl"></i>
                            <span class="font-medium text-sm">Configuración</span>
                        </a>
                    </div>
                    <?php endif; ?>
                </nav>
                
                <div class="mt-auto pt-4 border-t border-gray-800">
                    <div class="flex items-center gap-3 px-4 py-3">
                        <div class="w-10 h-10 rounded-full bg-uptval-orange/20 flex items-center justify-center flex-shrink-0">
                            <i class="ph ph-user text-xl text-uptval-orange"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-400">Usuario Activo</p>
                            <p class="text-sm text-uptval-orange font-bold">C.I: <?php echo htmlspecialchars($cedula ?? ''); ?></p>
                            <?php if (!empty($last_connection)): ?>
                                <p class="text-[11px] text-gray-500 mt-1">Última conexión: <?php echo htmlspecialchars($last_connection); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            
                <div id="menuOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden md:hidden"></div>

            <main class="flex-1 overflow-y-auto bg-gray-50 text-gray-800 flex flex-col relative z-10">
                
                <header class="bg-white px-6 sm:px-8 py-5 shadow-sm border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 sticky top-0 z-20">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Gestión de Personal</h2>
                        <p class="text-sm text-gray-500 mt-1">Administra docentes, administrativos y asignaciones académicas.</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                        <?php
                            $filterQuery = [];
                            if (!empty($_GET['department_filter'])) $filterQuery['department_filter'] = $_GET['department_filter'];
                            if (!empty($_GET['type_filter'])) $filterQuery['type_filter'] = $_GET['type_filter'];
                            if (!empty($_GET['condition_filter'])) $filterQuery['condition_filter'] = $_GET['condition_filter'];
                            if (!empty($_GET['contract_filter'])) $filterQuery['contract_filter'] = $_GET['contract_filter'];
                            if (!empty($_GET['search'])) $filterQuery['search'] = $_GET['search'];
                            $pdfUrl = '/personal/export-pdf' . (!empty($filterQuery) ? '?' . http_build_query($filterQuery) : '');
                        ?>
                        <a href="<?= $pdfUrl ?>"
                           class="bg-white border border-slate-300 text-slate-700 px-4 py-2.5 rounded-lg font-medium shadow-sm transition-all flex items-center justify-center gap-2 hover:bg-slate-50 w-full sm:w-auto">
                            <i class="ph ph-file-pdf text-lg text-red-500"></i>
                            Exportar PDF
                        </a>
                        <button onclick="toggleModal('modalRegistrar')" class="bg-slate-900 hover:bg-black text-white px-5 py-2.5 rounded-lg font-medium shadow-sm transition-all flex items-center justify-center gap-2 transform hover:scale-105 w-full sm:w-auto">
                            <i class="ph ph-plus-circle text-lg"></i>
                            Registrar Personal
                        </button>
                    </div>
                </header>

                <div class="p-4 sm:p-6 md:p-8 flex-1">
                    
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        
                        <div class="border-b border-gray-200 bg-gray-50/50 px-4">
                            <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
                                <a href="#" class="border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2" aria-current="page">
                                    <i class="ph ph-users-three text-lg"></i>
                                    Directorio General
                                </a>
                                <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                                    <i class="ph ph-books text-lg"></i>
                                    Asignación Académica
                                </a>
                                <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                                    <i class="ph ph-shield-check text-lg"></i>
                                    Permisos y Roles
                                </a>
                            </nav>
                        </div>

                        <form method="GET" action="/personal" class="p-4 border-b border-gray-100 flex flex-col sm:flex-row gap-4 items-center justify-between">
                            <div class="relative w-full sm:max-w-xs">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="ph ph-magnifying-glass text-gray-400"></i>
                                </div>
                                <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors" placeholder="Buscar por cédula o nombre...">
                            </div>
                            

                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 w-full">
        
                                <div class="relative">
                                    <label class="absolute -top-2.5 left-3 px-1 bg-white text-[10px] font-bold text-uptval-black tracking-wider pointer-events-none z-10">
                                        Departamento
                                    </label>
                                    <select name="department_filter" onchange="this.form.submit()" class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-uptval-blue focus:border-uptval-blue bg-white transition-all">
                                        <option value="">Todos los Departamentos</option>
                                        <?php foreach ($departamentos as $depto): ?>
                                            <option value="<?php echo htmlspecialchars($depto['id_department']); ?>" <?php echo (($_GET['department_filter'] ?? '') == $depto['id_department']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($depto['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="relative">
                                    <label class="absolute -top-2.5 left-3 px-1 bg-white text-[10px] font-bold text-uptval-black tracking-wider pointer-events-none z-10">
                                        Tipo
                                    </label>
                                    <select name="type_filter" onchange="this.form.submit()" class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-uptval-blue focus:border-uptval-blue bg-white transition-all">
                                        <option value="">Todos los Tipos</option>
                                        <option value="Administrativo" <?php echo (($_GET['type_filter'] ?? '') === 'Administrativo') ? 'selected' : ''; ?>>Administrativo</option>
                                        <option value="Docente" <?php echo (($_GET['type_filter'] ?? '') === 'Docente') ? 'selected' : ''; ?>>Docente</option>                                        
                                        <option value="Obrero" <?php echo (($_GET['type_filter'] ?? '') === 'Obrero') ? 'selected' : ''; ?>>Obrero</option>
                                    </select>
                                </div>

                                <div class="relative">
                                    <label class="absolute -top-2.5 left-3 px-1 bg-white text-[10px] font-bold text-uptval-blac tracking-wider pointer-events-none z-10">
                                        Condicion
                                    </label>
                                    <select name="condition_filter" onchange="this.form.submit()" class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-uptval-blue focus:border-uptval-blue bg-white transition-all">
                                        <option value="">Todas las Condiciones</option>
                                        <?php foreach ($conditions as $cond): ?>
                                            <option value="<?php echo htmlspecialchars($cond['id_condition']); ?>" <?php echo (($_GET['condition_filter'] ?? '') == $cond['id_condition']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cond['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="relative">
                                    <label class="absolute -top-2.5 left-3 px-1 bg-white text-[10px] font-bold text-uptval-blac tracking-wider pointer-events-none z-10">
                                        Tipo Contrato
                                    </label>
                                    <select name="contract_filter" onchange="this.form.submit()" class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-uptval-blue focus:border-uptval-blue bg-white transition-all">
                                        <option value="">Todos los Contratos</option>
                                        <?php foreach ($contractTypes as $ct): ?>
                                            <option value="<?php echo htmlspecialchars($ct['id_contract']); ?>" <?php echo (($_GET['contract_filter'] ?? '') == $ct['id_contract']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($ct['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                            </div>
                        </form>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <th scope="col" class="px-6 py-3 text-left">Cedula</th>
                                        <th scope="col" class="px-6 py-3 text-left">Personal</th>
                                        <th scope="col" class="px-6 py-3 text-left">Tipo / Departamento</th>
                                        <th scope="col" class="px-6 py-3 text-center">Condicion</th>
                                        <th scope="col" class="px-6 py-3 text-center">Tipo Contrato</th>
                                        <th scope="col" class="px-6 py-3 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($staffList) && !empty($_GET['department_filter'] ?? $_GET['type_filter'] ?? $_GET['condition_filter'] ?? $_GET['contract_filter'] ?? $_GET['search'] ?? '')): ?>
                                        <tr>
                                            <td colspan="6" class="py-10 text-center text-gray-500 bg-gray-50">
                                                <i class="ph ph-funnel text-5xl mb-3 block opacity-50"></i>
                                                No se encontraron resultados con los filtros aplicados.
                                            </td>
                                        </tr>
                                    <?php elseif (empty($staffList)): ?>
                                        <tr>
                                            <td colspan="6" class="py-10 text-center text-gray-500 bg-gray-50">
                                                <i class="ph ph-user-circle-minus text-5xl mb-3 block opacity-50"></i>
                                                No hay personal registrado en la base de datos. <br>
                                                Usa el botón "Registrar Personal" para comenzar.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($staffList as $person): ?>
                                            <?php 
                                                // Obtener las iniciales del nombre y apellido para el Avatar
                                                $initials = strtoupper(substr($person['first_name'], 0, 1) . substr($person['last_name'], 0, 1));
                                            ?>
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900 font-mono">
                                                        <?php echo htmlspecialchars($person['cedula']); ?>
                                                    </div>
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold">
                                                                <?php echo $initials; ?>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                <?php echo htmlspecialchars($person['last_name'] . ', ' . $person['first_name']); ?>
                                                            </div>
                                                            <div class="text-sm text-gray-500">
                                                                <?php echo htmlspecialchars($person['email']); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">
                                                        <?php echo htmlspecialchars($person['pas']); ?>
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        <?php echo htmlspecialchars($person['department_name'] ?? 'Sin Asignar'); ?>
                                                    </div>
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <?php if (!empty($person['condition_name'])): ?>
                                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-700 border border-gray-200">
                                                            <?php echo htmlspecialchars($person['condition_name']); ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-500 border border-gray-200">
                                                            Sin condicion
                                                        </span>
                                                    <?php endif; ?>
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <?php if (!empty($person['contract_name'])): ?>
                                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-700 border border-gray-200">
                                                            <?php echo htmlspecialchars($person['contract_name']); ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-500 border border-gray-200">
                                                            Sin contrato
                                                        </span>
                                                    <?php endif; ?>
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <button onclick="viewStaff(<?= (int) $person['id_staff'] ?>)" class="text-slate-400 hover:text-blue-600 transition-colors p-1" title="Ver Detalles">
                                                            <i class="ph ph-eye text-xl"></i>
                                                        </button>
                                                        <button class="text-slate-400 hover:text-amber-500 transition-colors p-1" title="Editar">
                                                            <i class="ph ph-pencil-simple text-xl"></i>
                                                        </button>
                                                    </div>
                                                </td>

                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center justify-center border-t border-gray-200 bg-gray-50/50 px-4 py-4 sm:px-6 w-full">
                            <?php
                                $filterQuery = [];
                                if (!empty($_GET['department_filter'])) $filterQuery['department_filter'] = $_GET['department_filter'];
                                if (!empty($_GET['type_filter'])) $filterQuery['type_filter'] = $_GET['type_filter'];
                                if (!empty($_GET['condition_filter'])) $filterQuery['condition_filter'] = $_GET['condition_filter'];
                                if (!empty($_GET['contract_filter'])) $filterQuery['contract_filter'] = $_GET['contract_filter'];
                                if (!empty($_GET['search'])) $filterQuery['search'] = $_GET['search'];
                                $filterQueryStr = !empty($filterQuery) ? '&' . http_build_query($filterQuery) : '';
                            ?>
                            <div class="flex items-center gap-6">
                                <?php if ($page > 1): ?>
                                    <a href="/personal?page=<?= $page - 1 ?><?= $filterQueryStr ?>" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                                        Anterior
                                    </a>
                                <?php else: ?>
                                    <button disabled class="relative inline-flex items-center rounded-md border border-gray-200 bg-gray-100/50 px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed shadow-sm">
                                        Anterior
                                    </button>
                                <?php endif; ?>

                                <div>
                                    <p class="text-sm text-gray-700">
                                        Página <span class="font-medium"><?= $page ?></span> de 
                                        <span class="font-medium"><?= $totalPages > 0 ? $totalPages : 1 ?></span>
                                    </p>
                                </div>

                                <?php if ($page < $totalPages): ?>
                                    <a href="/personal?page=<?= $page + 1 ?><?= $filterQueryStr ?>" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                                        Siguiente
                                    </a>
                                <?php else: ?>
                                    <button disabled class="relative inline-flex items-center rounded-md border border-gray-200 bg-gray-100/50 px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed shadow-sm">
                                        Siguiente
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                    
                    <div class="mt-8 mb-4">
                        <h3 class="text-lg font-bold text-slate-800 mb-6 text-center sm:text-left flex items-center justify-center sm:justify-start gap-2">
                            <i class="ph ph-chart-donut text-uptval-orange text-xl"></i> Resumen General de la Consulta
                        </h3>
                        
                        <?php
                        $typeColors = [
                            'Docente'        => ['hex' => '#3b82f6', 'bg' => 'bg-blue-500', 'shadow' => 'shadow-blue-200'],
                            'Administrativo' => ['hex' => '#f97316', 'bg' => 'bg-uptval-orange', 'shadow' => 'shadow-orange-200'],
                            'Obrero'         => ['hex' => '#10b981', 'bg' => 'bg-emerald-500', 'shadow' => 'shadow-emerald-200'],
                        ];
                        $totalStaff = array_sum(array_column($statsByType, 'total'));
                        $runningPercent = 0;
                        $typeCount = count($statsByType);
                        ?>
                        
                        <div class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-8 shadow-sm max-w-4xl mx-auto flex flex-col md:flex-row items-center gap-8 sm:gap-10">
                            
                            <div class="relative w-40 h-40 sm:w-48 sm:h-48 shrink-0">
                                <svg viewBox="0 0 40 40" class="w-full h-full -rotate-90 drop-shadow-md">
                                    <?php foreach ($statsByType as $type): 
                                        $percent = $totalStaff > 0 ? round($type['total'] / $totalStaff * 100, 1) : 0;
                                        $color = $typeColors[$type['pas']] ?? ['hex' => '#6b7280', 'bg' => 'bg-gray-500', 'shadow' => 'shadow-gray-200'];
                                    ?>
                                    <circle cx="20" cy="20" r="15.915" fill="none" stroke="<?= $color['hex'] ?>" stroke-width="6" stroke-dasharray="<?= $percent ?> <?= 100 - $percent ?>" stroke-dashoffset="-<?= $runningPercent ?>" class="transition-all duration-1000 ease-out"></circle>
                                    <?php 
                                        $runningPercent += $percent;
                                    endforeach; 
                                    ?>
                                </svg>
                                
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-800"><?= $totalStaff ?></span>
                                    <span class="text-[9px] sm:text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Registros</span>
                                </div>
                            </div>
                            
                            <div class="flex-1 w-full grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">
                                
                                <?php foreach ($statsByType as $i => $type): 
                                    $percent = $totalStaff > 0 ? round($type['total'] / $totalStaff * 100) : 0;
                                    $color = $typeColors[$type['pas']] ?? ['hex' => '#6b7280', 'bg' => 'bg-gray-500', 'shadow' => 'shadow-gray-200'];
                                    $isLast = $i === $typeCount - 1;
                                ?>
                                <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 <?= $isLast && $typeCount === 3 ? 'sm:col-span-2' : '' ?>">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="flex items-center gap-2 font-bold text-slate-700 text-sm sm:text-base">
                                            <div class="w-3 h-3 rounded-full <?= $color['bg'] ?> shadow-sm <?= $color['shadow'] ?>"></div> 
                                            <?= htmlspecialchars($type['pas']) ?>s (<?= $percent ?>%)
                                        </span>
                                        <span class="font-extrabold text-slate-900 text-lg"><?= $type['total'] ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>

                            </div>
                        </div>

                    </div>
                    </div>
            </main>
        </div>

    <div id="modalRegistrar" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[100] transition-opacity p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform scale-95 transition-transform modal-content" id="modalContent">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 text-gray-800">
                <h3 class="text-lg font-bold">Registrar Nuevo Personal</h3>
                <button onclick="toggleModal('modalRegistrar')" class="text-gray-400 hover:text-red-500 transition-colors"><i class="ph ph-x text-xl"></i></button>
            </div>
            <div class="p-6">
                <form method="POST" action="/personal/store" class="text-gray-800">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cédula</label>
                            <input type="text" name="cedula" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white">
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombres</label>
                            <input type="text" name="first_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white">
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos</label>
                            <input type="text" name="last_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white">
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sexo</label>
                            <select name="sex" required class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none bg-white">
                                <option value="">Seleccione...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Correo Institucional</label>
                            <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white">
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono <span class="text-gray-400">(opcional)</span></label>
                            <input type="text" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white">
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Personal</label>
                            <select name="pas" required class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none bg-white">
                                <option value="">Seleccione...</option>
                                <option value="Docente">Docente</option>
                                <option value="Administrativo">Administrativo</option>
                                <option value="Obrero">Obrero</option>
                            </select>
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Nombramiento</label>
                            <select name="type_staff" required class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none bg-white">
                                <option value="">Seleccione...</option>
                                <option value="Regular">Regular</option>
                                <option value="Contratado">Contratado</option>
                            </select>
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                            <select name="id_department" required class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none bg-white">
                                <option value="">Seleccione...</option>
                                <?php foreach ($departamentos as $depto): ?>
                                    <option value="<?php echo htmlspecialchars($depto['id_department']); ?>">
                                        <?php echo htmlspecialchars($depto['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Condición</label>
                            <select name="type_condition" required class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none bg-white">
                                <option value="">Seleccione...</option>
                                <?php foreach ($conditions as $cond): ?>
                                    <option value="<?php echo htmlspecialchars($cond['id_condition']); ?>">
                                        <?php echo htmlspecialchars($cond['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Contrato</label>
                            <select name="type_contract" required class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none bg-white">
                                <option value="">Seleccione...</option>
                                <?php foreach ($contractTypes as $ct): ?>
                                    <option value="<?php echo htmlspecialchars($ct['id_contract']); ?>">
                                        <?php echo htmlspecialchars($ct['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-200 bg-gray-50 -mx-6 -mb-6 px-6 py-4 flex flex-col sm:flex-row justify-end gap-3 rounded-b-2xl">
                        <button type="button" onclick="toggleModal('modalRegistrar')" class="px-4 py-2 text-gray-600 font-medium hover:bg-gray-200 rounded-lg w-full sm:w-auto">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-black text-white font-medium rounded-lg shadow-md w-full sm:w-auto">Guardar Registro</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="modalVerPersonal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[100] transition-opacity p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform scale-95 transition-transform modal-content">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 text-gray-800">
                <h3 class="text-lg font-bold">Detalle del Personal</h3>
                <button onclick="toggleModal('modalVerPersonal')" class="text-gray-400 hover:text-red-500 transition-colors"><i class="ph ph-x text-xl"></i></button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-gray-800">
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cédula</label>
                        <input type="text" id="view_cedula" readonly class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-700 outline-none">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombres</label>
                        <input type="text" id="view_first_name" readonly class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-700 outline-none">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos</label>
                        <input type="text" id="view_last_name" readonly class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-700 outline-none">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sexo</label>
                        <input type="text" id="view_sex" readonly class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-700 outline-none">
                    </div>
                    <div class="col-span-1 sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo Institucional</label>
                        <input type="text" id="view_email" readonly class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-700 outline-none">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" id="view_phone" readonly class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-700 outline-none">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Personal</label>
                        <input type="text" id="view_pas" readonly class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-700 outline-none">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Nombramiento</label>
                        <input type="text" id="view_type_staff" readonly class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-700 outline-none">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                        <input type="text" id="view_department" readonly class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-700 outline-none">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Condición</label>
                        <input type="text" id="view_condition" readonly class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-700 outline-none">
                    </div>
                    <div class="col-span-1 sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Contrato</label>
                        <input type="text" id="view_contract" readonly class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-700 outline-none">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-end gap-3 rounded-b-2xl">
                <a id="viewPdfButton" href="#" class="px-4 py-2 bg-slate-900 hover:bg-black text-white font-medium rounded-lg shadow-md text-center">
                    <i class="ph ph-file-pdf text-lg text-red-500 mr-1"></i> Descargar PDF
                </a>
                <button onclick="toggleModal('modalVerPersonal')" class="px-4 py-2 text-gray-600 font-medium hover:bg-gray-200 rounded-lg">Cerrar</button>
            </div>
        </div>
    </div>
    
    <script>
        // Lógica del Menú Móvil
        const menuToggle = document.getElementById('menuToggle');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const closeMenu = document.getElementById('closeMenu');
        const menuOverlay = document.getElementById('menuOverlay');

        function openMenu() {
            mobileSidebar.classList.add('open');
            menuOverlay.classList.remove('hidden');
            document.body.classList.add('menu-open');
        }

        function closeMenuFunc() {
            mobileSidebar.classList.remove('open');
            menuOverlay.classList.add('hidden');
            document.body.classList.remove('menu-open');
        }

        if (menuToggle) menuToggle.addEventListener('click', openMenu);
        if (closeMenu) closeMenu.addEventListener('click', closeMenuFunc);
        if (menuOverlay) menuOverlay.addEventListener('click', closeMenuFunc);

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) closeMenuFunc();
        });

        // Lógica del Modal
        function toggleModal(modalID) {
            const modal = document.getElementById(modalID);
            const content = modal.querySelector('.modal-content');
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    content.classList.remove('scale-95');
                    content.classList.add('scale-100');
                }, 10);
            } else {
                content.classList.remove('scale-100');
                content.classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }, 150);
            }
        }

        // =========================================================
        // LÓGICA DE VER PERSONAL (MODAL + PDF)
        // =========================================================
        const staffData = <?php echo json_encode($staffList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

        function viewStaff(id) {
            const record = staffData.find(function(item) {
                return parseInt(item.id_staff, 10) === parseInt(id, 10);
            });

            if (!record) {
                showToast('error', 'Registro no encontrado', 'El personal solicitado no está en la página actual.');
                return;
            }

            document.getElementById('view_cedula').value = record.cedula || '';
            document.getElementById('view_first_name').value = record.first_name || '';
            document.getElementById('view_last_name').value = record.last_name || '';
            document.getElementById('view_sex').value = record.sex === 'M' ? 'Masculino' : (record.sex === 'F' ? 'Femenino' : record.sex || '');
            document.getElementById('view_email').value = record.email || '';
            document.getElementById('view_phone').value = record.phone || 'No registrado';
            document.getElementById('view_pas').value = record.pas || '';
            document.getElementById('view_type_staff').value = record.type_staff || '';
            document.getElementById('view_department').value = record.department_name || 'Sin Asignar';
            document.getElementById('view_condition').value = record.condition_name || 'Sin condicion';
            document.getElementById('view_contract').value = record.contract_name || 'Sin contrato';

            document.getElementById('viewPdfButton').href = '/personal/export-pdf-one?id=' + encodeURIComponent(record.id_staff);

            toggleModal('modalVerPersonal');
        }

        // =========================================================
        // LÓGICA DE NOTIFICACIONES (TOASTS)
        // =========================================================
        const icons = {
            success: `<svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`,
            error: `<svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`
        };

        function showToast(type, title, message) {
            const toast = document.getElementById('toast');
            const toastTitle = document.getElementById('toast-title');
            const toastMessage = document.getElementById('toast-message');
            const toastProgress = document.getElementById('toast-progress');
            const iconContainer = document.getElementById('toast-icon-container');

            toastTitle.innerText = title;
            toastMessage.innerText = message;

            if (type === 'error') {
                iconContainer.innerHTML = icons.error;
                toastProgress.className = 'h-full w-full bg-red-400';
            } else {
                iconContainer.innerHTML = icons.success;
                toastProgress.className = 'h-full w-full bg-green-400';
            }

            toastProgress.classList.remove('animate-shrink');
            void toastProgress.offsetWidth;
            toastProgress.classList.add('animate-shrink');

            toast.classList.remove('translate-x-[150%]', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');

            setTimeout(() => { hideToast(); }, 4000);
        }

        function hideToast() {
            const toast = document.getElementById('toast');
            toast.classList.remove('translate-x-0', 'opacity-100');
            toast.classList.add('translate-x-[150%]', 'opacity-0');
        }
    </script>

    <?php if ($flash): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            showToast('<?= $flash['type'] ?>', '<?= addslashes($flash['title']) ?>', '<?= addslashes($flash['message']) ?>');
        });
    </script>
    <?php endif; ?>
</body>
</html>