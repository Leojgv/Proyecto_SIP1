<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Gestión de Inclusión</title>
    <style>
        body { 
            font-family: 'DejaVu Sans', 'Helvetica', sans-serif; 
            color: #333; 
            font-size: 12px; 
            margin: 0;
            padding: 20px;
        }
        
        /* Encabezado */
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
            border-bottom: 2px solid #b91d47; 
            padding-bottom: 10px; 
        }
        .header h1 { 
            margin: 5px 0; 
            font-size: 18px; 
            color: #b91d47; 
            font-weight: bold;
        }
        .header p { 
            margin: 5px 0; 
            color: #666; 
            font-size: 11px;
        }

        /* Tarjetas de Resumen (KPIs) */
        .kpi-container { 
            width: 100%; 
            overflow: hidden; 
            margin-bottom: 30px; 
        }
        .kpi-card { 
            float: left; 
            width: 31%; 
            background: #f4f4f4; 
            padding: 18px 8px; 
            margin-right: 3.5%; 
            border-radius: 5px; 
            text-align: center; 
            border: 1px solid #ddd;
            min-height: 95px;
            box-sizing: border-box;
        }
        .kpi-card:last-child { 
            margin-right: 0; 
        }
        .kpi-number { 
            font-size: 26px; 
            font-weight: bold; 
            color: #b91d47; 
            display: block; 
            line-height: 1.2;
            margin-bottom: 10px;
        }
        .kpi-label { 
            font-size: 8px; 
            text-transform: uppercase; 
            color: #555; 
            line-height: 1.4;
            word-wrap: break-word;
            padding: 0 3px;
            letter-spacing: 0.3px;
        }

        /* Títulos de Sección */
        h2 { 
            font-size: 14px; 
            border-left: 5px solid #b91d47; 
            padding-left: 10px; 
            margin-top: 20px; 
            background-color: #eee; 
            padding: 8px 10px; 
            margin-bottom: 15px;
            font-weight: bold;
        }

        /* Gráficas */
        .chart-container {
            margin: 20px 0;
            text-align: center;
            page-break-inside: avoid;
            padding: 15px;
            background: #fafafa;
            border-radius: 5px;
        }
        .chart-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        /* Tablas */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        th { 
            background-color: #b91d47; 
            color: white; 
            padding: 8px; 
            text-align: left; 
            font-size: 11px; 
            font-weight: bold;
        }
        td { 
            border-bottom: 1px solid #ddd; 
            padding: 8px; 
            font-size: 11px; 
        }
        tr:nth-child(even) { 
            background-color: #f9f9f9; 
        }

        /* Estados (Badges simulados) */
        .status-aprobado { 
            color: green; 
            font-weight: bold; 
        }
        .status-rechazado { 
            color: red; 
            font-weight: bold; 
        }
        .status-pendiente { 
            color: orange; 
            font-weight: bold; 
        }

        /* Ajustes Razonables */
        .ajuste-section {
            margin-bottom: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #b91d47;
            border-radius: 3px;
        }
        .ajuste-title {
            font-size: 13px;
            font-weight: bold;
            color: #b91d47;
            margin-bottom: 10px;
        }
        .estudiante-item {
            padding: 4px 0;
            padding-left: 20px;
            font-size: 11px;
            color: #555;
        }
        .estudiante-item::before {
            content: "• ";
            color: #b91d47;
            font-weight: bold;
        }

        /* Pie de página */
        .footer { 
            position: fixed; 
            bottom: 0; 
            left: 0; 
            right: 0; 
            text-align: center; 
            font-size: 10px; 
            color: #999; 
            border-top: 1px solid #ddd; 
            padding-top: 10px; 
            background: white;
        }

        .page-break {
            page-break-before: always;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>INFORME DE GESTIÓN DE AJUSTES RAZONABLES</h1>
        <p><strong>Sistema SIP</strong></p>
        <p>Generado por: {{ auth()->user()->nombre ?? auth()->user()->name }} {{ auth()->user()->apellido ?? '' }} | Fecha: {{ $fechaGeneracion }}</p>
    </div>

    {{-- KPIs --}}
    <div class="kpi-container clearfix">
        <div class="kpi-card" style="width: 48%;">
            <span class="kpi-number">{{ $totalSolicitudes }}</span>
            <span class="kpi-label">Total Solicitudes</span>
        </div>
        <div class="kpi-card" style="width: 48%; margin-right: 0;">
            <span class="kpi-number">{{ $porcentajeAprobacion }}%</span>
            <span class="kpi-label">Tasa de Aprobación</span>
        </div>
    </div>

    {{-- Gráfica de Estudiantes por Carrera --}}
    <h2>Distribución de Estudiantes por Carrera</h2>
    <div class="chart-container">
        <div class="chart-title">Participación de Estudiantes por Carrera</div>
        @php
            $totalEstudiantes = $estudiantesPorCarrera->sum('cantidad');
            $colors = ['#b91d47', '#dc2626', '#ef4444', '#f87171', '#fca5a5', '#fecaca'];
        @endphp
        
        <div style="margin: 20px 0;">
            <div style="width: 100%; height: 40px; background-color: #f0f0f0; border-radius: 20px; overflow: hidden; margin-bottom: 25px; position: relative; border: 2px solid #ddd;">
                @php
                    $currentPosition = 0;
                    $colorIndex = 0;
                @endphp
                @foreach($estudiantesPorCarrera as $item)
                    @php
                        $percentage = $totalEstudiantes > 0 ? ($item['cantidad'] / $totalEstudiantes) * 100 : 0;
                        if ($percentage == 0) continue;
                        $color = $colors[$colorIndex % count($colors)];
                        $colorIndex++;
                    @endphp
                    <div style="position: absolute; left: {{ $currentPosition }}%; width: {{ $percentage }}%; height: 100%; background-color: {{ $color }}; border-right: 2px solid white;"></div>
                    @php
                        $currentPosition += $percentage;
                    @endphp
                @endforeach
            </div>
            
            <table style="width: 100%; border-collapse: collapse;">
                @foreach($estudiantesPorCarrera as $index => $item)
                    @php
                        $color = $colors[$index % count($colors)];
                        $percentage = $totalEstudiantes > 0 ? ($item['cantidad'] / $totalEstudiantes) * 100 : 0;
                    @endphp
                    <tr style="background-color: #fafafa;">
                        <td style="padding: 12px; width: 25px;">
                            <div style="width: 20px; height: 20px; background-color: {{ $color }}; border-radius: 4px; border: 2px solid white; box-shadow: 0 1px 3px rgba(0,0,0,0.2);"></div>
                        </td>
                        <td style="padding: 12px; font-size: 11px; font-weight: bold;">
                            {{ $item['nombre'] }}
                        </td>
                        <td style="padding: 12px; text-align: right; font-size: 11px;">
                            <strong>{{ $item['cantidad'] }}</strong> estudiantes
                        </td>
                        <td style="padding: 12px; text-align: right; font-size: 11px; font-weight: bold; color: #b91d47; width: 80px;">
                            {{ number_format($percentage, 1) }}%
                        </td>
                        <td style="padding: 12px; width: 200px;">
                            <div style="width: 100%; height: 12px; background-color: #e0e0e0; border-radius: 6px; overflow: hidden;">
                                <div style="width: {{ $percentage }}%; height: 100%; background-color: {{ $color }}; border-radius: 6px;"></div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>

    {{-- Gráfica de Docentes por Carrera --}}
    <h2 class="page-break">Distribución de Docentes por Carrera</h2>
    <div class="chart-container">
        <div class="chart-title">Participación de Docentes por Carrera</div>
        @php
            $totalDocentes = $docentesPorCarrera->sum('cantidad');
            $docentesColors = ['#1e40af', '#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#dbeafe'];
        @endphp
        
        <div style="margin: 20px 0;">
            <div style="width: 100%; height: 40px; background-color: #f0f0f0; border-radius: 20px; overflow: hidden; margin-bottom: 25px; position: relative; border: 2px solid #ddd;">
                @php
                    $currentPosition = 0;
                    $colorIndex = 0;
                @endphp
                @foreach($docentesPorCarrera as $item)
                    @php
                        $percentage = $totalDocentes > 0 ? ($item['cantidad'] / $totalDocentes) * 100 : 0;
                        if ($percentage == 0) continue;
                        $color = $docentesColors[$colorIndex % count($docentesColors)];
                        $colorIndex++;
                    @endphp
                    <div style="position: absolute; left: {{ $currentPosition }}%; width: {{ $percentage }}%; height: 100%; background-color: {{ $color }}; border-right: 2px solid white;"></div>
                    @php
                        $currentPosition += $percentage;
                    @endphp
                @endforeach
            </div>
            
            <table style="width: 100%; border-collapse: collapse;">
                @foreach($docentesPorCarrera as $index => $item)
                    @php
                        $color = $docentesColors[$index % count($docentesColors)];
                        $percentage = $totalDocentes > 0 ? ($item['cantidad'] / $totalDocentes) * 100 : 0;
                    @endphp
                    <tr style="background-color: #fafafa;">
                        <td style="padding: 12px; width: 25px;">
                            <div style="width: 20px; height: 20px; background-color: {{ $color }}; border-radius: 4px; border: 2px solid white; box-shadow: 0 1px 3px rgba(0,0,0,0.2);"></div>
                        </td>
                        <td style="padding: 12px; font-size: 11px; font-weight: bold;">
                            {{ $item['nombre'] }}
                        </td>
                        <td style="padding: 12px; text-align: right; font-size: 11px;">
                            <strong>{{ $item['cantidad'] }}</strong> docentes
                        </td>
                        <td style="padding: 12px; text-align: right; font-size: 11px; font-weight: bold; color: #1e40af; width: 80px;">
                            {{ number_format($percentage, 1) }}%
                        </td>
                        <td style="padding: 12px; width: 200px;">
                            <div style="width: 100%; height: 12px; background-color: #e0e0e0; border-radius: 6px; overflow: hidden;">
                                <div style="width: {{ $percentage }}%; height: 100%; background-color: {{ $color }}; border-radius: 6px;"></div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>

    {{-- Gráfica 2: Distribución de Ajustes por Carrera --}}
    <div class="chart-container page-break">
        <div class="chart-title">Distribución de Ajustes por Carrera</div>
        @php
            $totalAjustes = $ajustesPorCarrera->sum('cantidad');
            $colorIndex = 0;
        @endphp
        
        {{-- Representación visual de gráfica de pastel usando barras horizontales --}}
        <div style="margin: 20px 0;">
            {{-- Barra de distribución visual --}}
            <div style="width: 100%; height: 40px; background-color: #f0f0f0; border-radius: 20px; overflow: hidden; margin-bottom: 25px; position: relative; border: 2px solid #ddd;">
                @php
                    $currentPosition = 0;
                    $colorIndex = 0;
                @endphp
                @foreach($ajustesPorCarrera as $item)
                    @php
                        $percentage = $totalAjustes > 0 ? ($item['cantidad'] / $totalAjustes) * 100 : 0;
                        if ($percentage == 0) continue;
                        $color = $colors[$colorIndex % count($colors)];
                        $colorIndex++;
                    @endphp
                    <div style="position: absolute; left: {{ $currentPosition }}%; width: {{ $percentage }}%; height: 100%; background-color: {{ $color }}; border-right: 2px solid white;"></div>
                    @php
                        $currentPosition += $percentage;
                    @endphp
                @endforeach
            </div>
            
            {{-- Leyenda detallada --}}
            <table style="width: 100%; border-collapse: collapse;">
                @foreach($ajustesPorCarrera as $index => $item)
                    @php
                        $color = $colors[$index % count($colors)];
                        $percentage = $totalAjustes > 0 ? ($item['cantidad'] / $totalAjustes) * 100 : 0;
                    @endphp
                    <tr style="background-color: #fafafa;">
                        <td style="padding: 12px; width: 25px;">
                            <div style="width: 20px; height: 20px; background-color: {{ $color }}; border-radius: 4px; border: 2px solid white; box-shadow: 0 1px 3px rgba(0,0,0,0.2);"></div>
                        </td>
                        <td style="padding: 12px; font-size: 11px; font-weight: bold;">
                            {{ $item['nombre'] }}
                        </td>
                        <td style="padding: 12px; text-align: right; font-size: 11px;">
                            <strong>{{ $item['cantidad'] }}</strong> ajustes
                        </td>
                        <td style="padding: 12px; text-align: right; font-size: 11px; font-weight: bold; color: #b91d47; width: 80px;">
                            {{ number_format($percentage, 1) }}%
                        </td>
                        <td style="padding: 12px; width: 200px;">
                            <div style="width: 100%; height: 12px; background-color: #e0e0e0; border-radius: 6px; overflow: hidden;">
                                <div style="width: {{ $percentage }}%; height: 100%; background-color: {{ $color }}; border-radius: 6px;"></div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        
    </div>

    {{-- Distribución por Tipo de Ajuste --}}
    <h2>Distribución por Tipo de Ajuste (Solo Aprobados)</h2>
    <table>
        <thead>
            <tr>
                <th>Tipo de Ajuste Solicitado</th>
                <th style="text-align: right;">Cantidad</th>
                <th style="text-align: right;">Porcentaje</th>
            </tr>
        </thead>
        <tbody>
            @forelse($statsPorTipo as $tipo)
            <tr>
                <td>{{ $tipo->nombre }}</td>
                <td style="text-align: right;">{{ $tipo->cantidad }}</td>
                <td style="text-align: right;">{{ $tipo->porcentaje }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center;">No hay ajustes registrados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Lista de Carreras --}}
    <h2>Lista de Carreras</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre de la Carrera</th>
                <th>Jornada</th>
                <th style="text-align: right;">Total de Estudiantes</th>
                <th style="text-align: right;">Estudiantes con Ajustes</th>
                <th style="text-align: right;">Total de Ajustes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($carreras as $carrera)
                @php
                    $totalEstudiantes = $carrera->estudiantes->count();
                    $estudiantesConAjustes = $carrera->estudiantes->filter(fn($e) => $e->ajustesRazonables->isNotEmpty())->count();
                    $totalAjustes = $carrera->estudiantes->sum(fn($e) => $e->ajustesRazonables->count());
                @endphp
                <tr>
                    <td>{{ $carrera->nombre ?? 'Sin nombre' }}</td>
                    <td>{{ $carrera->jornada ?? 'No definida' }}</td>
                    <td style="text-align: right;">{{ $totalEstudiantes }}</td>
                    <td style="text-align: right;">{{ $estudiantesConAjustes }}</td>
                    <td style="text-align: right;">{{ $totalAjustes }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No hay carreras registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Detalle de Casos del Periodo --}}
    <h2 class="page-break">Detalle de Casos del Periodo</h2>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>RUT Estudiante</th>
                <th>Nombre Estudiante</th>
                <th>Ajustes Aprobados</th>
                <th>Responsable Entrevista</th>
                <th>Responsable Ajuste</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($casosAgrupados ?? [] as $caso)
            <tr>
                <td style="white-space: nowrap;">{{ optional($caso['fecha'])->format('d/m/Y') ?? 'N/A' }}</td>
                <td>{{ $caso['estudiante']->rut ?? 'N/A' }}</td>
                <td style="font-weight: bold;">{{ trim(($caso['estudiante']->nombre ?? '') . ' ' . ($caso['estudiante']->apellido ?? '')) ?: 'N/A' }}</td>
                <td style="font-size: 10px; line-height: 1.4;">
                    @if($caso['ajustes']->count() > 0)
                        @foreach($caso['ajustes'] as $ajuste)
                            • {{ Str::limit($ajuste->nombre, 35) }}@if(!$loop->last)<br>@endif
                        @endforeach
                        <small style="color: #666;">({{ $caso['ajustes']->count() }} ajuste{{ $caso['ajustes']->count() > 1 ? 's' : '' }})</small>
                    @else
                        Sin ajustes
                    @endif
                </td>
                <td style="font-size: 10px;">{{ $caso['responsable_entrevista'] }}</td>
                <td style="font-size: 10px;">{{ $caso['responsable_ajuste'] }}</td>
                <td>
                    <span class="status-aprobado">Aprobado</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">No hay casos aprobados para este periodo.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Ajustes Razonables con Lista de Estudiantes --}}
    <h2 class="page-break">Ajustes Razonables Aplicados</h2>
    @forelse($ajustesConEstudiantes as $ajusteNombre => $ajusteData)
        <div class="ajuste-section">
            <div class="ajuste-title">{{ $ajusteNombre }}</div>
            <div style="margin-bottom: 10px;">
                <div style="font-size: 10px; color: #555; margin-bottom: 5px;">
                    <strong>Descripción:</strong> {{ $ajusteData['descripcion'] ?? 'No descripcion' }}
                </div>
                <div style="font-size: 10px; color: #555; margin-bottom: 10px;">
                    <strong>Fecha de aplicación:</strong> {{ optional($ajusteData['fecha_aplicacion'])->format('d/m/Y') ?? 'No especificada' }}
                </div>
            </div>
            <div>
                @foreach($ajusteData['estudiantes'] ?? [] as $estudiante)
                    <div class="estudiante-item">
                        {{ $estudiante['nombre'] }} - {{ $estudiante['carrera'] }}
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p style="text-align: center; padding: 20px; color: #666;">No hay ajustes razonables aplicados.</p>
    @endforelse

    {{-- Ajustes Rechazados --}}
    <h2 class="page-break">Ajustes Rechazados</h2>
    @forelse($ajustesRechazadosConEstudiantes ?? [] as $ajusteNombre => $ajusteData)
        <div class="ajuste-section" style="border-left-color: #dc3545;">
            <div class="ajuste-title" style="color: #dc3545;">{{ $ajusteNombre }}</div>
            <div style="margin-bottom: 10px;">
                <div style="font-size: 10px; color: #555; margin-bottom: 5px;">
                    <strong>Descripción:</strong> {{ $ajusteData['descripcion'] ?? 'No descripcion' }}
                </div>
                <div style="font-size: 10px; color: #555; margin-bottom: 5px;">
                    <strong>Fecha de rechazo:</strong> {{ optional($ajusteData['fecha_rechazo'])->format('d/m/Y') ?? 'No especificada' }}
                </div>
                <div style="font-size: 10px; color: #dc3545; margin-bottom: 10px; padding: 8px; background-color: #ffeaea; border-left: 3px solid #dc3545; border-radius: 3px;">
                    <strong>Motivo de rechazo:</strong> {{ $ajusteData['motivo_rechazo'] ?? 'No se especificó motivo de rechazo' }}
                </div>
            </div>
            <div>
                @foreach($ajusteData['estudiantes'] ?? [] as $estudiante)
                    <div class="estudiante-item">
                        {{ $estudiante['nombre'] }} - {{ $estudiante['carrera'] }}
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p style="text-align: center; padding: 20px; color: #666;">No hay ajustes rechazados.</p>
    @endforelse

    {{-- TERCERA PÁGINA: Lista Completa de Estudiantes y Docentes --}}
    <div class="page-break">
        <h2>Directorio Completo de Estudiantes</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 25%;">Nombre Completo</th>
                    <th style="width: 15%;">RUT</th>
                    <th style="width: 25%;">Correo Electrónico</th>
                    <th style="width: 15%;">Teléfono</th>
                    <th style="width: 15%;">Carrera</th>
                    <th style="width: 10%; text-align: center;">Ajustes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($estudiantesCompletos ?? [] as $index => $estudiante)
                    <tr>
                        <td style="text-align: center; font-size: 10px; color: #666;">{{ $index + 1 }}</td>
                        <td style="font-weight: bold; font-size: 11px;">{{ $estudiante['nombre'] ?: 'Sin nombre' }}</td>
                        <td style="font-size: 11px;">{{ $estudiante['rut'] }}</td>
                        <td style="font-size: 10px; color: #555;">{{ $estudiante['email'] }}</td>
                        <td style="font-size: 11px;">{{ $estudiante['telefono'] }}</td>
                        <td style="font-size: 10px; color: #555;">{{ $estudiante['carrera'] }}</td>
                        <td style="text-align: center; font-size: 11px;">
                            @if($estudiante['ajustes_count'] > 0)
                                <span style="background-color: #4caf50; color: white; padding: 3px 8px; border-radius: 12px; font-weight: bold;">{{ $estudiante['ajustes_count'] }}</span>
                            @else
                                <span style="color: #999;">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: #666;">No hay estudiantes registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div style="margin-top: 30px; padding: 15px; background-color: #f5f5f5; border-radius: 5px; border-left: 4px solid #b91d47;">
            <div style="font-size: 11px; color: #555;">
                <strong>Total de Estudiantes:</strong> {{ count($estudiantesCompletos ?? []) }}<br>
                <strong>Estudiantes con Ajustes Aprobados:</strong> {{ collect($estudiantesCompletos ?? [])->filter(fn($e) => $e['ajustes_count'] > 0)->count() }}
            </div>
        </div>
    </div>

    <div class="page-break">
        <h2>Directorio Completo de Docentes</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%;">Nombre Completo</th>
                    <th style="width: 20%;">RUT</th>
                    <th style="width: 30%;">Correo Electrónico</th>
                    <th style="width: 15%;">Carrera</th>
                </tr>
            </thead>
            <tbody>
                @forelse($docentesCompletos ?? [] as $index => $docente)
                    <tr>
                        <td style="text-align: center; font-size: 10px; color: #666;">{{ $index + 1 }}</td>
                        <td style="font-weight: bold; font-size: 11px;">{{ $docente['nombre'] ?: 'Sin nombre' }}</td>
                        <td style="font-size: 11px;">{{ $docente['rut'] }}</td>
                        <td style="font-size: 10px; color: #555;">{{ $docente['email'] }}</td>
                        <td style="font-size: 10px; color: #555;">{{ $docente['carrera'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px; color: #666;">No hay docentes registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div style="margin-top: 30px; padding: 15px; background-color: #f5f5f5; border-radius: 5px; border-left: 4px solid #b91d47;">
            <div style="font-size: 11px; color: #555;">
                <strong>Total de Docentes:</strong> {{ count($docentesCompletos ?? []) }}
            </div>
        </div>
    </div>

    <div class="footer">
        Documento generado automáticamente por el Sistema de Gestión de Inclusión (SIP).<br>
        Uso exclusivo interno para la Dirección de Carrera.
    </div>

</body>
</html>
