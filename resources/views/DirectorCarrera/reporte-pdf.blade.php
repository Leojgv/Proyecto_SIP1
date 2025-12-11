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
        <div class="kpi-card">
            <span class="kpi-number">{{ $totalSolicitudes }}</span>
            <span class="kpi-label">Total Solicitudes</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-number">{{ $pendientesAprobacion }}</span>
            <span class="kpi-label">Pendientes de Aprobación</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-number">{{ $porcentajeAprobacion }}%</span>
            <span class="kpi-label">Tasa de Aprobación</span>
        </div>
    </div>

    {{-- Gráficas de Pastel --}}
    <h2>Gráficas de Participación en la Carrera</h2>
    
    {{-- Gráfica 1: Distribución de Estudiantes por Carrera --}}
    <div class="chart-container">
        <div class="chart-title">Distribución de Estudiantes por Carrera</div>
        @php
            $totalEstudiantes = $estudiantesPorCarrera->sum('cantidad');
            $colors = ['#b91d47', '#dc2626', '#ef4444', '#f87171', '#fca5a5', '#fecaca'];
        @endphp
        
        {{-- Representación visual de gráfica de pastel usando barras horizontales --}}
        <div style="margin: 20px 0;">
            {{-- Barra de distribución visual --}}
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
            
            {{-- Leyenda detallada --}}
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
    <h2>Distribución por Tipo de Ajuste</h2>
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
                <th>Ajuste Principal</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($solicitudes as $solicitud)
            <tr>
                <td>{{ optional($solicitud->fecha_solicitud ?? $solicitud->created_at)->format('d/m/Y') ?? 'N/A' }}</td>
                <td>{{ $solicitud->estudiante->rut ?? 'N/A' }}</td>
                <td>{{ trim(($solicitud->estudiante->nombre ?? '') . ' ' . ($solicitud->estudiante->apellido ?? '')) ?: 'N/A' }}</td>
                <td>
                    @php
                        $primerAjuste = $solicitud->ajustesRazonables->first();
                        $ajusteNombre = $primerAjuste->nombre ?? 'Sin ajuste definido';
                    @endphp
                    {{ Str::limit($ajusteNombre, 40) }}
                </td>
                <td>
                    <span class="status-{{ Str::lower(str_replace(' ', '-', $solicitud->estado ?? 'pendiente')) }}">
                        {{ $solicitud->estado ?? 'Pendiente' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">No hay registros para este periodo.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Ajustes Razonables con Lista de Estudiantes --}}
    <h2 class="page-break">Ajustes Razonables Aplicados</h2>
    @forelse($ajustesConEstudiantes as $ajusteNombre => $estudiantes)
        <div class="ajuste-section">
            <div class="ajuste-title">{{ $ajusteNombre }}</div>
            <div>
                @foreach($estudiantes as $estudiante)
                    <div class="estudiante-item">
                        {{ $estudiante['nombre'] }} - {{ $estudiante['carrera'] }}
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p style="text-align: center; padding: 20px; color: #666;">No hay ajustes razonables aplicados.</p>
    @endforelse

    <div class="footer">
        Documento generado automáticamente por el Sistema de Gestión de Inclusión (SIP).<br>
        Uso exclusivo interno para la Dirección de Carrera.
    </div>

</body>
</html>
