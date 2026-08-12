<?php
/**
 * Vista de Estudiantes (solo lectura)
 * @var string $cedula
 * @var string $last_connection
 * @var int $page
 * @var int $totalPages
 * @var int $totalRecords
 * @var array $estudiantes
 * @var array $trayectos
 * @var array $especialidades
 * @var array $userRoles
 */
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Estudiantes - UPTVal</title>
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
    </style>
</head>
<body class="bg-slate-950 h-screen text-white font-sans flex flex-col overflow-hidden">

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
            <span class="text-xs text-gray-500 uppercase tracking-widest hidden md:inline ml-4 border-l border-gray-700 pl-4">Gestion Academica</span>
        </div>
        <div class="flex items-center gap-2 sm:gap-4">
            <div class="text-right hidden sm:block">
                <p class="text-sm font-medium text-gray-300">Usuario Activo</p>
                <p class="text-xs text-uptval-orange font-bold">C.I: <?php echo htmlspecialchars($cedula ?? ''); ?></p>
                <?php if (!empty($last_connection)): ?>
                    <p class="text-[11px] text-gray-500 mt-1">Ultima conexion: <?php echo htmlspecialchars($last_connection); ?></p>
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

        <!-- SIDEBAR DESKTOP -->
        <aside class="w-64 glass-panel border-r border-gray-800 flex flex-col py-6 px-4 shrink-0 overflow-y-auto hidden md:flex relative z-10">
            <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-4 px-4">Menu Principal</p>
            <nav class="space-y-1.5 flex-1">
                <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
                    <i class="ph ph-squares-four text-xl"></i>
                    <span class="font-medium text-sm">Inicio</span>
                </a>
                <?php if (in_array('Administrador', $userRoles)): ?>
                <a href="/departamentos" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
                    <i class="ph ph-buildings text-xl"></i>
                    <span class="font-medium text-sm">Gestion de Departamentos</span>
                </a>
                <a href="/materias" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
                    <i class="ph ph-books text-xl"></i>
                    <span class="font-medium text-sm">Materias</span>
                </a>
                <a href="/personal" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
                    <i class="ph ph-users text-xl"></i>
                    <span class="font-medium text-sm">Gestion de Personal</span>
                </a>
                <a href="/aulas" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
                    <i class="ph ph-door text-xl"></i>
                    <span class="font-medium text-sm">Aulas y Laboratorios</span>
                </a>
                <?php endif; ?>
                <a href="/estudiantes" class="flex items-center gap-3 px-4 py-3 text-uptval-orange bg-uptval-orange/10 rounded-xl transition-all duration-300 border-l-2 border-uptval-orange shadow-[inset_0px_0px_20px_rgba(217,123,41,0.05)]">
                    <i class="ph ph-student text-xl"></i>
                    <span class="font-medium text-sm">Estudiantes</span>
                </a>
                <a href="/documentacion" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
                    <i class="ph ph-book-open-text text-xl"></i>
                    <span class="font-medium text-sm">Manual de Usuario</span>
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

        <!-- SIDEBAR MOBILE -->
        <div id="mobileSidebar" class="mobile-menu fixed top-0 left-0 h-full w-64 glass-panel border-r border-gray-800 flex flex-col py-6 px-4 z-50 md:hidden shadow-2xl">
            <div class="flex justify-between items-center mb-6 px-4">
                <h2 class="text-lg font-bold text-white">Menu</h2>
                <button id="closeMenu" class="text-gray-400 hover:text-white"><i class="ph ph-x text-2xl"></i></button>
            </div>
            <nav class="space-y-1.5 flex-1">
                <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300"><i class="ph ph-squares-four text-xl"></i><span class="font-medium text-sm">Inicio</span></a>
                <?php if (in_array('Administrador', $userRoles)): ?>
                <a href="/departamentos" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300"><i class="ph ph-buildings text-xl"></i><span class="font-medium text-sm">Gestion de Departamentos</span></a>
                <a href="/materias" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300"><i class="ph ph-books text-xl"></i><span class="font-medium text-sm">Materias</span></a>
                <a href="/personal" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300"><i class="ph ph-users text-xl"></i><span class="font-medium text-sm">Gestion de Personal</span></a>
                <a href="/aulas" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300"><i class="ph ph-door text-xl"></i><span class="font-medium text-sm">Aulas y Laboratorios</span></a>
                <?php endif; ?>
                <a href="/estudiantes" class="flex items-center gap-3 px-4 py-3 text-uptval-orange bg-uptval-orange/10 rounded-xl transition-all duration-300 border-l-2 border-uptval-orange"><i class="ph ph-student text-xl"></i><span class="font-medium text-sm">Estudiantes</span></a>
                <a href="/documentacion" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300"><i class="ph ph-book-open-text text-xl"></i><span class="font-medium text-sm">Manual de Usuario</span></a>
                <?php if (in_array('Administrador', $userRoles)): ?>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300"><i class="ph ph-calendar-check text-xl"></i><span class="font-medium text-sm">Control de Asistencia</span></a>
                <div class="pt-6 mt-6 border-t border-gray-800">
                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300"><i class="ph ph-gear text-xl"></i><span class="font-medium text-sm">Configuracion</span></a>
                </div>
                <?php endif; ?>
            </nav>
            <div class="mt-auto pt-4 border-t border-gray-800">
                <div class="flex items-center gap-3 px-4 py-3">
                    <div class="w-10 h-10 rounded-full bg-uptval-orange/20 flex items-center justify-center flex-shrink-0"><i class="ph ph-user text-xl text-uptval-orange"></i></div>
                    <div class="flex-1"><p class="text-xs text-gray-400">Usuario Activo</p><p class="text-sm text-uptval-orange font-bold">C.I: <?php echo htmlspecialchars($cedula ?? ''); ?></p></div>
                </div>
            </div>
        </div>
        <div id="menuOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden md:hidden"></div>

        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-y-auto bg-gray-50 text-gray-800 flex flex-col relative z-10">

            <header class="bg-white px-6 sm:px-8 py-5 shadow-sm border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 sticky top-0 z-20">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Estudiantes</h2>
                    <p class="text-sm text-gray-500 mt-1">Listado de estudiantes registrados en el sistema. El registro se realiza desde el sistema de inscripcion.</p>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                    <div class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 text-sm">
                        <i class="ph ph-info text-lg"></i>
                        <span>Modulo de solo lectura</span>
                    </div>
                    <?php
                        $filterQuery = [];
                        if (!empty($_GET['trayecto_filter'])) $filterQuery['trayecto_filter'] = $_GET['trayecto_filter'];
                        if (!empty($_GET['especialidad_filter'])) $filterQuery['especialidad_filter'] = $_GET['especialidad_filter'];
                        if (!empty($_GET['status_filter'])) $filterQuery['status_filter'] = $_GET['status_filter'];
                        if (!empty($_GET['search'])) $filterQuery['search'] = $_GET['search'];
                        $pdfUrl = '/estudiantes/export-pdf' . (!empty($filterQuery) ? '?' . http_build_query($filterQuery) : '');
                    ?>
                    <a href="<?= $pdfUrl ?>"
                       class="bg-white border border-slate-300 text-slate-700 px-4 py-2.5 rounded-lg font-medium shadow-sm transition-all flex items-center justify-center gap-2 hover:bg-slate-50 w-full sm:w-auto">
                        <i class="ph ph-file-pdf text-lg text-red-500"></i>
                        Exportar PDF
                    </a>
                </div>
            </header>

            <div class="p-4 sm:p-6 md:p-8 flex-1">

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                    <!-- FILTROS -->
                    <form method="GET" action="/estudiantes" class="p-4 border-b border-gray-100 flex flex-col sm:flex-row gap-4 items-center justify-between">
                        <div class="relative w-full sm:max-w-xs">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="ph ph-magnifying-glass text-gray-400"></i>
                            </div>
                            <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors" placeholder="Buscar por cedula o nombre...">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full">

                            <div class="relative">
                                <label class="absolute -top-2.5 left-3 px-1 bg-white text-[10px] font-bold text-gray-700 tracking-wider pointer-events-none z-10">
                                    Trayecto
                                </label>
                                <select name="trayecto_filter" onchange="this.form.submit()" class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all">
                                    <option value="">Todos los trayectos</option>
                                    <?php foreach ($trayectos as $trayecto): ?>
                                        <option value="<?php echo htmlspecialchars($trayecto['id_trayecto']); ?>" <?php echo (($_GET['trayecto_filter'] ?? '') == $trayecto['id_trayecto']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($trayecto['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="relative">
                                <label class="absolute -top-2.5 left-3 px-1 bg-white text-[10px] font-bold text-gray-700 tracking-wider pointer-events-none z-10">
                                    Especialidad
                                </label>
                                <select name="especialidad_filter" onchange="this.form.submit()" class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all">
                                    <option value="">Todas las especialidades</option>
                                    <?php foreach ($especialidades as $esp): ?>
                                        <option value="<?php echo htmlspecialchars($esp['id_especialidad']); ?>" <?php echo (($_GET['especialidad_filter'] ?? '') == $esp['id_especialidad']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($esp['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="relative">
                                <label class="absolute -top-2.5 left-3 px-1 bg-white text-[10px] font-bold text-gray-700 tracking-wider pointer-events-none z-10">
                                    Estatus
                                </label>
                                <select name="status_filter" onchange="this.form.submit()" class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all">
                                    <option value="">Todos los estatus</option>
                                    <option value="activo" <?php echo (($_GET['status_filter'] ?? '') === 'activo') ? 'selected' : ''; ?>>Activo</option>
                                    <option value="inactivo" <?php echo (($_GET['status_filter'] ?? '') === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                                </select>
                            </div>

                        </div>
                    </form>

                    <!-- TABLA -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <th scope="col" class="px-6 py-3 text-left">Cedula</th>
                                    <th scope="col" class="px-6 py-3 text-left">Apellidos y Nombres</th>
                                    <th scope="col" class="px-6 py-3 text-left">Trayecto</th>
                                    <th scope="col" class="px-6 py-3 text-left">Especialidad</th>
                                    <th scope="col" class="px-6 py-3 text-center">Seccion</th>
                                    <th scope="col" class="px-6 py-3 text-left">Contacto</th>
                                    <th scope="col" class="px-6 py-3 text-center">Estatus</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $hasFilters = !empty($_GET['trayecto_filter'] ?? $_GET['especialidad_filter'] ?? $_GET['status_filter'] ?? $_GET['search'] ?? ''); ?>
                                <?php if (empty($estudiantes) && $hasFilters): ?>
                                    <tr>
                                        <td colspan="7" class="py-10 text-center text-gray-500 bg-gray-50">
                                            <i class="ph ph-funnel text-5xl mb-3 block opacity-50"></i>
                                            No se encontraron estudiantes con los filtros aplicados.
                                        </td>
                                    </tr>
                                <?php elseif (empty($estudiantes)): ?>
                                    <tr>
                                        <td colspan="7" class="py-10 text-center text-gray-500 bg-gray-50">
                                            <i class="ph ph-student text-5xl mb-3 block opacity-50"></i>
                                            No hay estudiantes registrados en el sistema.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($estudiantes as $est): ?>
                                        <?php $estActive = ($est['status'] ?? 'activo') === 'activo'; ?>
                                        <tr class="hover:bg-gray-50 transition-colors <?php echo $estActive ? '' : 'opacity-60'; ?>">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 font-mono font-semibold">
                                                    <?php echo htmlspecialchars($est['cedula']); ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0">
                                                        <i class="ph ph-student text-lg text-slate-500"></i>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">
                                                            <?php echo htmlspecialchars($est['last_name']); ?>
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            <?php echo htmlspecialchars($est['first_name']); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2.5 py-1 inline-flex items-center gap-1 text-xs leading-5 font-semibold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                    <i class="ph ph-graduation-cap"></i>
                                                    <?php echo htmlspecialchars($est['trayecto_name']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php if (!empty($est['especialidad_name'])): ?>
                                                    <span class="px-2.5 py-1 inline-flex items-center gap-1 text-xs leading-5 font-semibold rounded-full bg-purple-50 text-purple-700 border border-purple-200">
                                                        <i class="ph ph-certificate"></i>
                                                        <?php echo htmlspecialchars($est['especialidad_name']); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-xs text-gray-400 italic">Sin asignar</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <?php if (!empty($est['seccion'])): ?>
                                                    <span class="px-2.5 py-1 text-xs font-bold text-slate-700 bg-slate-100 rounded">
                                                        <?php echo htmlspecialchars($est['seccion']); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-xs text-gray-400">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-xs text-gray-600 space-y-0.5">
                                                    <?php if (!empty($est['phone'])): ?>
                                                        <div class="flex items-center gap-1">
                                                            <i class="ph ph-phone text-gray-400"></i>
                                                            <?php echo htmlspecialchars($est['phone']); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if (!empty($est['email'])): ?>
                                                        <div class="flex items-center gap-1">
                                                            <i class="ph ph-envelope text-gray-400"></i>
                                                            <?php echo htmlspecialchars($est['email']); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if (empty($est['phone']) && empty($est['email'])): ?>
                                                        <span class="text-gray-400 italic">Sin contacto</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <?php if ($estActive): ?>
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
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINACION -->
                    <?php
                        $filterQuery = [];
                        if (!empty($_GET['trayecto_filter'])) $filterQuery['trayecto_filter'] = $_GET['trayecto_filter'];
                        if (!empty($_GET['especialidad_filter'])) $filterQuery['especialidad_filter'] = $_GET['especialidad_filter'];
                        if (!empty($_GET['status_filter'])) $filterQuery['status_filter'] = $_GET['status_filter'];
                        if (!empty($_GET['search'])) $filterQuery['search'] = $_GET['search'];
                        $filterQueryStr = !empty($filterQuery) ? '&' . http_build_query($filterQuery) : '';
                    ?>

                    <div class="flex items-center justify-center border-t border-gray-200 bg-gray-50/50 px-4 py-4 sm:px-6 w-full">
                        <div class="flex items-center gap-6">
                            <?php if ($page > 1): ?>
                                <a href="/estudiantes?page=<?= $page - 1 ?><?= $filterQueryStr ?>" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
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
                                <a href="/estudiantes?page=<?= $page + 1 ?><?= $filterQueryStr ?>" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
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

    <script>
        const menuToggle = document.getElementById('menuToggle');
        const closeMenu = document.getElementById('closeMenu');
        const mobileSidebar = document.getElementById('mobileSidebar');
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

        menuToggle.addEventListener('click', openMenu);
        closeMenu.addEventListener('click', closeMenuFunc);
        menuOverlay.addEventListener('click', closeMenuFunc);
    </script>
</body>
</html>
