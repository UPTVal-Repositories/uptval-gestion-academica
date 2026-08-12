<?php
/**
 * @var string $cedula
 * @var string $last_connection
 * @var int $page
 * @var int $totalPages
 * @var string $activeTab
 * @var array $materias
 * @var array $trayectos
 * @var array $especialidades
 * @var array $duraciones
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
    <title>Materias y Trayectos - UPTVal</title>
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
                <a href="/materias" class="flex items-center gap-3 px-4 py-3 text-uptval-orange bg-uptval-orange/10 rounded-xl transition-all duration-300 border-l-2 border-uptval-orange shadow-[inset_0px_0px_20px_rgba(217,123,41,0.05)]">
                    <i class="ph ph-books text-xl"></i>
                    <span class="font-medium text-sm">Materias</span>
                </a>
                <?php endif; ?>

                <?php if (in_array('Administrador', $userRoles)): ?>
                <a href="/personal" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
                    <i class="ph ph-users text-xl"></i>
                    <span class="font-medium text-sm">Gestión de Personal</span>
                </a>
                <?php endif; ?>

                <?php if (in_array('Administrador', $userRoles)): ?>
                <a href="/aulas" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
                    <i class="ph ph-door text-xl"></i>
                    <span class="font-medium text-sm">Aulas y Laboratorios</span>
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
                    <span class="font-medium text-sm">Configuracion</span>
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
                <a href="/materias" class="flex items-center gap-3 px-4 py-3 text-uptval-orange bg-uptval-orange/10 rounded-xl transition-all duration-300 border-l-2 border-uptval-orange">
                    <i class="ph ph-books text-xl"></i>
                    <span class="font-medium text-sm">Materias</span>
                </a>
                <a href="/personal" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300">
                    <i class="ph ph-users text-xl"></i>
                    <span class="font-medium text-sm">Gestion de Personal</span>
                </a>
                <a href="/aulas" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300">
                    <i class="ph ph-door text-xl"></i>
                    <span class="font-medium text-sm">Aulas y Laboratorios</span>
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
                        <span class="font-medium text-sm">Configuracion</span>
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
                    <h2 class="text-2xl font-bold text-gray-900">Materias, Trayectos y Especialidades</h2>
                    <p class="text-sm text-gray-500 mt-1">Administra las materias academicas, los trayectos del PNF y las especialidades.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <?php if ($activeTab === 'materias'): ?>
                    <button onclick="toggleModal('modalRegistrarMateria')" class="bg-slate-900 hover:bg-black text-white px-5 py-2.5 rounded-lg font-medium shadow-sm transition-all flex items-center justify-center gap-2 transform hover:scale-105 w-full sm:w-auto">
                        <i class="ph ph-plus-circle text-lg"></i>
                        Registrar Materia
                    </button>
                    <?php elseif ($activeTab === 'trayectos'): ?>
                    <button onclick="toggleModal('modalRegistrarTrayecto')" class="bg-slate-900 hover:bg-black text-white px-5 py-2.5 rounded-lg font-medium shadow-sm transition-all flex items-center justify-center gap-2 transform hover:scale-105 w-full sm:w-auto">
                        <i class="ph ph-plus-circle text-lg"></i>
                        Registrar Trayecto
                    </button>
                    <?php else: ?>
                    <button onclick="toggleModal('modalRegistrarEspecialidad')" class="bg-slate-900 hover:bg-black text-white px-5 py-2.5 rounded-lg font-medium shadow-sm transition-all flex items-center justify-center gap-2 transform hover:scale-105 w-full sm:w-auto">
                        <i class="ph ph-plus-circle text-lg"></i>
                        Registrar Especialidad
                    </button>
                    <?php endif; ?>
                </div>
            </header>

            <div class="p-4 sm:p-6 md:p-8 flex-1">

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                    <div class="border-b border-gray-200 bg-gray-50/50 px-4">
                        <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
                            <a href="/materias" class="<?php echo $activeTab === 'materias' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                                <i class="ph ph-notebook text-lg"></i>
                                Materias
                            </a>
                            <a href="/materias/trayectos" class="<?php echo $activeTab === 'trayectos' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                                <i class="ph ph-squares-four text-lg"></i>
                                Trayectos
                            </a>
                            <a href="/materias/especialidades" class="<?php echo $activeTab === 'especialidades' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                                <i class="ph ph-graduation-cap text-lg"></i>
                                Especialidades
                            </a>
                        </nav>
                    </div>

                    <?php if ($activeTab === 'materias'): ?>

                    <form method="GET" action="/materias" class="p-4 border-b border-gray-100 flex flex-col sm:flex-row gap-4 items-center justify-between">
                        <div class="relative w-full sm:max-w-xs">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="ph ph-magnifying-glass text-gray-400"></i>
                            </div>
                            <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors" placeholder="Buscar por código o nombre...">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 w-full">

                            <div class="relative">
                                <label class="absolute -top-2.5 left-3 px-1 bg-white text-[10px] font-bold text-uptval-black tracking-wider pointer-events-none z-10">
                                    Trayecto
                                </label>
                                <select name="trayecto_filter" onchange="this.form.submit()" class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-uptval-blue focus:border-uptval-blue bg-white transition-all">
                                    <option value="">Todos los Trayectos</option>
                                    <?php foreach ($trayectos as $trayecto): ?>
                                        <option value="<?php echo htmlspecialchars($trayecto['id_trayecto']); ?>" <?php echo (($_GET['trayecto_filter'] ?? '') == $trayecto['id_trayecto']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($trayecto['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="relative">
                                <label class="absolute -top-2.5 left-3 px-1 bg-white text-[10px] font-bold text-uptval-black tracking-wider pointer-events-none z-10">
                                    Especialidad
                                </label>
                                <select name="especialidad_filter" onchange="this.form.submit()" class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-uptval-blue focus:border-uptval-blue bg-white transition-all">
                                    <option value="">Todas las Especialidades</option>
                                    <?php foreach ($especialidades as $esp): ?>
                                        <option value="<?php echo htmlspecialchars($esp['id_especialidad']); ?>" <?php echo (($_GET['especialidad_filter'] ?? '') == $esp['id_especialidad']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($esp['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="relative">
                                <label class="absolute -top-2.5 left-3 px-1 bg-white text-[10px] font-bold text-uptval-black tracking-wider pointer-events-none z-10">
                                    Duración
                                </label>
                                <select name="duracion_filter" onchange="this.form.submit()" class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-uptval-blue focus:border-uptval-blue bg-white transition-all">
                                    <option value="">Todas las Duraciones</option>
                                    <?php foreach ($duraciones as $duracion): ?>
                                        <option value="<?php echo htmlspecialchars($duracion); ?>" <?php echo (($_GET['duracion_filter'] ?? '') === $duracion) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($duracion); ?>
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
                                    <th scope="col" class="px-6 py-3 text-left">Codigo</th>
                                    <th scope="col" class="px-6 py-3 text-left">Materia</th>
                                    <th scope="col" class="px-6 py-3 text-left">Trayecto</th>
                                    <th scope="col" class="px-6 py-3 text-left">Especialidad</th>
                                    <th scope="col" class="px-6 py-3 text-left">Duracion</th>
                                    <th scope="col" class="px-6 py-3 text-center">Estatus</th>
                                    <th scope="col" class="px-6 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($materias) && !empty($_GET['trayecto_filter'] ?? $_GET['especialidad_filter'] ?? $_GET['duracion_filter'] ?? $_GET['status_filter'] ?? $_GET['search'] ?? '')): ?>
                                    <tr>
                                        <td colspan="7" class="py-10 text-center text-gray-500 bg-gray-50">
                                            <i class="ph ph-funnel text-5xl mb-3 block opacity-50"></i>
                                            No se encontraron resultados con los filtros aplicados.
                                        </td>
                                    </tr>
                                <?php elseif (empty($materias)): ?>
                                    <tr>
                                        <td colspan="7" class="py-10 text-center text-gray-500 bg-gray-50">
                                            <i class="ph ph-books text-5xl mb-3 block opacity-50"></i>
                                            No hay materias registradas en el sistema.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($materias as $materia): ?>
                                        <?php $materiaActive = ($materia['status'] ?? 'activo') === 'activo'; ?>
                                        <tr class="hover:bg-gray-50 transition-colors <?php echo $materiaActive ? '' : 'opacity-60'; ?>">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 font-mono font-semibold">
                                                    <?php echo htmlspecialchars($materia['codigo']); ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($materia['name']); ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-1.5">
                                                    <i class="ph ph-squares-four text-slate-400"></i>
                                                    <div class="text-sm text-gray-900">
                                                        <?php echo htmlspecialchars($materia['trayecto_name'] ?? 'Sin asignar'); ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-1.5">
                                                    <i class="ph ph-graduation-cap text-slate-400"></i>
                                                    <div class="text-sm text-gray-900">
                                                        <?php echo htmlspecialchars($materia['especialidad_name'] ?? 'Sin asignar'); ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                                    <?php echo htmlspecialchars($materia['duracion']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <?php if ($materiaActive): ?>
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
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button type="button"
                                                            onclick="openEditMateria(this)"
                                                            data-id="<?php echo (int) $materia['id_materia']; ?>"
                                                            data-codigo="<?php echo htmlspecialchars($materia['codigo']); ?>"
                                                            data-name="<?php echo htmlspecialchars($materia['name']); ?>"
                                                            data-duracion="<?php echo htmlspecialchars($materia['duracion']); ?>"
                                                            data-trayecto="<?php echo (int) $materia['id_trayecto']; ?>"
                                                            data-especialidad="<?php echo (int) ($materia['id_especialidad'] ?? 0); ?>"
                                                            class="text-slate-400 hover:text-blue-500 transition-colors p-1" title="Editar materia">
                                                        <i class="ph ph-pencil text-xl"></i>
                                                    </button>

                                                    <?php if ($materiaActive): ?>
                                                    <form method="POST" action="/materias/toggle-status" class="m-0">
                                                        <input type="hidden" name="id_materia" value="<?php echo (int) $materia['id_materia']; ?>">
                                                        <button type="button"
                                                                onclick="confirmAccion('Desactivar materia', '¿Desea desactivar la materia \"<?php echo htmlspecialchars($materia['name']); ?>\"? Podrá reactivarla en cualquier momento.', 'Sí, desactivar', 'warn', this.closest('form'))"
                                                                class="text-slate-400 hover:text-uptval-orange transition-colors p-1" title="Desactivar materia">
                                                            <i class="ph ph-user-minus text-xl"></i>
                                                        </button>
                                                    </form>
                                                    <?php else: ?>
                                                    <form method="POST" action="/materias/toggle-status" class="m-0">
                                                        <input type="hidden" name="id_materia" value="<?php echo (int) $materia['id_materia']; ?>">
                                                        <button type="submit" class="text-green-500 hover:text-green-600 transition-colors p-1" title="Activar materia">
                                                            <i class="ph ph-user-check text-xl"></i>
                                                        </button>
                                                    </form>
                                                    <?php endif; ?>

                                                    <form method="POST" action="/materias/delete" class="m-0">
                                                        <input type="hidden" name="id_materia" value="<?php echo (int) $materia['id_materia']; ?>">
                                                        <button type="button"
                                                                onclick="confirmAccion('Eliminar materia', '¿Está seguro de eliminar la materia \"<?php echo htmlspecialchars($materia['name']); ?>\"? Esta acción no se puede deshacer.', 'Sí, eliminar', 'danger', this.closest('form'))"
                                                                class="text-slate-400 hover:text-red-500 transition-colors p-1" title="Eliminar materia">
                                                            <i class="ph ph-trash text-xl"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php
                        $filterQuery = [];
                        if (!empty($_GET['trayecto_filter'])) $filterQuery['trayecto_filter'] = $_GET['trayecto_filter'];
                        if (!empty($_GET['especialidad_filter'])) $filterQuery['especialidad_filter'] = $_GET['especialidad_filter'];
                        if (!empty($_GET['duracion_filter'])) $filterQuery['duracion_filter'] = $_GET['duracion_filter'];
                        if (!empty($_GET['status_filter'])) $filterQuery['status_filter'] = $_GET['status_filter'];
                        if (!empty($_GET['search'])) $filterQuery['search'] = $_GET['search'];
                        $filterQueryStr = !empty($filterQuery) ? '&' . http_build_query($filterQuery) : '';
                    ?>

                    <?php elseif ($activeTab === 'trayectos'): ?>

                    <form method="GET" action="/materias/trayectos" class="p-4 border-b border-gray-100 flex flex-col sm:flex-row gap-4 items-center justify-between">
                        <div class="relative w-full sm:max-w-xs">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="ph ph-magnifying-glass text-gray-400"></i>
                            </div>
                            <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors" placeholder="Buscar por nombre del trayecto...">
                        </div>

                        <div class="relative w-full sm:w-56">
                            <label class="absolute -top-2.5 left-3 px-1 bg-white text-[10px] font-bold text-uptval-black tracking-wider pointer-events-none z-10">
                                Estatus
                            </label>
                            <select name="status_filter" onchange="this.form.submit()" class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-uptval-blue focus:border-uptval-blue bg-white transition-all">
                                <option value="">Todos los estatus</option>
                                <option value="activo" <?php echo (($_GET['status_filter'] ?? '') === 'activo') ? 'selected' : ''; ?>>Activo</option>
                                <option value="inactivo" <?php echo (($_GET['status_filter'] ?? '') === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <th scope="col" class="px-6 py-3 text-left">Trayecto</th>
                                    <th scope="col" class="px-6 py-3 text-center">Estatus</th>
                                    <th scope="col" class="px-6 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($trayectos) && !empty($_GET['status_filter'] ?? $_GET['search'] ?? '')): ?>
                                    <tr>
                                        <td colspan="3" class="py-10 text-center text-gray-500 bg-gray-50">
                                            <i class="ph ph-funnel text-5xl mb-3 block opacity-50"></i>
                                            No se encontraron resultados con los filtros aplicados.
                                        </td>
                                    </tr>
                                <?php elseif (empty($trayectos)): ?>
                                    <tr>
                                        <td colspan="3" class="py-10 text-center text-gray-500 bg-gray-50">
                                            <i class="ph ph-squares-four text-5xl mb-3 block opacity-50"></i>
                                            No hay trayectos registrados en el sistema.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($trayectos as $trayecto): ?>
                                        <?php $trayectoActive = ($trayecto['status'] ?? 'activo') === 'activo'; ?>
                                        <tr class="hover:bg-gray-50 transition-colors <?php echo $trayectoActive ? '' : 'opacity-60'; ?>">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($trayecto['name']); ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <?php if ($trayectoActive): ?>
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
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button type="button"
                                                            onclick="openEditTrayecto(this)"
                                                            data-id="<?php echo (int) $trayecto['id_trayecto']; ?>"
                                                            data-name="<?php echo htmlspecialchars($trayecto['name']); ?>"
                                                            class="text-slate-400 hover:text-blue-500 transition-colors p-1" title="Editar trayecto">
                                                        <i class="ph ph-pencil text-xl"></i>
                                                    </button>

                                                    <?php if ($trayectoActive): ?>
                                                    <form method="POST" action="/materias/trayectos/toggle-status" class="m-0">
                                                        <input type="hidden" name="id_trayecto" value="<?php echo (int) $trayecto['id_trayecto']; ?>">
                                                        <button type="button"
                                                                onclick="confirmAccion('Desactivar trayecto', '¿Desea desactivar el trayecto \"<?php echo htmlspecialchars($trayecto['name']); ?>\"? Podrá reactivarlo en cualquier momento.', 'Sí, desactivar', 'warn', this.closest('form'))"
                                                                class="text-slate-400 hover:text-uptval-orange transition-colors p-1" title="Desactivar trayecto">
                                                            <i class="ph ph-user-minus text-xl"></i>
                                                        </button>
                                                    </form>
                                                    <?php else: ?>
                                                    <form method="POST" action="/materias/trayectos/toggle-status" class="m-0">
                                                        <input type="hidden" name="id_trayecto" value="<?php echo (int) $trayecto['id_trayecto']; ?>">
                                                        <button type="submit" class="text-green-500 hover:text-green-600 transition-colors p-1" title="Activar trayecto">
                                                            <i class="ph ph-user-check text-xl"></i>
                                                        </button>
                                                    </form>
                                                    <?php endif; ?>

                                                    <form method="POST" action="/materias/trayectos/delete" class="m-0">
                                                        <input type="hidden" name="id_trayecto" value="<?php echo (int) $trayecto['id_trayecto']; ?>">
                                                        <button type="button"
                                                                onclick="confirmAccion('Eliminar trayecto', '¿Está seguro de eliminar el trayecto \"<?php echo htmlspecialchars($trayecto['name']); ?>\"? Esta acción no se puede deshacer.', 'Sí, eliminar', 'danger', this.closest('form'))"
                                                                class="text-slate-400 hover:text-red-500 transition-colors p-1" title="Eliminar trayecto">
                                                            <i class="ph ph-trash text-xl"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php
                        $filterQuery = [];
                        if (!empty($_GET['status_filter'])) $filterQuery['status_filter'] = $_GET['status_filter'];
                        if (!empty($_GET['search'])) $filterQuery['search'] = $_GET['search'];
                        $filterQueryStr = !empty($filterQuery) ? '&' . http_build_query($filterQuery) : '';
                    ?>

                    <?php else: ?>

                    <form method="GET" action="/materias/especialidades" class="p-4 border-b border-gray-100 flex flex-col sm:flex-row gap-4 items-center justify-between">
                        <div class="relative w-full sm:max-w-xs">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="ph ph-magnifying-glass text-gray-400"></i>
                            </div>
                            <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors" placeholder="Buscar por nombre de la especialidad...">
                        </div>

                        <div class="relative w-full sm:w-56">
                            <label class="absolute -top-2.5 left-3 px-1 bg-white text-[10px] font-bold text-uptval-black tracking-wider pointer-events-none z-10">
                                Estatus
                            </label>
                            <select name="status_filter" onchange="this.form.submit()" class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-uptval-blue focus:border-uptval-blue bg-white transition-all">
                                <option value="">Todos los estatus</option>
                                <option value="activo" <?php echo (($_GET['status_filter'] ?? '') === 'activo') ? 'selected' : ''; ?>>Activo</option>
                                <option value="inactivo" <?php echo (($_GET['status_filter'] ?? '') === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <th scope="col" class="px-6 py-3 text-left">Especialidad</th>
                                    <th scope="col" class="px-6 py-3 text-center">Estatus</th>
                                    <th scope="col" class="px-6 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($especialidades) && !empty($_GET['status_filter'] ?? $_GET['search'] ?? '')): ?>
                                    <tr>
                                        <td colspan="3" class="py-10 text-center text-gray-500 bg-gray-50">
                                            <i class="ph ph-funnel text-5xl mb-3 block opacity-50"></i>
                                            No se encontraron resultados con los filtros aplicados.
                                        </td>
                                    </tr>
                                <?php elseif (empty($especialidades)): ?>
                                    <tr>
                                        <td colspan="3" class="py-10 text-center text-gray-500 bg-gray-50">
                                            <i class="ph ph-graduation-cap text-5xl mb-3 block opacity-50"></i>
                                            No hay especialidades registradas en el sistema.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($especialidades as $especialidad): ?>
                                        <?php $espActive = ($especialidad['status'] ?? 'activo') === 'activo'; ?>
                                        <tr class="hover:bg-gray-50 transition-colors <?php echo $espActive ? '' : 'opacity-60'; ?>">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($especialidad['name']); ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <?php if ($espActive): ?>
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
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button type="button"
                                                            onclick="openEditEspecialidad(this)"
                                                            data-id="<?php echo (int) $especialidad['id_especialidad']; ?>"
                                                            data-name="<?php echo htmlspecialchars($especialidad['name']); ?>"
                                                            class="text-slate-400 hover:text-blue-500 transition-colors p-1" title="Editar especialidad">
                                                        <i class="ph ph-pencil text-xl"></i>
                                                    </button>

                                                    <?php if ($espActive): ?>
                                                    <form method="POST" action="/materias/especialidades/toggle-status" class="m-0">
                                                        <input type="hidden" name="id_especialidad" value="<?php echo (int) $especialidad['id_especialidad']; ?>">
                                                        <button type="button"
                                                                onclick="confirmAccion('Desactivar especialidad', 'Desea desactivar la especialidad \"<?php echo htmlspecialchars($especialidad['name']); ?>\"? Podra reactivarla en cualquier momento.', 'Si, desactivar', 'warn', this.closest('form'))"
                                                                class="text-slate-400 hover:text-uptval-orange transition-colors p-1" title="Desactivar especialidad">
                                                            <i class="ph ph-user-minus text-xl"></i>
                                                        </button>
                                                    </form>
                                                    <?php else: ?>
                                                    <form method="POST" action="/materias/especialidades/toggle-status" class="m-0">
                                                        <input type="hidden" name="id_especialidad" value="<?php echo (int) $especialidad['id_especialidad']; ?>">
                                                        <button type="submit" class="text-green-500 hover:text-green-600 transition-colors p-1" title="Activar especialidad">
                                                            <i class="ph ph-user-check text-xl"></i>
                                                        </button>
                                                    </form>
                                                    <?php endif; ?>

                                                    <form method="POST" action="/materias/especialidades/delete" class="m-0">
                                                        <input type="hidden" name="id_especialidad" value="<?php echo (int) $especialidad['id_especialidad']; ?>">
                                                        <button type="button"
                                                                onclick="confirmAccion('Eliminar especialidad', 'Esta seguro de eliminar la especialidad \"<?php echo htmlspecialchars($especialidad['name']); ?>\"? Esta accion no se puede deshacer.', 'Si, eliminar', 'danger', this.closest('form'))"
                                                                class="text-slate-400 hover:text-red-500 transition-colors p-1" title="Eliminar especialidad">
                                                            <i class="ph ph-trash text-xl"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php
                        $filterQuery = [];
                        if (!empty($_GET['status_filter'])) $filterQuery['status_filter'] = $_GET['status_filter'];
                        if (!empty($_GET['search'])) $filterQuery['search'] = $_GET['search'];
                        $filterQueryStr = !empty($filterQuery) ? '&' . http_build_query($filterQuery) : '';
                    ?>

                    <?php endif; ?>

                    <div class="flex items-center justify-center border-t border-gray-200 bg-gray-50/50 px-4 py-4 sm:px-6 w-full">
                        <div class="flex items-center gap-6">
                            <?php if ($page > 1): ?>
                                <a href="<?php echo $activeTab === 'materias' ? '/materias?page=' . ($page - 1) : ($activeTab === 'trayectos' ? '/materias/trayectos?page=' . ($page - 1) : '/materias/especialidades?page=' . ($page - 1)); ?><?= $filterQueryStr ?>" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                                    Anterior
                                </a>
                            <?php else: ?>
                                <button disabled class="relative inline-flex items-center rounded-md border border-gray-200 bg-gray-100/50 px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed shadow-sm">
                                    Anterior
                                </button>
                            <?php endif; ?>

                            <div>
                                <p class="text-sm text-gray-700">
                                    Pagina <span class="font-medium"><?= $page ?></span> de
                                    <span class="font-medium"><?= $totalPages > 0 ? $totalPages : 1 ?></span>
                                </p>
                            </div>

                            <?php if ($page < $totalPages): ?>
                                <a href="<?php echo $activeTab === 'materias' ? '/materias?page=' . ($page + 1) : ($activeTab === 'trayectos' ? '/materias/trayectos?page=' . ($page + 1) : '/materias/especialidades?page=' . ($page + 1)); ?><?= $filterQueryStr ?>" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
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

    <div id="modalRegistrarMateria" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[100] transition-opacity p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto transform scale-95 transition-transform modal-content">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 text-gray-800">
                <div>
                    <h3 class="text-lg font-bold">Registrar Materia</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Una materia pertenece a un trayecto y tiene una duración específica.</p>
                </div>
                <button onclick="toggleModal('modalRegistrarMateria')" class="text-gray-400 hover:text-red-500 transition-colors"><i class="ph ph-x text-xl"></i></button>
            </div>
            <div class="p-6">
                <form method="POST" action="/materias/store" class="grid grid-cols-1 gap-5">
                    <div>
                        <label for="materia_codigo" class="block text-sm font-medium text-gray-700 mb-1">Código</label>
                        <input type="text" id="materia_codigo" name="codigo" required maxlength="20" placeholder="Ej: MATE-101"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white text-gray-900">
                    </div>
                    <div>
                        <label for="materia_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <input type="text" id="materia_name" name="name" required maxlength="120" placeholder="Ej: Matemática I"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white text-gray-900">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="materia_duracion" class="block text-sm font-medium text-gray-700 mb-1">Duracion</label>
                            <select id="materia_duracion" name="duracion" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white text-gray-900">
                                <option value="">Seleccione...</option>
                                <?php foreach ($duraciones as $duracion): ?>
                                    <option value="<?php echo htmlspecialchars($duracion); ?>"><?php echo htmlspecialchars($duracion); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="materia_trayecto" class="block text-sm font-medium text-gray-700 mb-1">Trayecto</label>
                            <select id="materia_trayecto" name="id_trayecto" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white text-gray-900">
                                <option value="">Seleccione...</option>
                                <?php foreach ($trayectos as $trayecto): ?>
                                    <option value="<?php echo (int) $trayecto['id_trayecto']; ?>"><?php echo htmlspecialchars($trayecto['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="materia_especialidad" class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
                        <select id="materia_especialidad" name="id_especialidad" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white text-gray-900">
                            <option value="">Sin especialidad</option>
                            <?php foreach ($especialidades as $esp): ?>
                                <option value="<?php echo (int) $esp['id_especialidad']; ?>"><?php echo htmlspecialchars($esp['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="mt-2 px-4 py-2.5 bg-slate-900 hover:bg-black text-white font-medium rounded-lg shadow-md transition-all">Registrar Materia</button>
                </form>
            </div>
        </div>
    </div>

    <div id="modalEditarMateria" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[100] transition-opacity p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto transform scale-95 transition-transform modal-content">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 text-gray-800">
                <div>
                    <h3 class="text-lg font-bold">Editar Materia</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Actualice los datos de la materia.</p>
                </div>
                <button onclick="toggleModal('modalEditarMateria')" class="text-gray-400 hover:text-red-500 transition-colors"><i class="ph ph-x text-xl"></i></button>
            </div>
            <div class="p-6">
                <form method="POST" action="/materias/update" class="grid grid-cols-1 gap-5">
                    <input type="hidden" name="id_materia" id="edit_materia_id">
                    <div>
                        <label for="edit_materia_codigo" class="block text-sm font-medium text-gray-700 mb-1">Código</label>
                        <input type="text" id="edit_materia_codigo" name="codigo" required maxlength="20"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white text-gray-900">
                    </div>
                    <div>
                        <label for="edit_materia_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <input type="text" id="edit_materia_name" name="name" required maxlength="120"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white text-gray-900">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="edit_materia_duracion" class="block text-sm font-medium text-gray-700 mb-1">Duracion</label>
                            <select id="edit_materia_duracion" name="duracion" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white text-gray-900">
                                <option value="">Seleccione...</option>
                                <?php foreach ($duraciones as $duracion): ?>
                                    <option value="<?php echo htmlspecialchars($duracion); ?>"><?php echo htmlspecialchars($duracion); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="edit_materia_trayecto" class="block text-sm font-medium text-gray-700 mb-1">Trayecto</label>
                            <select id="edit_materia_trayecto" name="id_trayecto" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white text-gray-900">
                                <option value="">Seleccione...</option>
                                <?php foreach ($trayectos as $trayecto): ?>
                                    <option value="<?php echo (int) $trayecto['id_trayecto']; ?>"><?php echo htmlspecialchars($trayecto['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="edit_materia_especialidad" class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
                        <select id="edit_materia_especialidad" name="id_especialidad" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white text-gray-900">
                            <option value="">Sin especialidad</option>
                            <?php foreach ($especialidades as $esp): ?>
                                <option value="<?php echo (int) $esp['id_especialidad']; ?>"><?php echo htmlspecialchars($esp['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="mt-2 px-4 py-2.5 bg-slate-900 hover:bg-black text-white font-medium rounded-lg shadow-md transition-all">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>

    <div id="modalRegistrarTrayecto" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[100] transition-opacity p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform scale-95 transition-transform modal-content">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 text-gray-800">
                <div>
                    <h3 class="text-lg font-bold">Registrar Trayecto</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Ej: Trayecto I, Trayecto II, etc.</p>
                </div>
                <button onclick="toggleModal('modalRegistrarTrayecto')" class="text-gray-400 hover:text-red-500 transition-colors"><i class="ph ph-x text-xl"></i></button>
            </div>
            <div class="p-6">
                <form method="POST" action="/materias/trayectos/store" class="grid grid-cols-1 gap-5">
                    <div>
                        <label for="trayecto_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Trayecto</label>
                        <input type="text" id="trayecto_name" name="name" required maxlength="30" placeholder="Ej: Trayecto I"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white text-gray-900">
                    </div>
                    <button type="submit" class="mt-2 px-4 py-2.5 bg-slate-900 hover:bg-black text-white font-medium rounded-lg shadow-md transition-all">Registrar Trayecto</button>
                </form>
            </div>
        </div>
    </div>

    <div id="modalEditarTrayecto" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[100] transition-opacity p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform scale-95 transition-transform modal-content">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 text-gray-800">
                <div>
                    <h3 class="text-lg font-bold">Editar Trayecto</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Actualice el nombre del trayecto.</p>
                </div>
                <button onclick="toggleModal('modalEditarTrayecto')" class="text-gray-400 hover:text-red-500 transition-colors"><i class="ph ph-x text-xl"></i></button>
            </div>
            <div class="p-6">
                <form method="POST" action="/materias/trayectos/update" class="grid grid-cols-1 gap-5">
                    <input type="hidden" name="id_trayecto" id="edit_trayecto_id">
                    <div>
                        <label for="edit_trayecto_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Trayecto</label>
                        <input type="text" id="edit_trayecto_name" name="name" required maxlength="30"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white text-gray-900">
                    </div>
                    <button type="submit" class="mt-2 px-4 py-2.5 bg-slate-900 hover:bg-black text-white font-medium rounded-lg shadow-md transition-all">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>

    <div id="modalRegistrarEspecialidad" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[100] transition-opacity p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform scale-95 transition-transform modal-content">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 text-gray-800">
                <div>
                    <h3 class="text-lg font-bold">Registrar Especialidad</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Ej: Informatica, Electronica, Mecanica, etc.</p>
                </div>
                <button onclick="toggleModal('modalRegistrarEspecialidad')" class="text-gray-400 hover:text-red-500 transition-colors"><i class="ph ph-x text-xl"></i></button>
            </div>
            <div class="p-6">
                <form method="POST" action="/materias/especialidades/store" class="grid grid-cols-1 gap-5">
                    <div>
                        <label for="especialidad_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Especialidad</label>
                        <input type="text" id="especialidad_name" name="name" required maxlength="120" placeholder="Ej: Informatica"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white text-gray-900">
                    </div>
                    <button type="submit" class="mt-2 px-4 py-2.5 bg-slate-900 hover:bg-black text-white font-medium rounded-lg shadow-md transition-all">Registrar Especialidad</button>
                </form>
            </div>
        </div>
    </div>

    <div id="modalEditarEspecialidad" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[100] transition-opacity p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform scale-95 transition-transform modal-content">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 text-gray-800">
                <div>
                    <h3 class="text-lg font-bold">Editar Especialidad</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Actualice el nombre de la especialidad.</p>
                </div>
                <button onclick="toggleModal('modalEditarEspecialidad')" class="text-gray-400 hover:text-red-500 transition-colors"><i class="ph ph-x text-xl"></i></button>
            </div>
            <div class="p-6">
                <form method="POST" action="/materias/especialidades/update" class="grid grid-cols-1 gap-5">
                    <input type="hidden" name="id_especialidad" id="edit_especialidad_id">
                    <div>
                        <label for="edit_especialidad_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Especialidad</label>
                        <input type="text" id="edit_especialidad_name" name="name" required maxlength="120"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white text-gray-900">
                    </div>
                    <button type="submit" class="mt-2 px-4 py-2.5 bg-slate-900 hover:bg-black text-white font-medium rounded-lg shadow-md transition-all">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>

    <div id="modalConfirmAccion" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[100] transition-opacity p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform scale-95 transition-transform modal-content overflow-hidden">
            <div class="px-6 pt-8 pb-6 text-center">
                <div id="confirmAccionIconWrap" class="mx-auto w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mb-4">
                    <i id="confirmAccionIcon" class="ph ph-trash text-3xl text-red-600"></i>
                </div>
                <h3 id="confirmAccionTitle" class="text-xl font-bold text-gray-900">¿Eliminar?</h3>
                <p id="confirmAccionMessage" class="mt-2 text-sm text-gray-500">¿Está seguro de realizar esta acción?</p>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="toggleModal('modalConfirmAccion')" class="px-4 py-2 text-gray-600 font-medium hover:bg-gray-200 rounded-lg w-full sm:w-auto">Cancelar</button>
                <button type="button" id="confirmAccionBtn" onclick="confirmAccionSubmit()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg shadow-md flex items-center justify-center gap-2 w-full sm:w-auto transition-colors">
                    <i class="ph ph-trash text-lg"></i>
                    Sí, eliminar
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

        // =========================================================
        // CONFIRMACIÓN GENÉRICA (desactivar / eliminar)
        // =========================================================
        let pendingConfirmForm = null;

        function confirmAccion(title, message, btnLabel, type, form) {
            document.getElementById('confirmAccionTitle').innerText = title;
            document.getElementById('confirmAccionMessage').innerText = message;

            const iconWrap = document.getElementById('confirmAccionIconWrap');
            const icon = document.getElementById('confirmAccionIcon');
            const btn = document.getElementById('confirmAccionBtn');

            if (type === 'danger') {
                iconWrap.className = 'mx-auto w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mb-4';
                icon.className = 'ph ph-trash text-3xl text-red-600';
                btn.className = 'px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg shadow-md flex items-center justify-center gap-2 w-full sm:w-auto transition-colors';
                btn.innerHTML = '<i class="ph ph-trash text-lg"></i>' + btnLabel;
            } else {
                iconWrap.className = 'mx-auto w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center mb-4';
                icon.className = 'ph ph-pause text-3xl text-amber-600';
                btn.className = 'px-4 py-2 bg-uptval-orange hover:bg-uptval-dark text-white font-medium rounded-lg shadow-md flex items-center justify-center gap-2 w-full sm:w-auto transition-colors';
                btn.innerHTML = '<i class="ph ph-user-minus text-lg"></i>' + btnLabel;
            }

            pendingConfirmForm = form;
            toggleModal('modalConfirmAccion');
        }

        function confirmAccionSubmit() {
            if (pendingConfirmForm) {
                const form = pendingConfirmForm;
                pendingConfirmForm = null;
                toggleModal('modalConfirmAccion');
                form.submit();
            }
        }

        // =========================================================
        // EDICION DE MATERIA
        // =========================================================
        function openEditMateria(btn) {
            document.getElementById('edit_materia_id').value = btn.getAttribute('data-id');
            document.getElementById('edit_materia_codigo').value = btn.getAttribute('data-codigo');
            document.getElementById('edit_materia_name').value = btn.getAttribute('data-name');
            document.getElementById('edit_materia_duracion').value = btn.getAttribute('data-duracion');
            document.getElementById('edit_materia_trayecto').value = btn.getAttribute('data-trayecto');
            var especialidad = btn.getAttribute('data-especialidad') || '';
            document.getElementById('edit_materia_especialidad').value = especialidad;
            toggleModal('modalEditarMateria');
        }

        // =========================================================
        // EDICION DE TRAYECTO
        // =========================================================
        function openEditTrayecto(btn) {
            document.getElementById('edit_trayecto_id').value = btn.getAttribute('data-id');
            document.getElementById('edit_trayecto_name').value = btn.getAttribute('data-name');
            toggleModal('modalEditarTrayecto');
        }

        // =========================================================
        // EDICION DE ESPECIALIDAD
        // =========================================================
        function openEditEspecialidad(btn) {
            document.getElementById('edit_especialidad_id').value = btn.getAttribute('data-id');
            document.getElementById('edit_especialidad_name').value = btn.getAttribute('data-name');
            toggleModal('modalEditarEspecialidad');
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
