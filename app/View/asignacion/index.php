<?php
/**
 * @var string $cedula
 * @var string $last_connection
 * @var int $page
 * @var int $totalPages
 * @var array $assignments
 * @var array $materias
 * @var array $trayectos
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
    <title>Asignación Académica - UPTVal</title>
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
        .glass-panel {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #334155; }

        .mobile-menu {
            transition: transform 0.3s ease-in-out;
            transform: translateX(-100%);
        }
        .mobile-menu.open { transform: translateX(0); }
        body.menu-open { overflow: hidden; }

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
                    <a href="/departamentos" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
                        <i class="ph ph-buildings text-xl"></i>
                        <span class="font-medium text-sm">Gestión de Departamentos</span>
                    </a>
                    <?php endif; ?>

                    <?php if (in_array('Administrador', $userRoles)): ?>
                    <a href="/materias" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
                        <i class="ph ph-books text-xl"></i>
                        <span class="font-medium text-sm">Materias</span>
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
                    <a href="/departamentos" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300">
                        <i class="ph ph-buildings text-xl"></i>
                        <span class="font-medium text-sm">Gestión de Departamentos</span>
                    </a>
                    <a href="/materias" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300">
                        <i class="ph ph-books text-xl"></i>
                        <span class="font-medium text-sm">Materias</span>
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
                        <h2 class="text-2xl font-bold text-gray-900">Asignación Académica</h2>
                        <p class="text-sm text-gray-500 mt-1">Asigna materias a los docentes. Un docente puede impartir varias materias y una materia puede ser impartida por varios docentes.</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                        <?php
                            $filterQuery = [];
                            if (!empty($_GET['materia_filter'])) $filterQuery['materia_filter'] = $_GET['materia_filter'];
                            if (!empty($_GET['trayecto_filter'])) $filterQuery['trayecto_filter'] = $_GET['trayecto_filter'];
                            if (!empty($_GET['status_filter'])) $filterQuery['status_filter'] = $_GET['status_filter'];
                            if (!empty($_GET['search'])) $filterQuery['search'] = $_GET['search'];
                            $pdfUrl = '/personal/asignacion-academica/export-pdf' . (!empty($filterQuery) ? '?' . http_build_query($filterQuery) : '');
                        ?>
                        <a href="<?= $pdfUrl ?>"
                           class="bg-white border border-slate-300 text-slate-700 px-4 py-2.5 rounded-lg font-medium shadow-sm transition-all flex items-center justify-center gap-2 hover:bg-slate-50 w-full sm:w-auto">
                            <i class="ph ph-file-pdf text-lg text-red-500"></i>
                            Exportar PDF
                        </a>
                        <button onclick="openAssignModal()" class="bg-slate-900 hover:bg-black text-white px-5 py-2.5 rounded-lg font-medium shadow-sm transition-all flex items-center justify-center gap-2 transform hover:scale-105 w-full sm:w-auto">
                            <i class="ph ph-books text-lg"></i>
                            Asignar Materia
                        </button>
                    </div>
                </header>

                <div class="p-4 sm:p-6 md:p-8 flex-1">

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                        <div class="border-b border-gray-200 bg-gray-50/50 px-4">
                            <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
                                <a href="/personal" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                                    <i class="ph ph-users-three text-lg"></i>
                                    Directorio General
                                </a>
                                <a href="/personal/asignacion-academica" class="border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2" aria-current="page">
                                    <i class="ph ph-books text-lg"></i>
                                    Asignación Académica
                                </a>
                                <a href="/personal/permisos-roles" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                                    <i class="ph ph-shield-check text-lg"></i>
                                    Permisos y Roles
                                </a>
                            </nav>
                        </div>

                        <form method="GET" action="/personal/asignacion-academica" class="p-4 border-b border-gray-100 flex flex-col sm:flex-row gap-4 items-center justify-between">
                            <div class="relative w-full sm:max-w-xs">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="ph ph-magnifying-glass text-gray-400"></i>
                                </div>
                                <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors" placeholder="Buscar por cédula, docente, materia o código...">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full">

                                <div class="relative">
                                    <label class="absolute -top-2.5 left-3 px-1 bg-white text-[10px] font-bold text-uptval-black tracking-wider pointer-events-none z-10">
                                        Materia
                                    </label>
                                    <select name="materia_filter" onchange="this.form.submit()" class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-uptval-blue focus:border-uptval-blue bg-white transition-all">
                                        <option value="">Todas las Materias</option>
                                        <?php foreach ($materias as $materia): ?>
                                            <option value="<?php echo (int) $materia['id_materia']; ?>" <?php echo (($_GET['materia_filter'] ?? '') == $materia['id_materia']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($materia['codigo'] . ' - ' . $materia['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="relative">
                                    <label class="absolute -top-2.5 left-3 px-1 bg-white text-[10px] font-bold text-uptval-black tracking-wider pointer-events-none z-10">
                                        Trayecto
                                    </label>
                                    <select name="trayecto_filter" onchange="this.form.submit()" class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-uptval-blue focus:border-uptval-blue bg-white transition-all">
                                        <option value="">Todos los Trayectos</option>
                                        <?php foreach ($trayectos as $trayecto): ?>
                                            <option value="<?php echo (int) $trayecto['id_trayecto']; ?>" <?php echo (($_GET['trayecto_filter'] ?? '') == $trayecto['id_trayecto']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($trayecto['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="relative">
                                    <label class="absolute -top-2.5 left-3 px-1 bg-white text-[10px] font-bold text-uptval-black tracking-wider pointer-events-none z-10">
                                        Estatus
                                    </label>
                                    <select name="status_filter" onchange="this.form.submit()" class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-uptval-blue focus:border-uptval-blue bg-white transition-all">
                                        <option value="">Todos los estatus</option>
                                        <option value="activo" <?php echo (($_GET['status_filter'] ?? '') === 'activo') ? 'selected' : ''; ?>>Activo</option>
                                        <option value="inactivo" <?php echo (($_GET['status_filter'] ?? '') === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                                    </select>
                                </div>

                            </div>
                        </form>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <th scope="col" class="px-6 py-3 text-left">Cedula</th>
                                        <th scope="col" class="px-6 py-3 text-left">Docente</th>
                                        <th scope="col" class="px-6 py-3 text-left">Materia</th>
                                        <th scope="col" class="px-6 py-3 text-left">Trayecto</th>
                                        <th scope="col" class="px-6 py-3 text-center">Duración</th>
                                        <th scope="col" class="px-6 py-3 text-center">Estatus</th>
                                        <th scope="col" class="px-6 py-3 text-right">Fecha Asignación</th>
                                        <th scope="col" class="px-6 py-3 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($assignments) && !empty($_GET['materia_filter'] ?? $_GET['trayecto_filter'] ?? $_GET['status_filter'] ?? $_GET['search'] ?? '')): ?>
                                        <tr>
                                            <td colspan="8" class="py-10 text-center text-gray-500 bg-gray-50">
                                                <i class="ph ph-funnel text-5xl mb-3 block opacity-50"></i>
                                                No se encontraron resultados con los filtros aplicados.
                                            </td>
                                        </tr>
                                    <?php elseif (empty($assignments)): ?>
                                        <tr>
                                            <td colspan="8" class="py-10 text-center text-gray-500 bg-gray-50">
                                                <i class="ph ph-books text-5xl mb-3 block opacity-50"></i>
                                                No hay asignaciones de materias registradas. <br>
                                                Usa el botón "Asignar Materia" para comenzar.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($assignments as $assignment): ?>
                                            <?php
                                                $initials = strtoupper(
                                                    substr($assignment['first_name'] ?? '?', 0, 1) .
                                                    substr($assignment['last_name'] ?? '?', 0, 1)
                                                );
                                                $assignmentDate = !empty($assignment['assignment_date'])
                                                    ? date('d/m/Y', strtotime($assignment['assignment_date']))
                                                    : '---';
                                                $isActive = ($assignment['assignment_state'] ?? 'activo') === 'activo';
                                            ?>
                                            <tr data-id-staff-materia="<?php echo (int) $assignment['id_staff_materia']; ?>" class="hover:bg-gray-50 transition-colors <?php echo $isActive ? '' : 'opacity-60'; ?>">

                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900 font-mono">
                                                        <?php echo htmlspecialchars($assignment['cedula']); ?>
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
                                                                <?php echo htmlspecialchars(trim(($assignment['last_name'] ?? '') . ', ' . ($assignment['first_name'] ?? ''))); ?>
                                                            </div>
                                                            <div class="text-sm text-gray-500">
                                                                <?php echo htmlspecialchars($assignment['email'] ?? 'Sin correo'); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?php echo htmlspecialchars($assignment['materia_name']); ?>
                                                    </div>
                                                    <div class="text-sm text-gray-500 font-mono">
                                                        <?php echo htmlspecialchars($assignment['codigo']); ?>
                                                    </div>
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center gap-1.5">
                                                        <i class="ph ph-squares-four text-slate-400"></i>
                                                        <div class="text-sm text-gray-900">
                                                            <?php echo htmlspecialchars($assignment['trayecto_name']); ?>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                                        <?php echo htmlspecialchars($assignment['duracion']); ?>
                                                    </span>
                                                </td>

                                                <td data-status-badge class="px-6 py-4 whitespace-nowrap text-center">
                                                    <?php if ($isActive): ?>
                                                        <span class="px-2.5 py-1 inline-flex items-center gap-1 text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-700 border border-green-300">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                            Activo
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="px-2.5 py-1 inline-flex items-center gap-1 text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-500 border border-gray-300">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                                            Inactivo
                                                        </span>
                                                    <?php endif; ?>
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    <div class="text-sm text-gray-900">
                                                        <?php echo htmlspecialchars($assignmentDate); ?>
                                                    </div>
                                                </td>

                                                <td data-toggle-action class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <?php if ($isActive): ?>
                                                            <button type="button" onclick="confirmToggleAsignacion(this)"
                                                                    data-id-staff-materia="<?php echo (int) $assignment['id_staff_materia']; ?>"
                                                                    data-materia-name="<?php echo htmlspecialchars($assignment['materia_name']); ?>"
                                                                    data-cedula="<?php echo htmlspecialchars($assignment['cedula']); ?>"
                                                                    data-current-state="activo"
                                                                    class="text-slate-400 hover:text-uptval-orange transition-colors p-1" title="Desactivar asignación">
                                                                <i class="ph ph-user-minus text-xl"></i>
                                                            </button>
                                                        <?php else: ?>
                                                            <button type="button" onclick="confirmToggleAsignacion(this)"
                                                                    data-id-staff-materia="<?php echo (int) $assignment['id_staff_materia']; ?>"
                                                                    data-materia-name="<?php echo htmlspecialchars($assignment['materia_name']); ?>"
                                                                    data-cedula="<?php echo htmlspecialchars($assignment['cedula']); ?>"
                                                                    data-current-state="inactivo"
                                                                    class="text-green-500 hover:text-green-600 transition-colors p-1" title="Activar asignación">
                                                                <i class="ph ph-user-check text-xl"></i>
                                                            </button>
                                                        <?php endif; ?>

                                                        <button type="button" onclick="confirmEliminarAsignacion(this)"
                                                                data-id-staff-materia="<?php echo (int) $assignment['id_staff_materia']; ?>"
                                                                data-materia-name="<?php echo htmlspecialchars($assignment['materia_name']); ?>"
                                                                data-cedula="<?php echo htmlspecialchars($assignment['cedula']); ?>"
                                                                class="text-slate-400 hover:text-red-500 transition-colors p-1" title="Eliminar asignación">
                                                            <i class="ph ph-trash text-xl"></i>
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
                                if (!empty($_GET['materia_filter'])) $filterQuery['materia_filter'] = $_GET['materia_filter'];
                                if (!empty($_GET['trayecto_filter'])) $filterQuery['trayecto_filter'] = $_GET['trayecto_filter'];
                                if (!empty($_GET['status_filter'])) $filterQuery['status_filter'] = $_GET['status_filter'];
                                if (!empty($_GET['search'])) $filterQuery['search'] = $_GET['search'];
                                $filterQueryStr = !empty($filterQuery) ? '&' . http_build_query($filterQuery) : '';
                            ?>
                            <div class="flex items-center gap-6">
                                <?php if ($page > 1): ?>
                                    <a href="/personal/asignacion-academica?page=<?= $page - 1 ?><?= $filterQueryStr ?>" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
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
                                    <a href="/personal/asignacion-academica?page=<?= $page + 1 ?><?= $filterQueryStr ?>" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
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
                    </div>
            </main>
        </div>

    <div id="modalAsignarMateria" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[100] transition-opacity p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto transform scale-95 transition-transform modal-content">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 text-gray-800">
                <div>
                    <h3 class="text-lg font-bold">Asignar Materia</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Un docente puede impartir varias materias y una materia puede ser impartida por varios docentes.</p>
                </div>
                <button onclick="toggleModal('modalAsignarMateria')" class="text-gray-400 hover:text-red-500 transition-colors"><i class="ph ph-x text-xl"></i></button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label for="search_cedula" class="block text-sm font-medium text-gray-700 mb-1">Cédula del docente</label>
                        <div class="flex gap-2">
                            <input type="text" id="search_cedula" name="search_cedula" autocomplete="off"
                                   placeholder="Ej: 22224341"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white text-gray-900"
                                   onkeydown="if (event.key === 'Enter') { event.preventDefault(); searchByCedula(); }">
                            <button type="button" id="btnSearchStaff" onclick="searchByCedula()"
                                    class="px-4 py-2 bg-slate-900 hover:bg-black text-white font-medium rounded-lg shadow-md flex items-center gap-2 transition-all">
                                <i class="ph ph-magnifying-glass"></i>
                                <span>Buscar</span>
                            </button>
                        </div>
                    </div>
                    <div id="assignResult"></div>
                </div>
                <div class="mt-6 pt-4 border-t border-gray-200 bg-gray-50 -mx-6 -mb-6 px-6 py-4 flex flex-col sm:flex-row justify-end gap-3 rounded-b-2xl">
                    <button type="button" onclick="toggleModal('modalAsignarMateria')" class="px-4 py-2 text-gray-600 font-medium hover:bg-gray-200 rounded-lg w-full sm:w-auto">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div id="modalConfirmEliminarAsignacion" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[100] transition-opacity p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform scale-95 transition-transform modal-content overflow-hidden">
            <div class="px-6 pt-8 pb-6 text-center">
                <div class="mx-auto w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mb-4">
                    <i class="ph ph-trash text-3xl text-red-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">¿Eliminar la asignación?</h3>
                <p class="mt-2 text-sm text-gray-500">
                    Se eliminará la asignación de
                    <span class="font-semibold text-uptval-dark" id="confirmEliminarMateriaName"></span>
                    al docente
                    <span class="font-semibold text-gray-900" id="confirmEliminarStaffName"></span>.
                    Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="toggleModal('modalConfirmEliminarAsignacion')" class="px-4 py-2 text-gray-600 font-medium hover:bg-gray-200 rounded-lg w-full sm:w-auto">Cancelar</button>
                <button type="button" id="confirmEliminarAsignacionBtn" onclick="confirmEliminarAsignacionAction()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg shadow-md flex items-center justify-center gap-2 w-full sm:w-auto transition-colors">
                    <i class="ph ph-trash text-lg"></i>
                    Sí, eliminar
                </button>
            </div>
        </div>
    </div>

    <div id="modalConfirmToggleAsignacion" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[100] transition-opacity p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform scale-95 transition-transform modal-content overflow-hidden">
            <div class="px-6 pt-8 pb-6 text-center">
                <div class="mx-auto w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center mb-4">
                    <i class="ph ph-pause text-3xl text-amber-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">¿Desactivar la asignación?</h3>
                <p class="mt-2 text-sm text-gray-500">
                    Se desactivará la asignación de
                    <span class="font-semibold text-uptval-dark" id="confirmToggleMateriaName"></span>
                    al docente
                    <span class="font-semibold text-gray-900" id="confirmToggleStaffName"></span>.
                    Podrá reactivarla en cualquier momento.
                </p>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="toggleModal('modalConfirmToggleAsignacion')" class="px-4 py-2 text-gray-600 font-medium hover:bg-gray-200 rounded-lg w-full sm:w-auto">Cancelar</button>
                <button type="button" id="confirmToggleAsignacionBtn" onclick="confirmToggleAsignacionAction()" class="px-4 py-2 bg-uptval-orange hover:bg-uptval-dark text-white font-medium rounded-lg shadow-md flex items-center justify-center gap-2 w-full sm:w-auto transition-colors">
                    <i class="ph ph-user-minus text-lg"></i>
                    Sí, desactivar
                </button>
            </div>
        </div>
    </div>

    <script>
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

        function esc(value) {
            return String(value == null ? '' : value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function loadingHtml() {
            return '<div class="flex items-center justify-center gap-2 text-sm text-gray-500 py-4"><i class="ph ph-circle-notch ph-spin text-lg"></i>Buscando docente...</div>';
        }

        function alertHtml(type, message) {
            const isError = type === 'error';
            const icon = isError ? 'ph-warning-circle' : 'ph-info';
            const classes = isError ? 'border-red-200 bg-red-50 text-red-700' : 'border-blue-200 bg-blue-50 text-blue-700';
            return '<div class="flex items-start gap-2 rounded-lg border px-4 py-3 text-sm ' + classes + '">'
                + '<i class="ph ' + icon + ' text-lg mt-0.5"></i>'
                + '<span>' + esc(message) + '</span></div>';
        }

        function openAssignModal() {
            document.getElementById('search_cedula').value = '';
            document.getElementById('assignResult').innerHTML = '';
            toggleModal('modalAsignarMateria');
            setTimeout(() => document.getElementById('search_cedula').focus(), 100);
        }

        function searchByCedula() {
            const input = document.getElementById('search_cedula');
            const result = document.getElementById('assignResult');
            const btn = document.getElementById('btnSearchStaff');
            const cedula = input.value.trim();

            if (!cedula) {
                result.innerHTML = alertHtml('error', 'Ingrese una cédula para realizar la búsqueda.');
                input.focus();
                return;
            }

            btn.disabled = true;
            result.innerHTML = loadingHtml();

            fetch('/personal/asignacion-academica/search-by-cedula?cedula=' + encodeURIComponent(cedula))
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (!data.ok) {
                        result.innerHTML = alertHtml('error', data.message || 'No se pudo completar la búsqueda.');
                        return;
                    }
                    result.innerHTML = renderStaffResult(data);
                })
                .catch(function() {
                    result.innerHTML = alertHtml('error', 'Ocurrió un error al buscar el docente. Intente nuevamente.');
                })
                .finally(function() { btn.disabled = false; });
        }

        function renderStaffResult(data) {
            const s = data.staff;
            const name = (s.last_name && s.first_name) ? (s.last_name + ', ' + s.first_name) : s.cedula;
            const statusBadge = s.status === 'activo'
                ? '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Activo</span>'
                : '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Pendiente</span>';

            const card = '<div class="rounded-xl border border-gray-200 p-4 bg-gray-50">'
                + '<div class="flex items-start justify-between gap-3">'
                + '<div>'
                + '<p class="text-base font-bold text-gray-900">' + esc(name) + '</p>'
                + '<p class="text-sm text-gray-600 mt-0.5">C.I: ' + esc(s.cedula) + '</p>'
                + '<p class="text-sm text-gray-600">' + esc(s.department_name) + '</p>'
                + '</div>'
                + statusBadge
                + '</div></div>';

            const currentMateriaIds = data.materia_ids || [];

            const materiasChips = (data.assignments || []).map(function(a) {
                return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-uptval-orange/10 text-uptval-dark border border-uptval-orange/30">'
                    + '<i class="ph ph-books"></i>'
                    + esc(a.codigo) + ' · ' + esc(a.name)
                    + '<button type="button" title="Quitar materia" data-id-staff-materia="' + a.id_staff_materia + '" data-materia-name="' + esc(a.name) + '" data-staff-name="' + esc(name) + '" onclick="removeAsignacion(this)" class="p-0.5 rounded-full hover:bg-uptval-orange/20 text-gray-500 hover:text-red-600 transition-colors ml-0.5">'
                    + '<i class="ph ph-x text-sm"></i>'
                    + '</button>'
                    + '</span>';
            }).join('');

            const availableMaterias = data.available_materias || [];
            const available = availableMaterias.filter(function(m) {
                return currentMateriaIds.indexOf(m.id_materia) === -1;
            });

            let html = card;

            if (data.has_assignment) {
                html += '<div class="mt-3">'
                    + '<p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Materias asignadas</p>'
                    + '<div class="flex flex-wrap gap-2">' + materiasChips + '</div>'
                    + '</div>';
            }

            if (available.length > 0) {
                const groups = {};
                available.forEach(function(m) {
                    const key = m.trayecto_name || 'Sin trayecto';
                    if (!groups[key]) groups[key] = [];
                    groups[key].push(m);
                });

                const options = Object.keys(groups).map(function(key) {
                    const opts = groups[key].map(function(m) {
                        return '<option value="' + m.id_materia + '">' + esc(m.codigo) + ' - ' + esc(m.name) + '</option>';
                    }).join('');
                    return '<optgroup label="' + esc(key) + '">' + opts + '</optgroup>';
                }).join('');

                html += '<form method="POST" action="/personal/asignacion-academica/store" class="mt-4">'
                    + '<input type="hidden" name="id_staff" value="' + s.id_staff + '">'
                    + '<div>'
                    + '<label for="assign_materia" class="block text-sm font-medium text-gray-700 mb-1">Materia</label>'
                    + '<select name="id_materia" id="assign_materia" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white text-gray-900">'
                    + '<option value="">Seleccione una materia...</option>'
                    + options
                    + '</select>'
                    + '</div>'
                    + '<button type="submit" class="mt-4 w-full px-4 py-2 bg-slate-900 hover:bg-black text-white font-medium rounded-lg shadow-md transition-all">Asignar Materia</button>'
                    + '</form>';
            } else {
                html += alertHtml('info', data.has_assignment
                    ? 'Este docente ya tiene todas las materias activas asignadas.'
                    : 'No hay materias activas disponibles para asignar.');
            }

            return html;
        }

        let pendingEliminar = null;

        function confirmEliminarAsignacion(btn) {
            const idStaffMateria = btn.getAttribute('data-id-staff-materia');
            const materiaName = btn.getAttribute('data-materia-name');
            const cedula = btn.getAttribute('data-cedula');

            if (!idStaffMateria) return;

            document.getElementById('confirmEliminarMateriaName').innerText = materiaName;
            document.getElementById('confirmEliminarStaffName').innerText = 'C.I ' + cedula;
            pendingEliminar = { idStaffMateria: idStaffMateria };

            toggleModal('modalConfirmEliminarAsignacion');
        }

        function confirmEliminarAsignacionAction() {
            if (!pendingEliminar) return;

            const idStaffMateria = pendingEliminar.idStaffMateria;
            pendingEliminar = null;

            toggleModal('modalConfirmEliminarAsignacion');
            performEliminar(idStaffMateria);
        }

        function performEliminar(idStaffMateria) {
            const formData = new FormData();
            formData.append('id_staff_materia', idStaffMateria);

            fetch('/personal/asignacion-academica/delete', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.ok) {
                        showToast('success', data.title || 'Asignación eliminada', data.message || 'La asignación de la materia fue eliminada correctamente.');
                        removeRowFromTable(idStaffMateria);
                        const modal = document.getElementById('modalAsignarMateria');
                        if (modal && !modal.classList.contains('hidden')) {
                            searchByCedula();
                        }
                    } else {
                        showToast('error', data.title || 'No se pudo eliminar', data.message || 'Ocurrió un error al eliminar la asignación.');
                    }
                })
                .catch(function() {
                    showToast('error', 'Error al eliminar', 'Ocurrió un error inesperado. Intente nuevamente.');
                });
        }

        function removeAsignacion(btn) {
            const idStaffMateria = btn.getAttribute('data-id-staff-materia');
            const materiaName = btn.getAttribute('data-materia-name');
            const staffName = btn.getAttribute('data-staff-name');

            if (!idStaffMateria) return;

            document.getElementById('confirmEliminarMateriaName').innerText = materiaName;
            document.getElementById('confirmEliminarStaffName').innerText = staffName;
            pendingEliminar = { idStaffMateria: idStaffMateria };

            toggleModal('modalConfirmEliminarAsignacion');
        }

        function statusBadgeHtml(state) {
            if (state === 'activo') {
                return '<span class="px-2.5 py-1 inline-flex items-center gap-1 text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-700 border border-green-300">'
                    + '<span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Activo</span>';
            }
            return '<span class="px-2.5 py-1 inline-flex items-center gap-1 text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-500 border border-gray-300">'
                + '<span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactivo</span>';
        }

        let pendingToggle = null;

        function confirmToggleAsignacion(btn) {
            const idStaffMateria = btn.getAttribute('data-id-staff-materia');
            const currentState = btn.getAttribute('data-current-state');

            if (!idStaffMateria) return;

            if (currentState === 'inactivo') {
                performToggleAsignacion(idStaffMateria, 'activo', btn);
                return;
            }

            document.getElementById('confirmToggleMateriaName').innerText = btn.getAttribute('data-materia-name');
            document.getElementById('confirmToggleStaffName').innerText = 'C.I ' + btn.getAttribute('data-cedula');
            pendingToggle = { idStaffMateria: idStaffMateria, btn: btn };

            toggleModal('modalConfirmToggleAsignacion');
        }

        function confirmToggleAsignacionAction() {
            if (!pendingToggle) return;

            const idStaffMateria = pendingToggle.idStaffMateria;
            const btn = pendingToggle.btn;
            pendingToggle = null;

            toggleModal('modalConfirmToggleAsignacion');
            performToggleAsignacion(idStaffMateria, 'inactivo', btn);
        }

        function performToggleAsignacion(idStaffMateria, newState, btn) {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');

            const formData = new FormData();
            formData.append('id_staff_materia', idStaffMateria);

            fetch('/personal/asignacion-academica/toggle-status', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.ok) {
                        showToast('success', data.title || 'Asignación actualizada', data.message || 'El estatus de la asignación fue actualizado correctamente.');
                        updateRowAfterToggle(idStaffMateria, newState);
                    } else {
                        btn.disabled = false;
                        btn.classList.remove('opacity-50', 'cursor-not-allowed');
                        showToast('error', data.title || 'No se pudo actualizar', data.message || 'Ocurrió un error al actualizar el estatus.');
                    }
                })
                .catch(function() {
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    showToast('error', 'Error al actualizar', 'Ocurrió un error inesperado. Intente nuevamente.');
                });
        }

        function updateRowAfterToggle(idStaffMateria, newState) {
            const row = document.querySelector('tr[data-id-staff-materia="' + idStaffMateria + '"]');
            if (!row) {
                location.reload();
                return;
            }

            row.classList.toggle('opacity-60', newState !== 'activo');

            const badgeCell = row.querySelector('[data-status-badge]');
            if (badgeCell) badgeCell.innerHTML = statusBadgeHtml(newState);

            const actionCell = row.querySelector('[data-toggle-action]');
            if (actionCell) {
                const currentBtn = row.querySelector('button[data-id-staff-materia="' + idStaffMateria + '"]');
                const materiaName = currentBtn ? currentBtn.getAttribute('data-materia-name') : '';
                const cedula = currentBtn ? currentBtn.getAttribute('data-cedula') : '';

                let html;
                if (newState === 'activo') {
                    html = '<button type="button" onclick="confirmToggleAsignacion(this)"'
                        + ' data-id-staff-materia="' + idStaffMateria + '"'
                        + ' data-materia-name="' + esc(materiaName) + '"'
                        + ' data-cedula="' + esc(cedula) + '"'
                        + ' data-current-state="activo"'
                        + ' class="text-slate-400 hover:text-uptval-orange transition-colors p-1" title="Desactivar asignación">'
                        + '<i class="ph ph-user-minus text-xl"></i></button>';
                } else {
                    html = '<button type="button" onclick="confirmToggleAsignacion(this)"'
                        + ' data-id-staff-materia="' + idStaffMateria + '"'
                        + ' data-materia-name="' + esc(materiaName) + '"'
                        + ' data-cedula="' + esc(cedula) + '"'
                        + ' data-current-state="inactivo"'
                        + ' class="text-green-500 hover:text-green-600 transition-colors p-1" title="Activar asignación">'
                        + '<i class="ph ph-user-check text-xl"></i></button>';
                }

                html += '<button type="button" onclick="confirmEliminarAsignacion(this)"'
                    + ' data-id-staff-materia="' + idStaffMateria + '"'
                    + ' data-materia-name="' + esc(materiaName) + '"'
                    + ' data-cedula="' + esc(cedula) + '"'
                    + ' class="text-slate-400 hover:text-red-500 transition-colors p-1" title="Eliminar asignación">'
                    + '<i class="ph ph-trash text-xl"></i></button>';

                actionCell.innerHTML = html;
            }
        }

        function removeRowFromTable(idStaffMateria) {
            const rows = document.querySelectorAll('tr[data-id-staff-materia="' + idStaffMateria + '"]');
            rows.forEach(function(row) { row.remove(); });

            const tbody = document.querySelector('table tbody');
            const remaining = tbody ? tbody.querySelectorAll('tr[data-id-staff-materia]').length : 0;
            if (remaining === 0) {
                location.reload();
            }
        }

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
