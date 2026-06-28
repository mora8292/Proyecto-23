<?php
require 'Classes/PHPExcel/IOFactory.php';
require("conexion.php");

// Suppress deprecated warnings for PHPExcel compatibility
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', 0);

// Use output buffering to prevent header issues
ob_start();

// Define carrera mapping
$carreraOptions = [
    1 => 'ISC',
    2 => 'IIA',
    3 => 'IE',
    4 => 'IL',
    5 => 'IC',
    6 => 'IM',
    7 => 'IGE',
    8 => 'ISA',
    9 => 'LA',
    10 => 'LT',
    11 => 'MSC',
    12 => 'MCA'
];

function sanitizeFilename($string) {
    $string = preg_replace('/[^A-Za-z0-9 _-]/', '', $string);
    return str_replace(' ', '_', $string);
}

function processStudents($resultado, $objPHPExcel, $carreraOptions, &$totalFemales, &$totalMales) {
    $studentsPerPage = 11;
    $addedStudents = [];
    $allStudents = [];
    $totalFemales = 0;
    $totalMales = 0;
    
    // Reset result pointer if needed
    if ($resultado instanceof mysqli_result) {
        $resultado->data_seek(0);
    }
    
    // Primero recopila todos los estudiantes únicos
    while ($row = $resultado->fetch_assoc()) {
        if (!in_array($row['matricula'], $addedStudents)) {
            $addedStudents[] = $row['matricula'];
            $allStudents[] = $row;
            // Contar género
            if ($row['sexo'] == "F") {
                $totalFemales++;
            } else {
                $totalMales++;
            }
        }
    }
    
    // Ahora procesarlos con paginación adecuada
    $totalStudents = count($allStudents);
    $totalSheets = max(1, (int)ceil($totalStudents / $studentsPerPage));
    
    // Asegurarse de tener suficientes hojas
    while ($objPHPExcel->getSheetCount() < $totalSheets) {
        $newSheet = clone $objPHPExcel->getSheet(0);
        $newSheet->setTitle('Hoja' . ($objPHPExcel->getSheetCount() + 1));
        $objPHPExcel->addSheet($newSheet);
    }
    
    // Procesar cada estudiante en la hoja correcta
    foreach ($allStudents as $index => $row) {
        $sheetIndex = (int)floor($index / $studentsPerPage);
        $rowInSheet = ($index % $studentsPerPage) + 20;
        
        $objPHPExcel->setActiveSheetIndex($sheetIndex);
        $hojaAc = $objPHPExcel->getActiveSheet();
        
        $hojaAc->setCellValue('B' . $rowInSheet, $row['nombre'] . ' ' . $row['paterno'] . ' ' . $row['materno']);
        $carreraName = isset($carreraOptions[$row['carrera']]) ? $carreraOptions[$row['carrera']] : 'N/A';
        $hojaAc->setCellValue('S' . $rowInSheet, $carreraName);
        $hojaAc->setCellValue(($row['sexo'] == "F") ? 'AC' . $rowInSheet : 'AD' . $rowInSheet, "X");
    }
    
    // Agregar resumen a cada hoja
    for ($i = 0; $i < $totalSheets; $i++) {
        $objPHPExcel->setActiveSheetIndex($i);
        $hojaAc = $objPHPExcel->getActiveSheet();
        
        // Contar mujeres y hombres en esta hoja
        $sheetFemales = 0;
        $sheetMales = 0;
        for ($row = 20; $row < 31; $row++) { // Filas 20-30 contienen datos de estudiantes
            if ($hojaAc->getCell('AC' . $row)->getValue() == "X") {
                $sheetFemales++;
            }
            if ($hojaAc->getCell('AD' . $row)->getValue() == "X") {
                $sheetMales++;
            }
        }
        
        // Agregar totales de la hoja (fila 32 en la plantilla)
        $hojaAc->setCellValue('W32', $sheetFemales + $sheetMales); // Total en caja izquierda
        $hojaAc->setCellValue('AC32', $sheetFemales); // Mujeres en caja derecha (izquierda del par)
        $hojaAc->setCellValue('AD32', $sheetMales);   // Hombres en caja derecha (derecha del par)
    }
    
    return $objPHPExcel;
}

function processStudentsForReport($resultado, $carreraOptions, &$totalFemales, &$totalMales, &$carreraStats) {
    $addedStudents = [];
    $allStudents = [];
    $totalFemales = 0;
    $totalMales = 0;
    $carreraStats = array_fill_keys(array_values($carreraOptions), 0);
    
    // Reset result pointer if needed
    if ($resultado instanceof mysqli_result) {
        $resultado->data_seek(0);
    }
    
    while ($row = $resultado->fetch_assoc()) {
        if (!in_array($row['matricula'], $addedStudents)) {
            $addedStudents[] = $row['matricula'];
            $allStudents[] = $row;
            
            if ($row['sexo'] == "F") {
                $totalFemales++; 
            } else {
                $totalMales++;
            }
            
            $carreraName = isset($carreraOptions[$row['carrera']]) ? $carreraOptions[$row['carrera']] : 'Desconocida';
            if (!isset($carreraStats[$carreraName])) {
                $carreraStats[$carreraName] = 0;
            }
            $carreraStats[$carreraName]++;
        }
    }
    
    return $allStudents;
}

// Limpiar cualquier output buffer antes de procesar
ob_clean();

if(isset($_POST['pdf']) || isset($_POST['excel']) || isset($_POST['reporte']) || isset($_POST['download_report'])) {
    $campo = (int)$_POST["Eventos"];
    
    // Obtener detalles del evento
    $consultaEvento = "SELECT DISTINCT Nombre_Evento, Fecha_Evento FROM eventos WHERE Id_Evento = ?";
    $stmt = $mysqli->prepare($consultaEvento);
    $stmt->bind_param("i", $campo);
    $stmt->execute();
    $resultEvento = $stmt->get_result();
    $rowEvento = $resultEvento->fetch_assoc();
    $stmt->close();
    
    if (!$rowEvento) {
        die("Evento no encontrado");
    }
    
    // Consultar detalles de los estudiantes
    $sql = "SELECT e.matricula, e.nombre, e.paterno, e.materno, e.sexo, e.carrera
            FROM estudiantes e
            INNER JOIN qreventos q ON e.matricula = q.matricula 
            WHERE q.Id_Evento = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $campo);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    // Manejar exportación PDF/Excel
    if(isset($_POST['pdf']) || isset($_POST['excel'])) {
        $isPdf = isset($_POST['pdf']);

        try {
            // Cargar plantilla de Excel
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel = $objReader->load('/var/www/html/Lista de asistencia.xlsx');

            // Formatear fecha y nombre de archivo
            $eventDate = date('Y-m-d', strtotime($rowEvento['Fecha_Evento']));
            $eventName = sanitizeFilename($rowEvento['Nombre_Evento']);
            $fileBaseName = "($eventDate)-$eventName";
            $fileName = $fileBaseName . ($isPdf ? '.pdf' : '.xlsx');

            // Procesar estudiantes y obtener conteos de género
            $totalFemales = 0;
            $totalMales = 0;
            $objPHPExcel = processStudents($resultado, $objPHPExcel, $carreraOptions, $totalFemales, $totalMales);
            
            // Agregar resumen general a la última hoja (fila 33)
            $lastSheetIndex = $objPHPExcel->getSheetCount() - 1;
            $objPHPExcel->setActiveSheetIndex($lastSheetIndex);  
            $hojaAc = $objPHPExcel->getActiveSheet();
            $hojaAc->setCellValue('AC33', $totalFemales + $totalMales); // Total general (caja izquierda)
            $hojaAc->setCellValue('AC31', $totalFemales); // Total mujeres (caja derecha izquierda)
            $hojaAc->setCellValue('AD31', $totalMales);   // Total hombres (caja derecha derecha)

            // Establecer detalles del evento en todas las hojas
            $sheetCount = $objPHPExcel->getSheetCount();
            for ($i = 0; $i < $sheetCount; $i++) {
                $objPHPExcel->setActiveSheetIndex($i);
                $hojaAc = $objPHPExcel->getActiveSheet();
                $hojaAc->setCellValue('H15', $rowEvento['Nombre_Evento']);
                $hojaAc->setCellValue('H13', $rowEvento['Fecha_Evento']);
            }

            // Clean any remaining output
            ob_clean();

            if ($isPdf) {
                // Guardar y convertir a PDF
                $tempExcel = 'temp_'.time().'.xlsx';
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save($tempExcel);

                $libreOfficePath = '"C:\Program Files\LibreOffice\program\soffice.exe"';
                $command = "$libreOfficePath --headless --convert-to pdf --outdir . $tempExcel 2>&1";
                $output = [];
                $returnVar = 0;
                exec($command, $output, $returnVar);
     
                // Limpiar y enviar PDF
                $tempPDF = str_replace('.xlsx', '.pdf', $tempExcel);
                if (file_exists($tempPDF)) {
                    header('Content-Type: application/pdf');
                    header('Content-Disposition: attachment; filename="' . $fileName . '"');
                    header('Content-Length: ' . filesize($tempPDF));
                    header('Cache-Control: private, max-age=0, must-revalidate');
                    header('Pragma: public');
                    
                    readfile($tempPDF);
                    
                    // Clean up temp files
                    if (file_exists($tempExcel)) unlink($tempExcel);
                    if (file_exists($tempPDF)) unlink($tempPDF);
                } else {
                    echo "Error generando PDF. Código de retorno: " . $returnVar;
                    echo "<br>Salida del comando: " . implode("<br>", $output);
                }
            } else {
                // Salida de archivo Excel
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');
                header('Cache-Control: max-age=1');
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                header('Cache-Control: cache, must-revalidate');
                header('Pragma: public');

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save('php://output');
            }
            
        } catch (Exception $e) {
            ob_clean();
            echo "Error: " . $e->getMessage();
        }
        
        $stmt->close();
        exit();
    }
    
    if(isset($_POST['reporte']) || isset($_POST['download_report'])) {
        $eventName = htmlspecialchars($rowEvento['Nombre_Evento'], ENT_QUOTES, 'UTF-8');
        $eventDate = date('F j, Y', strtotime($rowEvento['Fecha_Evento']));
        
        // Procesar estudiantes y obtener estadísticas
        $totalFemales = 0;
        $totalMales = 0;
        $carreraStats = [];
        $allStudents = processStudentsForReport($resultado, $carreraOptions, $totalFemales, $totalMales, $carreraStats);
        $totalStudents = $totalFemales + $totalMales;
        
        // Clean output buffer before sending headers
        ob_clean();
        
        // Para descarga, establecer encabezados
        if(isset($_POST['download_report'])) {
            $filename = "Reporte_Evento_" . sanitizeFilename($eventName) . "_" . date('Y-m-d') . ".html";
            header('Content-Type: text/html; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        }
        ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Evento - <?php echo $eventName; ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --accent-color: #e74c3c;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --text-color: #333;
            --border-radius: 8px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: #f5f7fa;
            margin: 0;
            padding: 20px;
        }

        .report-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .report-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px;
            text-align: center;
        }

        .report-title {
            margin: 0;
            font-size: 2.2rem;
            font-weight: 600;
        }

        .report-subtitle {
            margin: 10px 0 0;
            font-size: 1.2rem;
            font-weight: 300;
        }

        .report-body {
            padding: 30px;
        }

        .stats-summary {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
            margin: 10px;
            flex: 1;
            min-width: 200px;
            text-align: center;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 600;
            color: var(--primary-color);
            margin: 10px 0;
        }

        .stat-label {
            font-size: 1rem;
            color: var(--text-color);
            opacity: 0.8;
        }

        .chart-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 30px;
            width: 400px;
            margin: 20px auto;
        }

        .chart-title {
            margin-top: 0;
            color: var(--dark-color);
            font-size: 1.4rem;
            border-bottom: 2px solid var(--light-color);
            padding-bottom: 10px;
            text-align: center;
        }

        .download-btn {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 12px 25px;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
            margin: 20px 0;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .download-btn:hover {
            background: #2980b9;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
                padding: 0;
            }

            .report-container {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="report-header">
            <h1 class="report-title">Reporte de Asistencia a Evento</h1>
            <p class="report-subtitle"><?php echo $eventName; ?> • <?php echo $eventDate; ?></p>
        </div>

        <div class="report-body">
            <div class="stats-summary">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $totalStudents; ?></div>
                    <div class="stat-label">Asistencia Total</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $totalFemales; ?></div>
                    <div class="stat-label">Mujeres</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $totalMales; ?></div>
                    <div class="stat-label">Hombres</div>
                </div>
            </div>

            <div class="chart-container">
                <h3 class="chart-title">Asistencia por Género</h3>
                <canvas id="genderChart" height="300"></canvas>
            </div>

            <div class="chart-container">
                <h3 class="chart-title">Asistencia por Carrera</h3>
                <canvas id="carreraChart" height="300"></canvas>
            </div>

            <?php if(!isset($_POST['download_report'])): ?>
            <button class="download-btn no-print" onclick="downloadPDF()">Descargar Reporte PDF</button>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Gráfico de Género
        const genderCtx = document.getElementById('genderChart').getContext('2d');
        new Chart(genderCtx, {
            type: 'doughnut',
            data: {
                labels: ['Mujeres (<?php echo $totalFemales; ?>)', 'Hombres (<?php echo $totalMales; ?>)'],
                datasets: [{
                    data: [<?php echo $totalFemales; ?>, <?php echo $totalMales; ?>],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de Carreras
        const carreraCtx = document.getElementById('carreraChart').getContext('2d');
        new Chart(carreraCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_keys($carreraStats)); ?>,
                datasets: [{
                    label: 'Número de Estudiantes',
                    data: <?php echo json_encode(array_values($carreraStats)); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' estudiantes';
                            }
                        }
                    }
                }
            }
        });

        async function downloadPDF() {
            const { jsPDF } = window.jspdf;
            const reportElement = document.querySelector('.report-container');

            try {
                await html2canvas(reportElement).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const pdf = new jsPDF('p', 'mm', 'a4');

                    const imgProps = pdf.getImageProperties(imgData);
                    const pdfWidth = pdf.internal.pageSize.getWidth();
                    const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

                    pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                    pdf.save("Reporte_Evento_<?php echo sanitizeFilename($eventName); ?>.pdf");
                });
            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('Error al generar el PDF. Por favor, intente nuevamente.');
            }
        }
    </script>
</body>
</html>

        <?php
        $stmt->close();
        exit();
    }
}
?>