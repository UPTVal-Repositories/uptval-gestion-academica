<?php
/**
 * @var string $cedula
 * @var string $last_connection
 * @var string $total_staff
 * @var string $activos_staff
 * @var string $inactivos_staff
 */
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Dashboard - UPTVal</title>
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
        
        /* Scrollbar personalizado general */
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

        /* Animación para los gráficos del Dashboard */
        @keyframes drawCircle {
            from { stroke-dasharray: 0 100; }
        }
        .chart-circle {
            animation: drawCircle 1.5s ease-out forwards;
        }
        /* Ocultar barra de scroll específicamente para el carrusel */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
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
                <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 text-uptval-orange bg-uptval-orange/10 rounded-xl transition-all duration-300 border-l-2 border-uptval-orange shadow-[inset_0px_0px_20px_rgba(217,123,41,0.05)]">
                    <i class="ph ph-squares-four text-xl"></i>
                    <span class="font-medium text-sm">Inicio</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
                    <i class="ph ph-buildings text-xl"></i>
                    <span class="font-medium text-sm">Gestión de Departamentos</span>
                </a>
                <a href="/personal" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
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
                <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 text-uptval-orange bg-uptval-orange/10 rounded-xl transition-all duration-300 border-l-2 border-uptval-orange shadow-[inset_0px_0px_20px_rgba(217,123,41,0.05)]">
                    <i class="ph ph-squares-four text-xl"></i>
                    <span class="font-medium text-sm">Inicio</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
                    <i class="ph ph-buildings text-xl"></i>
                    <span class="font-medium text-sm">Gestión de Departamentos</span>
                </a>
                <a href="/personal" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
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
                <div class="pt-6 mt-6 border-t border-gray-800">
                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
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

        <main class="flex-1 overflow-y-auto bg-gray-50 text-gray-800 p-4 sm:p-6 md:p-10 relative z-10">
            
            <header class="mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">Panel Analítico</h2>
                    <p class="text-slate-500 text-sm sm:text-base mt-1 sm:mt-2">Resumen estadístico y estado general del sistema.</p>
                </div>
                <button class="bg-white border border-slate-200 text-slate-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-50 flex items-center justify-center gap-2 shadow-sm transition-colors w-full sm:w-auto">
                    <i class="ph ph-download-simple text-lg"></i>
                    Exportar Reporte
                </button>
            </header>

            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                
                <a href="/personal" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-orange-300 transition-all group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-orange-50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-slate-500 font-medium">Total Personal</p>
                            <h3 class="text-3xl font-bold text-slate-900 mt-1"><?= $total_staff ?? 0 ?></h3>
                        </div>
                        <div class="p-3 bg-orange-100 text-orange-600 rounded-xl">
                            <i class="ph ph-users text-2xl"></i>
                        </div>
                    </div>
                </a>

                <a href="/estudiantes" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-blue-300 transition-all group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-slate-500 font-medium">Total Estudiantes</p>
                            <h3 class="text-3xl font-bold text-slate-900 mt-1">1,248</h3>
                        </div>
                        <div class="p-3 bg-blue-100 text-blue-600 rounded-xl">
                            <i class="ph ph-student text-2xl"></i>
                        </div>
                    </div>
                </a>

                <a href="/departamentos" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-purple-300 transition-all group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-purple-50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-slate-500 font-medium">Departamentos</p>
                            <h3 class="text-3xl font-bold text-slate-900 mt-1">5</h3>
                        </div>
                        <div class="p-3 bg-purple-100 text-purple-600 rounded-xl">
                            <i class="ph ph-buildings text-2xl"></i>
                        </div>
                    </div>
                </a>

                <a href="/asistencia" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-green-300 transition-all group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-green-50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-slate-500 font-medium">Asistencia Promedio</p>
                            <h3 class="text-3xl font-bold text-slate-900 mt-1">92%</h3>
                        </div>
                        <div class="p-3 bg-green-100 text-green-600 rounded-xl">
                            <i class="ph ph-calendar-check text-2xl"></i>
                        </div>
                    </div>
                </a>
            </section>

            <section>
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Desglose Analítico</h2>
                        <p class="text-sm text-slate-500">Desliza para ver más métricas del sistema.</p>
                    </div>
                    <div class="flex gap-2">
                        <button id="btnPrev" class="p-2.5 bg-white border border-slate-200 rounded-full hover:bg-slate-50 hover:text-orange-500 shadow-sm transition-colors focus:outline-none">
                            <i class="ph ph-caret-left text-lg"></i>
                        </button>
                        <button id="btnNext" class="p-2.5 bg-white border border-slate-200 rounded-full hover:bg-slate-50 hover:text-orange-500 shadow-sm transition-colors focus:outline-none">
                            <i class="ph ph-caret-right text-lg"></i>
                        </button>
                    </div>
                </div>

                <div id="chartCarousel" class="flex gap-6 overflow-x-auto snap-x snap-mandatory hide-scrollbar pb-4 scroll-smooth">
                    
                    <div class="snap-center shrink-0 w-full lg:w-[calc(50%-0.75rem)] bg-white border border-slate-200 rounded-2xl shadow-sm flex flex-col relative">
                        <div class="p-6 pb-2">
                            <div class="flex justify-between items-center mb-6">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900">Estado del Personal</h3>
                                    <p class="text-sm text-slate-500">Proporción de personal activo vs inactivo.</p>
                                </div>
                                <div class="p-2 bg-orange-50 text-orange-500 rounded-lg"><i class="ph ph-users text-xl"></i></div>
                            </div>
                            
                            <div class="flex-1 flex flex-col xl:flex-row items-center justify-center gap-8 mb-6">
                                <div class="relative w-36 h-36 flex-shrink-0">
                                    
                                    <?php 
                                        // Cálculo de porcentajes para el dibujo SVG (evita división por cero)
                                        $porcentaje_activos = ($total_staff > 0) ? round(($activos_staff / $total_staff) * 100) : 0;
                                        $porcentaje_inactivos = 100 - $porcentaje_activos;
                                    ?>
                                    
                                    <svg viewBox="0 0 40 40" class="w-full h-full -rotate-90">
                                        <circle cx="20" cy="20" r="15.915" fill="none" stroke="#fee2e2" stroke-width="5"></circle>
                                        <circle class="chart-circle" cx="20" cy="20" r="15.915" fill="none" stroke="#f97316" stroke-width="5" stroke-dasharray="<?= $porcentaje_activos ?> <?= $porcentaje_inactivos ?>" stroke-linecap="round"></circle>
                                    </svg>
                                    
                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                        <span class="text-2xl font-bold text-slate-800"><?= $total_staff ?></span>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total</span>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-4 w-full xl:w-auto">
                                    <div class="flex items-center justify-between gap-6">
                                        <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-orange-500"></div><span class="text-sm font-medium text-slate-600">Activos</span></div>
                                        <span class="text-base font-bold text-slate-900"><?= $activos_staff ?></span>
                                    </div>
                                    <div class="flex items-center justify-between gap-6">
                                        <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-red-200"></div><span class="text-sm font-medium text-slate-600">Inactivos</span></div>
                                        <span class="text-base font-bold text-slate-900"><?= $inactivos_staff ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-auto border-t border-slate-100 bg-slate-50 rounded-b-2xl">
                            <a href="/personal" class="w-full py-4 px-6 flex items-center justify-center gap-2 text-sm font-bold text-orange-600 hover:text-orange-700 hover:bg-orange-50/50 transition-colors rounded-b-2xl">
                                Ir a Gestión de Personal <i class="ph ph-arrow-right font-bold"></i>
                            </a>
                        </div>
                    </div>

                    <div class="snap-center shrink-0 w-full lg:w-[calc(50%-0.75rem)] bg-white border border-slate-200 rounded-2xl shadow-sm flex flex-col relative">
                        <div class="p-6 pb-2">
                            <div class="flex justify-between items-center mb-6">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900">Estado Estudiantil</h3>
                                    <p class="text-sm text-slate-500">Alumnos regulares frente a inactivos.</p>
                                </div>
                                <div class="p-2 bg-blue-50 text-blue-500 rounded-lg"><i class="ph ph-student text-xl"></i></div>
                            </div>
                            
                            <div class="flex-1 flex flex-col xl:flex-row items-center justify-center gap-8 mb-6">
                                <div class="relative w-36 h-36 flex-shrink-0">
                                    <svg viewBox="0 0 40 40" class="w-full h-full -rotate-90">
                                        <circle cx="20" cy="20" r="15.915" fill="none" stroke="#e0e7ff" stroke-width="5"></circle>
                                        <circle class="chart-circle" cx="20" cy="20" r="15.915" fill="none" stroke="#3b82f6" stroke-width="5" stroke-dasharray="85 15" stroke-linecap="round"></circle>
                                    </svg>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                        <span class="text-2xl font-bold text-slate-800">1,248</span>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total</span>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-4 w-full xl:w-auto">
                                    <div class="flex items-center justify-between gap-6">
                                        <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-blue-500"></div><span class="text-sm font-medium text-slate-600">Regulares</span></div>
                                        <span class="text-base font-bold text-slate-900">1,060</span>
                                    </div>
                                    <div class="flex items-center justify-between gap-6">
                                        <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-indigo-100"></div><span class="text-sm font-medium text-slate-600">Inactivos</span></div>
                                        <span class="text-base font-bold text-slate-900">188</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-auto border-t border-slate-100 bg-slate-50 rounded-b-2xl">
                            <a href="/estudiantes" class="w-full py-4 px-6 flex items-center justify-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-700 hover:bg-blue-50/50 transition-colors rounded-b-2xl">
                                Ir a Gestión de Estudiantes <i class="ph ph-arrow-right font-bold"></i>
                            </a>
                        </div>
                    </div>

                    <div class="snap-center shrink-0 w-full lg:w-[calc(50%-0.75rem)] bg-white border border-slate-200 rounded-2xl shadow-sm flex flex-col relative">
                        <div class="p-6 pb-2">
                            <div class="flex justify-between items-center mb-6">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900">Reporte de Asistencia</h3>
                                    <p class="text-sm text-slate-500">Cumplimiento del periodo actual.</p>
                                </div>
                                <div class="p-2 bg-green-50 text-green-500 rounded-lg"><i class="ph ph-calendar-check text-xl"></i></div>
                            </div>
                            
                            <div class="flex-1 flex flex-col xl:flex-row items-center justify-center gap-8 mb-6">
                                <div class="relative w-36 h-36 flex-shrink-0">
                                    <svg viewBox="0 0 40 40" class="w-full h-full -rotate-90">
                                        <circle cx="20" cy="20" r="15.915" fill="none" stroke="#dcfce7" stroke-width="5"></circle>
                                        <circle class="chart-circle" cx="20" cy="20" r="15.915" fill="none" stroke="#22c55e" stroke-width="5" stroke-dasharray="92 8" stroke-linecap="round"></circle>
                                    </svg>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                        <span class="text-2xl font-bold text-slate-800">92%</span>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Promedio</span>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-4 w-full xl:w-auto">
                                    <div class="flex items-center justify-between gap-6">
                                        <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-green-500"></div><span class="text-sm font-medium text-slate-600">Presentes</span></div>
                                        <span class="text-base font-bold text-slate-900">+1.2k</span>
                                    </div>
                                    <div class="flex items-center justify-between gap-6">
                                        <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-green-200"></div><span class="text-sm font-medium text-slate-600">Ausencias</span></div>
                                        <span class="text-base font-bold text-slate-900">84</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-auto border-t border-slate-100 bg-slate-50 rounded-b-2xl">
                            <a href="/asistencia" class="w-full py-4 px-6 flex items-center justify-center gap-2 text-sm font-bold text-green-600 hover:text-green-700 hover:bg-green-50/50 transition-colors rounded-b-2xl">
                                Ir a Control de Asistencia <i class="ph ph-arrow-right font-bold"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </section>

        </main>
    </div>

    <script>
        // Lógica de Sidebar Móvil
        const menuToggle = document.getElementById('menuToggle');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const closeMenu = document.getElementById('closeMenu');
        const menuOverlay = document.getElementById('menuOverlay');
        const body = document.body;

        function openMenu() {
            mobileSidebar.classList.add('open');
            menuOverlay.classList.remove('hidden');
            body.classList.add('menu-open');
        }

        function closeMenuFunc() {
            mobileSidebar.classList.remove('open');
            menuOverlay.classList.add('hidden');
            body.classList.remove('menu-open');
        }

        if (menuToggle) menuToggle.addEventListener('click', openMenu);
        if (closeMenu) closeMenu.addEventListener('click', closeMenuFunc);
        if (menuOverlay) menuOverlay.addEventListener('click', closeMenuFunc);

        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                closeMenuFunc();
            }
        });

        // Lógica del Carrusel de Estadísticas
        const carousel = document.getElementById('chartCarousel');
        const btnNext = document.getElementById('btnNext');
        const btnPrev = document.getElementById('btnPrev');

        if(carousel && btnNext && btnPrev) {
            // Desplazamiento dinámico según el tamaño de la pantalla
            const scrollAmount = () => carousel.offsetWidth / (window.innerWidth >= 1024 ? 2 : 1);

            btnNext.addEventListener('click', () => {
                carousel.scrollBy({ left: scrollAmount(), behavior: 'smooth' });
            });

            btnPrev.addEventListener('click', () => {
                carousel.scrollBy({ left: -scrollAmount(), behavior: 'smooth' });
            });
        }
    </script>
</body>
</html>