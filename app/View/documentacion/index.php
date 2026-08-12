<?php
/**
 * Manual de Usuario - Sistema de Gestion Academica UPTVal
 * @var string $cedula
 * @var array $userRoles
 */
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Manual de Usuario - UPTVal</title>
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
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #334155; }
        html { scroll-behavior: smooth; }
        .glass-panel {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    <nav class="glass-panel border-b border-gray-800 sticky top-0 z-30">
        <div class="flex items-center justify-between px-4 sm:px-6 py-3">
            <div class="flex items-center gap-3">
                <i class="ph ph-book-open-text text-2xl text-uptval-orange"></i>
                <h1 class="text-lg sm:text-xl font-bold text-white">
                    Manual de Usuario <span class="hidden sm:inline">· Sistema de Gestión Académica</span>
                </h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="/dashboard" class="hidden sm:flex bg-uptval-orange/10 hover:bg-uptval-orange/20 text-uptval-orange border border-uptval-orange/20 transition-colors duration-300 px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium items-center gap-2">
                    <i class="ph ph-squares-four text-lg"></i>
                    <span class="hidden sm:inline">Volver al Inicio</span>
                </a>
                <form action="/logout" method="POST" class="m-0">
                    <button type="submit" class="bg-red-500/10 hover:bg-red-500/20 text-red-500 border border-red-500/20 transition-colors duration-300 px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium flex items-center gap-1 sm:gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="hidden sm:inline">Salir</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="flex-1 px-4 sm:px-6 lg:px-10 py-8 max-w-7xl mx-auto w-full">

        <!-- HERO -->
        <section class="mb-10">
            <div class="relative bg-slate-900 rounded-3xl overflow-hidden p-8 sm:p-12">
                <div class="absolute top-[-30%] right-[-5%] w-80 h-80 bg-uptval-orange rounded-full mix-blend-screen filter blur-[130px] opacity-20 pointer-events-none"></div>
                <div class="relative">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="px-3 py-1 bg-uptval-orange text-white text-xs font-bold rounded-full">Manual de Usuario</span>
                        <span class="px-3 py-1 bg-white/10 text-gray-300 text-xs font-medium rounded-full border border-white/10">Versión v1.0.3</span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-white leading-tight">
                        Bienvenido a <span class="text-uptval-orange">UPTVal</span> Gestión Académica
                    </h2>
                    <p class="mt-4 text-gray-300 text-sm sm:text-base max-w-3xl leading-relaxed">
                        Esta guía describe todo lo que el sistema es capaz de hacer: autenticación segura, panel de métricas,
                        gestión del personal académico, departamentos, aulas con códigos QR, materias, roles y el listado de
                        estudiantes. Utilice el índice para ir directamente a la sección que necesite.
                    </p>
                    <p class="mt-4 text-xs text-gray-500">
                        Sesión activa · C.I: <?php echo htmlspecialchars($cedula ?? '---'); ?>
                    </p>
                </div>
            </div>
        </section>

        <!-- INDICE -->
        <section class="mb-10">
            <h3 class="text-xl font-bold text-gray-900 mb-5 flex items-center gap-2">
                <i class="ph ph-list-bullets text-uptval-orange"></i> Contenido de esta guía
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="#acceso" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-uptval-orange/40 hover:-translate-y-0.5 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0"><i class="ph ph-lock-key text-xl"></i></div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm group-hover:text-uptval-orange transition-colors">Acceso y Seguridad</p>
                            <p class="text-xs text-gray-500">Inicio de sesión, recordarme y recuperación</p>
                        </div>
                    </div>
                </a>
                <a href="#dashboard" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-uptval-orange/40 hover:-translate-y-0.5 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0"><i class="ph ph-squares-four text-xl"></i></div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm group-hover:text-uptval-orange transition-colors">Dashboard</p>
                            <p class="text-xs text-gray-500">Métricas y reportes del sistema</p>
                        </div>
                    </div>
                </a>
                <a href="#personal" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-uptval-orange/40 hover:-translate-y-0.5 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0"><i class="ph ph-users text-xl"></i></div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm group-hover:text-uptval-orange transition-colors">Gestión de Personal</p>
                            <p class="text-xs text-gray-500">Docentes y administrativos</p>
                        </div>
                    </div>
                </a>
                <a href="#roles" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-uptval-orange/40 hover:-translate-y-0.5 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0"><i class="ph ph-shield-check text-xl"></i></div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm group-hover:text-uptval-orange transition-colors">Permisos y Roles</p>
                            <p class="text-xs text-gray-500">Asignación de roles a usuarios</p>
                        </div>
                    </div>
                </a>
                <a href="#asignacion" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-uptval-orange/40 hover:-translate-y-0.5 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center flex-shrink-0"><i class="ph ph-books text-xl"></i></div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm group-hover:text-uptval-orange transition-colors">Asignación Académica</p>
                            <p class="text-xs text-gray-500">Materias asignadas a docentes</p>
                        </div>
                    </div>
                </a>
                <a href="#departamentos" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-uptval-orange/40 hover:-translate-y-0.5 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center flex-shrink-0"><i class="ph ph-buildings text-xl"></i></div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm group-hover:text-uptval-orange transition-colors">Departamentos</p>
                            <p class="text-xs text-gray-500">Unidades y coordinadores</p>
                        </div>
                    </div>
                </a>
                <a href="#aulas" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-uptval-orange/40 hover:-translate-y-0.5 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-orange-100 text-uptval-orange flex items-center justify-center flex-shrink-0"><i class="ph ph-door text-xl"></i></div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm group-hover:text-uptval-orange transition-colors">Aulas y Laboratorios</p>
                            <p class="text-xs text-gray-500">Espacios y códigos QR</p>
                        </div>
                    </div>
                </a>
                <a href="#materias" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-uptval-orange/40 hover:-translate-y-0.5 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0"><i class="ph ph-book-bookmark text-xl"></i></div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm group-hover:text-uptval-orange transition-colors">Materias, Trayectos y Especialidades</p>
                            <p class="text-xs text-gray-500">Pénsum académico</p>
                        </div>
                    </div>
                </a>
                <a href="#estudiantes" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-uptval-orange/40 hover:-translate-y-0.5 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0"><i class="ph ph-student text-xl"></i></div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm group-hover:text-uptval-orange transition-colors">Estudiantes</p>
                            <p class="text-xs text-gray-500">Matrícula y exportación PDF</p>
                        </div>
                    </div>
                </a>
                <a href="#pdf" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-uptval-orange/40 hover:-translate-y-0.5 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-red-100 text-red-500 flex items-center justify-center flex-shrink-0"><i class="ph ph-file-pdf text-xl"></i></div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm group-hover:text-uptval-orange transition-colors">Exportaciones PDF</p>
                            <p class="text-xs text-gray-500">Todos los reportes disponibles</p>
                        </div>
                    </div>
                </a>
            </div>
        </section>

        <!-- ACCESO Y SEGURIDAD -->
        <section id="acceso" class="scroll-mt-20 mb-10">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0"><i class="ph ph-lock-key text-2xl"></i></div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Acceso y Seguridad</h3>
                        <p class="text-sm text-gray-500">Cómo ingresar y mantener protegida la cuenta</p>
                    </div>
                </div>
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="border border-slate-100 rounded-xl p-5">
                            <p class="font-bold text-gray-900 flex items-center gap-2"><i class="ph ph-sign-in text-uptval-orange"></i> Iniciar sesión</p>
                            <ul class="mt-3 space-y-2 text-sm text-gray-600">
                                <li class="flex gap-2"><span class="text-uptval-orange font-bold">1.</span> Ingrese su <strong>cédula</strong> y <strong>contraseña</strong>.</li>
                                <li class="flex gap-2"><span class="text-uptval-orange font-bold">2.</span> Marque <strong>"Recordarme"</strong> para mantener la sesión por 30 días.</li>
                                <li class="flex gap-2"><span class="text-uptval-orange font-bold">3.</span> Al ingresar se registra automáticamente su última conexión.</li>
                            </ul>
                        </div>
                        <div class="border border-slate-100 rounded-xl p-5">
                            <p class="font-bold text-gray-900 flex items-center gap-2"><i class="ph ph-password text-uptval-orange"></i> Recuperar contraseña</p>
                            <ul class="mt-3 space-y-2 text-sm text-gray-600">
                                <li class="flex gap-2"><span class="text-uptval-orange font-bold">1.</span> Pulse <strong>"¿Olvidó su contraseña?"</strong> en el login.</li>
                                <li class="flex gap-2"><span class="text-uptval-orange font-bold">2.</span> Ingrese su cédula. Si es válida, recibirá un correo.</li>
                                <li class="flex gap-2"><span class="text-uptval-orange font-bold">3.</span> El enlace del correo caduca a los <strong>15 minutos</strong>.</li>
                                <li class="flex gap-2"><span class="text-uptval-orange font-bold">4.</span> La nueva contraseña debe tener al menos <strong>8 caracteres</strong>.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800 flex gap-3">
                        <i class="ph ph-warning-circle text-xl flex-shrink-0"></i>
                        <p>Las cuentas en estado <strong>pendiente</strong> no pueden ingresar hasta ser activadas. Las cuentas <strong>inactivas o bloqueadas</strong> reciben aviso al intentar entrar. La sesión se cierra con el botón <strong>Salir</strong> del menú superior.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- DASHBOARD -->
        <section id="dashboard" class="scroll-mt-20 mb-10">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0"><i class="ph ph-squares-four text-2xl"></i></div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Dashboard</h3>
                        <p class="text-sm text-gray-500">Panel principal con las métricas del sistema</p>
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <ul class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-sm text-gray-600">
                        <li class="flex items-start gap-3 border border-slate-100 rounded-xl p-4"><i class="ph ph-student text-xl text-blue-600 flex-shrink-0"></i><span><strong>Total de estudiantes</strong> registrados, con acceso directo al módulo de matrícula.</span></li>
                        <li class="flex items-start gap-3 border border-slate-100 rounded-xl p-4"><i class="ph ph-users text-xl text-sky-600 flex-shrink-0"></i><span><strong>Total de personal</strong> docente y administrativo del sistema.</span></li>
                        <li class="flex items-start gap-3 border border-slate-100 rounded-xl p-4"><i class="ph ph-buildings text-xl text-purple-600 flex-shrink-0"></i><span><strong>Departamentos</strong> creados en la institución.</span></li>
                        <li class="flex items-start gap-3 border border-slate-100 rounded-xl p-4"><i class="ph ph-chart-pie-slice text-xl text-uptval-orange flex-shrink-0"></i><span><strong>Desglose analítico</strong> con gráficos de estado estudiantil (regulares vs. inactivos) y reporte de asistencia. Use las flechas para desplazarse.</span></li>
                        <li class="flex items-start gap-3 border border-slate-100 rounded-xl p-4"><i class="ph ph-student text-xl text-indigo-600 flex-shrink-0"></i><span><strong>Estado estudiantil</strong>: anillo con el porcentaje de alumnos regulares e inactivos en tiempo real.</span></li>
                        <li class="flex items-start gap-3 border border-slate-100 rounded-xl p-4"><i class="ph ph-mouse-click text-xl text-slate-500 flex-shrink-0"></i><span>Todas las tarjetas del panel son <strong>enlaces</strong> que lo llevan al módulo correspondiente.</span></li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- GESTION DE PERSONAL -->
        <section id="personal" class="scroll-mt-20 mb-10">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0"><i class="ph ph-users text-2xl"></i></div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Gestión de Personal</h3>
                        <p class="text-sm text-gray-500">Directorio general de docentes y administrativos</p>
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <p class="text-sm text-gray-600 mb-5">Disponible en la ruta <span class="font-mono text-xs bg-slate-100 px-2 py-1 rounded">/personal</span>. Permite administrar el talento humano de la universidad:</p>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div class="border border-slate-100 rounded-xl p-5">
                            <p class="font-bold text-gray-900 flex items-center gap-2 mb-3"><i class="ph ph-user-plus text-emerald-600"></i> Registrar personal</p>
                            <ul class="space-y-2">
                                <li>· Cédula, nombres, correo y datos de contacto.</li>
                                <li>· Tipo (docente/administrativo), departamento, condición y tipo de contrato.</li>
                                <li>· Se crea automáticamente el acceso del usuario al sistema.</li>
                            </ul>
                        </div>
                        <div class="border border-slate-100 rounded-xl p-5">
                            <p class="font-bold text-gray-900 flex items-center gap-2 mb-3"><i class="ph ph-sliders-horizontal text-uptval-orange"></i> Filtrar y buscar</p>
                            <ul class="space-y-2">
                                <li>· Por departamento, tipo, condición, contrato o texto (cédula/nombre).</li>
                                <li>· Cambiar estatus (activo/inactivo) y editar registros.</li>
                                <li>· Exportar el directorio en PDF con los filtros aplicados.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PERMISOS Y ROLES -->
        <section id="roles" class="scroll-mt-20 mb-10">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0"><i class="ph ph-shield-check text-2xl"></i></div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Permisos y Roles</h3>
                        <p class="text-sm text-gray-500">Control de acceso por perfiles</p>
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <p class="text-sm text-gray-600 mb-5">Disponible en <span class="font-mono text-xs bg-slate-100 px-2 py-1 rounded">/personal/permisos-roles</span>. Define qué puede hacer cada usuario:</p>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div class="border border-slate-100 rounded-xl p-5">
                            <p class="font-bold text-gray-900 flex items-center gap-2 mb-3"><i class="ph ph-tag text-uptval-orange"></i> Roles del sistema</p>
                            <ul class="space-y-2">
                                <li>· <strong>Administrador</strong>: acceso a todos los módulos.</li>
                                <li>· <strong>Control de Estudio</strong>: secciones y métricas estudiantiles.</li>
                                <li>· Otros roles personalizados según el perfil del usuario.</li>
                            </ul>
                        </div>
                        <div class="border border-slate-100 rounded-xl p-5">
                            <p class="font-bold text-gray-900 flex items-center gap-2 mb-3"><i class="ph ph-user-switch text-blue-600"></i> Asignar a usuarios</p>
                            <ul class="space-y-2">
                                <li>· Busque por cédula al personal registrado.</li>
                                <li>· Asigne o retire roles de uno o varios usuarios.</li>
                                <li>· Active/desactive roles sin eliminar los registros.</li>
                                <li>· Exporte la lista de permisos en PDF.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ASIGNACION ACADEMICA -->
        <section id="asignacion" class="scroll-mt-20 mb-10">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center flex-shrink-0"><i class="ph ph-books text-2xl"></i></div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Asignación Académica</h3>
                        <p class="text-sm text-gray-500">Materias que dicta cada docente</p>
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <p class="text-sm text-gray-600 mb-5">Disponible en <span class="font-mono text-xs bg-slate-100 px-2 py-1 rounded">/personal/asignacion-academica</span>:</p>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div class="border border-slate-100 rounded-xl p-5">
                            <p class="font-bold text-gray-900 flex items-center gap-2 mb-3"><i class="ph ph-plus-circle text-emerald-600"></i> Asignar materia</p>
                            <ul class="space-y-2">
                                <li>· Busque al docente por su cédula.</li>
                                <li>· Seleccione la materia del pénsum.</li>
                                <li>· La asignación queda vinculada al docente y visible en su perfil.</li>
                            </ul>
                        </div>
                        <div class="border border-slate-100 rounded-xl p-5">
                            <p class="font-bold text-gray-900 flex items-center gap-2 mb-3"><i class="ph ph-export text-red-500"></i> Administrar asignaciones</p>
                            <ul class="space-y-2">
                                <li>· Retire asignaciones cuando el docente deje de dictar la materia.</li>
                                <li>· Active/desactive registros según el período académico.</li>
                                <li>· Exporte el reporte completo en PDF.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- DEPARTAMENTOS -->
        <section id="departamentos" class="scroll-mt-20 mb-10">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center flex-shrink-0"><i class="ph ph-buildings text-2xl"></i></div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Departamentos</h3>
                        <p class="text-sm text-gray-500">Unidades académicas y coordinadores</p>
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <p class="text-sm text-gray-600 mb-5">Disponible en <span class="font-mono text-xs bg-slate-100 px-2 py-1 rounded">/departamentos</span>:</p>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div class="border border-slate-100 rounded-xl p-5">
                            <p class="font-bold text-gray-900 flex items-center gap-2 mb-3"><i class="ph ph-building-office text-uptval-orange"></i> Gestionar departamentos</p>
                            <ul class="space-y-2">
                                <li>· Crear, editar, activar e inactivar departamentos.</li>
                                <li>· Asignar un <strong>coordinador</strong> a cada departamento.</li>
                                <li>· Los departamentos agrupan aulas, materias y personal.</li>
                            </ul>
                        </div>
                        <div class="border border-slate-100 rounded-xl p-5">
                            <p class="font-bold text-gray-900 flex items-center gap-2 mb-3"><i class="ph ph-file-pdf text-red-500"></i> Reportes</p>
                            <ul class="space-y-2">
                                <li>· Directorio de departamentos en PDF.</li>
                                <li>· Ficha individual del coordinador en PDF.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- AULAS Y LABORATORIOS -->
        <section id="aulas" class="scroll-mt-20 mb-10">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-orange-100 text-uptval-orange flex items-center justify-center flex-shrink-0"><i class="ph ph-door text-2xl"></i></div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Aulas y Laboratorios</h3>
                        <p class="text-sm text-gray-500">Espacios físicos con identificación QR</p>
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <p class="text-sm text-gray-600 mb-5">Disponible en <span class="font-mono text-xs bg-slate-100 px-2 py-1 rounded">/aulas</span>:</p>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div class="border border-slate-100 rounded-xl p-5">
                            <p class="font-bold text-gray-900 flex items-center gap-2 mb-3"><i class="ph ph-plus-circle text-emerald-600"></i> Registrar espacios</p>
                            <ul class="space-y-2">
                                <li>· Crear aulas o laboratorios con código único, nombre y tipo.</li>
                                <li>· Vincular cada espacio a su departamento.</li>
                                <li>· Editar y activar/inactivar espacios.</li>
                            </ul>
                        </div>
                        <div class="border border-slate-100 rounded-xl p-5">
                            <p class="font-bold text-gray-900 flex items-center gap-2 mb-3"><i class="ph ph-qr-code text-uptval-orange"></i> Códigos QR</p>
                            <ul class="space-y-2">
                                <li>· Genere el <strong>código QR</strong> de cualquier aula o laboratorio.</li>
                                <li>· Visualícelo en pantalla (SVG) o descárguelo como <strong>PDF</strong> para imprimir e identificar físicamente el espacio.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- MATERIAS -->
        <section id="materias" class="scroll-mt-20 mb-10">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0"><i class="ph ph-book-bookmark text-2xl"></i></div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Materias, Trayectos y Especialidades</h3>
                        <p class="text-sm text-gray-500">Pénsum académico de la universidad</p>
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <p class="text-sm text-gray-600 mb-5">Disponible en <span class="font-mono text-xs bg-slate-100 px-2 py-1 rounded">/materias</span> con sus pestañas de trayectos y especialidades:</p>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 text-sm text-gray-600">
                        <div class="border border-slate-100 rounded-xl p-5">
                            <p class="font-bold text-gray-900 flex items-center gap-2 mb-3"><i class="ph ph-book-bookmark text-rose-600"></i> Materias</p>
                            <ul class="space-y-2">
                                <li>· Crear, editar y gestionar materias.</li>
                                <li>· Asociar cada materia a un trayecto y especialidad.</li>
                            </ul>
                        </div>
                        <div class="border border-slate-100 rounded-xl p-5">
                            <p class="font-bold text-gray-900 flex items-center gap-2 mb-3"><i class="ph ph-graduation-cap text-indigo-600"></i> Trayectos</p>
                            <ul class="space-y-2">
                                <li>· Administrar los trayectos (Trayecto Inicial, I, II, III, IV...).</li>
                                <li>· Activar o desactivar trayectos según el plan de estudio.</li>
                            </ul>
                        </div>
                        <div class="border border-slate-100 rounded-xl p-5">
                            <p class="font-bold text-gray-900 flex items-center gap-2 mb-3"><i class="ph ph-identification-badge text-teal-600"></i> Especialidades</p>
                            <ul class="space-y-2">
                                <li>· Gestionar las especialidades de cada trayecto.</li>
                                <li>· Vincular materias y estudiantes a la especialidad correspondiente.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ESTUDIANTES -->
        <section id="estudiantes" class="scroll-mt-20 mb-10">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0"><i class="ph ph-student text-2xl"></i></div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Estudiantes</h3>
                        <p class="text-sm text-gray-500">Matrícula estudiantil de solo lectura</p>
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-5 bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800">
                        <i class="ph ph-info text-xl flex-shrink-0"></i>
                        <p><strong>Módulo de solo lectura:</strong> el registro de estudiantes se realiza desde el sistema de inscripción externo. Este módulo permite consultar la matrícula.</p>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div class="border border-slate-100 rounded-xl p-5">
                            <p class="font-bold text-gray-900 flex items-center gap-2 mb-3"><i class="ph ph-funnel text-uptval-orange"></i> Consultar y filtrar</p>
                            <ul class="space-y-2">
                                <li>· Filtre por <strong>trayecto</strong>, <strong>especialidad</strong> y <strong>estatus</strong>.</li>
                                <li>· Busque por cédula o nombre con el buscador.</li>
                                <li>· Resultados ordenados por apellido y paginados.</li>
                            </ul>
                        </div>
                        <div class="border border-slate-100 rounded-xl p-5">
                            <p class="font-bold text-gray-900 flex items-center gap-2 mb-3"><i class="ph ph-file-pdf text-red-500"></i> Matrícula en PDF</p>
                            <ul class="space-y-2">
                                <li>· Pulse <strong>Exportar PDF</strong> para descargar la matrícula.</li>
                                <li>· El reporte respeta los filtros activos de la vista.</li>
                                <li>· Incluye cédula, nombres, trayecto, especialidad, sección, contacto y estatus.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- EXPORTACIONES PDF -->
        <section id="pdf" class="scroll-mt-20 mb-10">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-red-100 text-red-500 flex items-center justify-center flex-shrink-0"><i class="ph ph-file-pdf text-2xl"></i></div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Exportaciones PDF</h3>
                        <p class="text-sm text-gray-500">Reportes disponibles en todo el sistema</p>
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-slate-900">
                                <tr class="text-xs font-medium text-white uppercase tracking-wider">
                                    <th scope="col" class="px-4 py-3 text-left">Reporte</th>
                                    <th scope="col" class="px-4 py-3 text-left">Cómo obtenerlo</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <tr><td class="px-4 py-3 font-medium text-gray-900"><i class="ph ph-student text-blue-600 mr-2"></i>Matrícula estudiantil</td><td class="px-4 py-3 text-gray-600">Botón <strong>Exportar PDF</strong> en Estudiantes</td></tr>
                                <tr class="bg-gray-50/50"><td class="px-4 py-3 font-medium text-gray-900"><i class="ph ph-users text-sky-600 mr-2"></i>Directorio de personal</td><td class="px-4 py-3 text-gray-600">Botón <strong>Exportar PDF</strong> en Gestión de Personal</td></tr>
                                <tr><td class="px-4 py-3 font-medium text-gray-900"><i class="ph ph-user text-emerald-600 mr-2"></i>Ficha de personal</td><td class="px-4 py-3 text-gray-600">Acción <strong>Ver PDF</strong> de cada registro en Personal</td></tr>
                                <tr class="bg-gray-50/50"><td class="px-4 py-3 font-medium text-gray-900"><i class="ph ph-shield-check text-emerald-600 mr-2"></i>Permisos y roles</td><td class="px-4 py-3 text-gray-600">Botón <strong>Exportar PDF</strong> en Permisos y Roles</td></tr>
                                <tr><td class="px-4 py-3 font-medium text-gray-900"><i class="ph ph-books text-teal-600 mr-2"></i>Asignación académica</td><td class="px-4 py-3 text-gray-600">Botón <strong>Exportar PDF</strong> en Asignación Académica</td></tr>
                                <tr class="bg-gray-50/50"><td class="px-4 py-3 font-medium text-gray-900"><i class="ph ph-buildings text-purple-600 mr-2"></i>Directorio de departamentos</td><td class="px-4 py-3 text-gray-600">Botón <strong>Exportar PDF</strong> en Departamentos</td></tr>
                                <tr><td class="px-4 py-3 font-medium text-gray-900"><i class="ph ph-user-circle text-purple-600 mr-2"></i>Ficha del coordinador</td><td class="px-4 py-3 text-gray-600">Acción <strong>PDF coordinador</strong> en Departamentos</td></tr>
                                <tr class="bg-gray-50/50"><td class="px-4 py-3 font-medium text-gray-900"><i class="ph ph-qr-code text-uptval-orange mr-2"></i>Código QR de aula</td><td class="px-4 py-3 text-gray-600">Acción <strong>QR PDF</strong> en Aulas y Laboratorios</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-4 text-xs text-gray-500 flex items-center gap-2"><i class="ph ph-lightbulb text-uptval-orange text-base"></i> Todos los reportes incluyen encabezado UPTVal, datos del generador, fecha, paginación y respetan los filtros seleccionados en la pantalla.</p>
                </div>
            </div>
        </section>

        <!-- PIE -->
        <footer class="mt-10 pb-6 text-center">
            <div class="inline-flex items-center gap-2 px-5 py-3 bg-white border border-slate-200 rounded-full shadow-sm text-sm text-gray-500">
                <i class="ph ph-book-open-text text-uptval-orange"></i>
                Manual de Usuario · Sistema de Gestión Académica UPTVal · Versión v1.0.3
            </div>
        </footer>

    </main>
</body>
</html>
