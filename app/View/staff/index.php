<?php
/**
 * @var string $cedula
 * @var string $last_connection
 * @var string $page
 * @var string $totalPages
 * @var array $staffList
 * @var array $departamentos
 */
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

        <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
            <i class="ph ph-buildings text-xl"></i>
            <span class="font-medium text-sm">Gestión de Departamentos</span>
        </a>

        <a href="/personal" class="flex items-center gap-3 px-4 py-3 text-uptval-orange bg-uptval-orange/10 rounded-xl transition-all duration-300 border-l-2 border-uptval-orange shadow-[inset_0px_0px_20px_rgba(217,123,41,0.05)]">
            <i class="ph ph-users text-xl"></i>
            <span class="font-medium text-sm">Gestión de Personal</span>
        </a>

        <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
            <i class="ph ph-student text-xl"></i>
            <span class="font-medium text-sm">Estudiantes</span>
        </a>

        <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
            <i class="ph ph-calendar-check text-xl"></i>
            <span class="font-medium text-sm">Control de Asistencia</span>
        </a>
    </nav>

                <div class="mt-8">
                    <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-4 px-4">Sistema</p>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
                        <i class="ph ph-gear text-xl"></i>
                        <span class="font-medium text-sm">Configuración</span>
                    </a>
                </div>
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
                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300">
                        <i class="ph ph-buildings text-xl"></i>
                        <span class="font-medium text-sm">Gestión de Departamentos</span>
                    </a>
                    <a href="/personal" class="flex items-center gap-3 px-4 py-3 text-uptval-orange bg-uptval-orange/10 rounded-xl transition-all duration-300 border-l-2 border-uptval-orange">
                        <i class="ph ph-users text-xl"></i>
                        <span class="font-medium text-sm">Gestión de Personal</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300">
                        <i class="ph ph-student text-xl"></i>
                        <span class="font-medium text-sm">Estudiantes</span>
                    </a>
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
                    <button onclick="toggleModal('modalRegistrar')" class="bg-slate-900 hover:bg-black text-white px-5 py-2.5 rounded-lg font-medium shadow-sm transition-all flex items-center justify-center gap-2 transform hover:scale-105 w-full sm:w-auto">
                        <i class="ph ph-plus-circle text-lg"></i>
                        Registrar Personal
                    </button>
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

                        <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row gap-4 items-center justify-between">
                            <div class="relative w-full sm:max-w-xs">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="ph ph-magnifying-glass text-gray-400"></i>
                                </div>
                                <input type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors" placeholder="Buscar por cédula o nombre...">
                            </div>
                            

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full">
        
                                <div class="relative">
                                    <label class="absolute -top-2.5 left-3 px-1 bg-white text-[10px] font-bold text-uptval-black tracking-wider pointer-events-none z-10">
                                        Departamento
                                    </label>
                                    <select class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-uptval-blue focus:border-uptval-blue bg-white transition-all">
                                        <option value="">Todos los Departamentos</option>
                                        <?php foreach ($departamentos as $depto): ?>
                                            <option value="<?php echo htmlspecialchars($depto['id_department']); ?>">
                                                <?php echo htmlspecialchars($depto['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="relative">
                                    <label class="absolute -top-2.5 left-3 px-1 bg-white text-[10px] font-bold text-uptval-black tracking-wider pointer-events-none z-10">
                                        Tipo
                                    </label>
                                    <select class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-uptval-blue focus:border-uptval-blue bg-white transition-all">
                                        <option value="">Todos los Tipos</option>
                                        <option value="Docente">Docente</option>
                                        <option value="Administrativo">Administrativo</option>
                                    </select>
                                </div>

                                <div class="relative">
                                    <label class="absolute -top-2.5 left-3 px-1 bg-white text-[10px] font-bold text-uptval-blac tracking-wider pointer-events-none z-10">
                                        Estatus
                                    </label>
                                    <select class="w-full pl-3 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-uptval-blue focus:border-uptval-blue bg-white transition-all">
                                        <option value="">Todos los Estatus</option>
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <th scope="col" class="px-6 py-3 text-left">Cédula</th>
                                        <th scope="col" class="px-6 py-3 text-left">Personal</th>
                                        <th scope="col" class="px-6 py-3 text-left">Tipo / Departamento</th>
                                        <th scope="col" class="px-6 py-3 text-center">Estado</th>
                                        <th scope="col" class="px-6 py-3 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($staffList)): ?>
                                        <tr>
                                            <td colspan="5" class="py-10 text-center text-gray-500 bg-gray-50">
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
                                                        <?php echo htmlspecialchars($person['pas'].' - '. $person['type_staff']); ?>
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        <?php echo htmlspecialchars($person['name'] ?? 'Sin Asignar'); ?>
                                                    </div>
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <?php if ($person['status'] === 'activo'): ?>
                                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                                            Activo
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">
                                                            Inactivo
                                                        </span>
                                                    <?php endif; ?>
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <button class="text-slate-400 hover:text-blue-600 transition-colors p-1" title="Ver Detalles">
                                                            <i class="ph ph-eye text-xl"></i>
                                                        </button>
                                                        <button class="text-slate-400 hover:text-amber-500 transition-colors p-1" title="Editar">
                                                            <i class="ph ph-pencil-simple text-xl"></i>
                                                        </button>
                                                        <?php if ($person['status'] === 'activo'): ?>
                                                            <button class="text-slate-400 hover:text-red-600 transition-colors p-1" title="Inactivar">
                                                                <i class="ph ph-prohibit text-xl"></i>
                                                            </button>
                                                        <?php else: ?>
                                                            <button class="text-slate-400 hover:text-green-600 transition-colors p-1" title="Reactivar">
                                                                <i class="ph ph-check-circle text-xl"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>

                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center justify-center border-t border-gray-200 bg-gray-50/50 px-4 py-4 sm:px-6 w-full">
                            <div class="flex items-center gap-6">
                                <?php if ($page > 1): ?>
                                    <a href="/personal?page=<?= $page - 1 ?>" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
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
                                    <a href="/personal?page=<?= $page + 1 ?>" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
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
                        
                        <div class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-8 shadow-sm max-w-4xl mx-auto flex flex-col md:flex-row items-center gap-8 sm:gap-10">
                            
                            <div class="relative w-40 h-40 sm:w-48 sm:h-48 shrink-0">
                                <svg viewBox="0 0 40 40" class="w-full h-full -rotate-90 drop-shadow-md">
                                    <circle cx="20" cy="20" r="15.915" fill="none" stroke="#3b82f6" stroke-width="6" stroke-dasharray="60 40" stroke-dashoffset="0" class="transition-all duration-1000 ease-out"></circle>
                                    
                                    <circle cx="20" cy="20" r="15.915" fill="none" stroke="#f97316" stroke-width="6" stroke-dasharray="30 70" stroke-dashoffset="-60" class="transition-all duration-1000 ease-out"></circle>
                                    
                                    <circle cx="20" cy="20" r="15.915" fill="none" stroke="#10b981" stroke-width="6" stroke-dasharray="10 90" stroke-dashoffset="-90" class="transition-all duration-1000 ease-out"></circle>
                                </svg>
                                
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-800">120</span>
                                    <span class="text-[9px] sm:text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Registros</span>
                                </div>
                            </div>
                            
                            <div class="flex-1 w-full grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">
                                
                                <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="flex items-center gap-2 font-bold text-slate-700 text-sm sm:text-base">
                                            <div class="w-3 h-3 rounded-full bg-blue-500 shadow-sm shadow-blue-200"></div> 
                                            Docentes (60%)
                                        </span>
                                        <span class="font-extrabold text-slate-900 text-lg">72</span>
                                    </div>
                                    <div class="flex gap-3 text-[11px] sm:text-xs font-medium pl-5">
                                        <span class="text-green-600 bg-green-50 px-2 py-0.5 rounded-md flex items-center gap-1"><i class="ph ph-check-circle"></i> 65 Activos</span>
                                        <span class="text-red-500 bg-red-50 px-2 py-0.5 rounded-md flex items-center gap-1"><i class="ph ph-prohibit"></i> 7 Inactivos</span>
                                    </div>
                                </div>

                                <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="flex items-center gap-2 font-bold text-slate-700 text-sm sm:text-base">
                                            <div class="w-3 h-3 rounded-full bg-uptval-orange shadow-sm shadow-orange-200"></div> 
                                            Administrativos (30%)
                                        </span>
                                        <span class="font-extrabold text-slate-900 text-lg">36</span>
                                    </div>
                                    <div class="flex gap-3 text-[11px] sm:text-xs font-medium pl-5">
                                        <span class="text-green-600 bg-green-50 px-2 py-0.5 rounded-md flex items-center gap-1"><i class="ph ph-check-circle"></i> 30 Activos</span>
                                        <span class="text-red-500 bg-red-50 px-2 py-0.5 rounded-md flex items-center gap-1"><i class="ph ph-prohibit"></i> 6 Inactivos</span>
                                    </div>
                                </div>

                                <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 sm:col-span-2">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="flex items-center gap-2 font-bold text-slate-700 text-sm sm:text-base">
                                            <div class="w-3 h-3 rounded-full bg-emerald-500 shadow-sm shadow-emerald-200"></div> 
                                            Obreros (10%)
                                        </span>
                                        <span class="font-extrabold text-slate-900 text-lg">12</span>
                                    </div>
                                    <div class="flex gap-3 text-[11px] sm:text-xs font-medium pl-5">
                                        <span class="text-green-600 bg-green-50 px-2 py-0.5 rounded-md flex items-center gap-1"><i class="ph ph-check-circle"></i> 10 Activos</span>
                                        <span class="text-red-500 bg-red-50 px-2 py-0.5 rounded-md flex items-center gap-1"><i class="ph ph-prohibit"></i> 2 Inactivos</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    </div>
            </main>
        </div>

    <div id="modalRegistrar" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[100] transition-opacity p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden transform scale-95 transition-transform" id="modalContent">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 text-gray-800">
                <h3 class="text-lg font-bold">Registrar Nuevo Personal</h3>
                <button onclick="toggleModal('modalRegistrar')" class="text-gray-400 hover:text-red-500 transition-colors"><i class="ph ph-x text-xl"></i></button>
            </div>
            <div class="p-6">
                <form class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-gray-800">
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cédula</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombres</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white">
                    </div>
                    <div class="col-span-1 sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo Institucional</label>
                        <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-800 outline-none bg-white">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Personal</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none bg-white">
                            <option value="Docente">Docente</option>
                            <option value="Administrativo">Administrativo</option>
                        </select>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none bg-white">
                            <option value="1">Ingeniería en Informática</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-end gap-3">
                <button onclick="toggleModal('modalRegistrar')" class="px-4 py-2 text-gray-600 font-medium hover:bg-gray-200 rounded-lg w-full sm:w-auto">Cancelar</button>
                <button class="px-4 py-2 bg-slate-900 hover:bg-black text-white font-medium rounded-lg shadow-md w-full sm:w-auto">Guardar Registro</button>
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
            const content = document.getElementById('modalContent');
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
    </script>
</body>
</html>