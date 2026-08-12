<?php
/**
 * @var string $cedula
 * @var string $last_connection
 * @var array $departamentos
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
    <title>Gestión de Departamentos - UPTVal</title>
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
                <a href="/departamentos" class="flex items-center gap-3 px-4 py-3 text-uptval-orange bg-uptval-orange/10 rounded-xl transition-all duration-300 border-l-2 border-uptval-orange shadow-[inset_0px_0px_20px_rgba(217,123,41,0.05)]">
                    <i class="ph ph-buildings text-xl"></i>
                    <span class="font-medium text-sm">Gestión de Departamentos</span>
                </a>

                <a href="/materias" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
                    <i class="ph ph-books text-xl"></i>
                    <span class="font-medium text-sm">Materias</span>
                </a>

                <a href="/personal" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
                    <i class="ph ph-users text-xl"></i>
                    <span class="font-medium text-sm">Gestión de Personal</span>
                </a>
                <?php endif; ?>

                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
                    <i class="ph ph-student text-xl"></i>
                    <span class="font-medium text-sm">Estudiantes</span>
                </a>

                <?php if (in_array('Administrador', $userRoles)): ?>
                <a href="/aulas" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
                    <i class="ph ph-door text-xl"></i>
                    <span class="font-medium text-sm">Aulas y Laboratorios</span>
                </a>
                <?php endif; ?>
                <?php if (in_array('Administrador', $userRoles)): ?>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300 border-l-2 border-transparent hover:border-gray-500">
                    <i class="ph ph-student text-xl"></i>
                    <span class="font-medium text-sm">Estudiantes</span>
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
                <a href="/departamentos" class="flex items-center gap-3 px-4 py-3 text-uptval-orange bg-uptval-orange/10 rounded-xl transition-all duration-300 border-l-2 border-uptval-orange">
                    <i class="ph ph-buildings text-xl"></i>
                    <span class="font-medium text-sm">Gestión de Departamentos</span>
                </a>
                <a href="/materias" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-300">
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
                    <h2 class="text-2xl font-bold text-gray-900">Gestión de Departamentos</h2>
                    <p class="text-sm text-gray-500 mt-1">Directorio general de los departamentos de la institución.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <a href="/departamentos/export-pdf"
                       class="bg-white border border-slate-300 text-slate-700 px-4 py-2.5 rounded-lg font-medium shadow-sm transition-all flex items-center justify-center gap-2 hover:bg-slate-50 w-full sm:w-auto">
                        <i class="ph ph-file-pdf text-lg text-red-500"></i>
                        Exportar PDF
                    </a>
                </div>
            </header>

            <div class="p-4 sm:p-6 md:p-8 flex-1">
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    
                    <div class="px-4 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                            <i class="ph ph-buildings text-xl text-uptval-orange"></i>
                            Directorio de Departamentos
                        </h3>
                        <span class="text-xs font-medium text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                            <?= count($departamentos) ?> departamentos
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Departamento</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">N.º de Personal</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <?php foreach ($departamentos as $depto): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-9 w-9 rounded-lg bg-uptval-orange/10 flex items-center justify-center mr-3 shrink-0">
                                                <i class="ph ph-buildings text-lg text-uptval-orange"></i>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($depto['name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($depto['status'] === 'activo'): ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                            Activo
                                        </span>
                                        <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            Inactivo
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-gray-900"><?php echo (int) $depto['staff_count']; ?></span>
                                        <span class="text-xs text-gray-400 ml-1">personas</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" onclick="viewCoordinator(<?php echo (int) $depto['id_department']; ?>)"
                                                    class="text-slate-400 hover:text-uptval-orange transition-colors p-1" title="Ver coordinador del departamento">
                                                <i class="ph ph-eye text-xl"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="modalCoordinador" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[100] transition-opacity p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto transform scale-95 transition-transform modal-content">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 text-gray-800">
                <div>
                    <h3 class="text-lg font-bold">Coordinador del Departamento</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Información de la asignación de coordinación.</p>
                </div>
                <button onclick="toggleModal('modalCoordinador')" class="text-gray-400 hover:text-red-500 transition-colors"><i class="ph ph-x text-xl"></i></button>
            </div>
            <div class="p-6">
                <div id="coord_no_result" class="hidden flex items-start gap-2 rounded-lg border border-amber-200 bg-amber-50 text-amber-700 px-4 py-3 text-sm">
                    <i class="ph ph-warning-circle text-lg mt-0.5"></i>
                    <span>Este departamento no tiene coordinador asignado.</span>
                </div>
                <div id="coord_result" class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                        <div id="coord_department" class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-700 text-sm"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Coordinador</label>
                        <div id="coord_name" class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-700 text-sm"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cédula</label>
                        <div id="coord_cedula" class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-700 text-sm font-mono"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Asignación</label>
                        <div id="coord_date" class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-700 text-sm"></div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-between gap-3 rounded-b-2xl">
                <a id="coordPdfButton" href="#" class="px-4 py-2 bg-slate-900 hover:bg-black text-white font-medium rounded-lg shadow-md text-center flex items-center justify-center gap-2">
                    <i class="ph ph-file-pdf text-lg text-red-500"></i> Descargar PDF
                </a>
                <button type="button" onclick="toggleModal('modalCoordinador')" class="px-4 py-2 text-gray-600 font-medium hover:bg-gray-200 rounded-lg">Cerrar</button>
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

        // =========================================================
        // LÓGICA DEL MODAL
        // =========================================================
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
        // LÓGICA DE VER COORDINADOR (MODAL)
        // =========================================================
        const departmentData = <?php echo json_encode($departamentos, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

        function viewCoordinator(id) {
            const record = departmentData.find(function(item) {
                return parseInt(item.id_department, 10) === parseInt(id, 10);
            });

            if (!record) {
                showToast('error', 'Departamento no encontrado', 'El departamento solicitado no está en la página actual.');
                return;
            }

            document.getElementById('coord_department').innerText = record.name || '';
            document.getElementById('coord_name').innerText = record.coordinator_first_name
                ? ((record.coordinator_last_name || '') + ', ' + record.coordinator_first_name)
                : '-';
            document.getElementById('coord_cedula').innerText = record.coordinator_cedula || '-';
            document.getElementById('coord_date').innerText = record.coordinator_assignment_date
                ? new Date(record.coordinator_assignment_date.replace(' ', 'T')).toLocaleDateString('es-VE', {
                    day: '2-digit', month: '2-digit', year: 'numeric'
                  })
                : '-';

            const pdfButton = document.getElementById('coordPdfButton');
            if (record.coordinator_cedula) {
                pdfButton.classList.remove('hidden');
                pdfButton.href = '/departamentos/export-pdf-coordinator?id_department=' + encodeURIComponent(record.id_department);
            } else {
                pdfButton.classList.add('hidden');
            }

            const noResult = document.getElementById('coord_no_result');
            if (record.coordinator_cedula) {
                noResult.classList.add('hidden');
            } else {
                noResult.classList.remove('hidden');
            }

            toggleModal('modalCoordinador');
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
