<?php
session_start();
require_once 'config.php';

try {
    require_once 'C:\wamp64\www\PT\vendor\autoload.php'; // Absolute path to Composer autoloader
} catch (Exception $e) {
    error_log("Autoloader error: " . $e->getMessage());
    header('Content-Type: application/json');
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['success' => false, 'message' => 'Server error: Unable to load required libraries']);
    exit();
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

error_log("Session data in admin_get_all_bookings.php: " . print_r($_SESSION, true)); // Debug session

// Align role check with sessions.php, assuming admin users have role 'staff'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    error_log("Unauthorized access attempt: user_id=" . ($_SESSION['user_id'] ?? 'none') . ", role=" . ($_SESSION['role'] ?? 'none'));
    header('Content-Type: application/json');
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Invalid session or insufficient privileges']);
    exit();
}

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$pet_id = isset($_GET['pet_id']) ? $_GET['pet_id'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$username = isset($_GET['username']) ? $_GET['username'] : '';
$format = isset($_GET['format']) ? $_GET['format'] : 'json';

$query = "SELECT b.booking_id, b.user_id, b.pet_id, b.start_date, b.end_date, b.booking_status, 
                 u.username, u.email
          FROM bookings b
          JOIN users u ON b.user_id = u.user_id
          WHERE 1=1";

$params = [];
if ($start_date && $end_date) {
    $query .= " AND b.start_date BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
} elseif ($start_date) {
    $query .= " AND b.start_date >= ?";
    $params[] = $start_date;
} elseif ($end_date) {
    $query .= " AND b.start_date <= ?";
    $params[] = $end_date;
}
if ($pet_id) {
    $query .= " AND b.pet_id = ?";
    $params[] = $pet_id;
}
if ($status) {
    $query .= " AND b.booking_status = ?";
    $params[] = $status;
}
if ($username) {
    $query .= " AND u.username LIKE ?";
    $params[] = "%$username%";
}

try {
    $stmt = $conn->prepare($query);
    if ($params) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }

    if ($format === 'pdf') {
        // Generate PDF using TCPDF
        $pdf = new \TCPDF();
        $pdf->SetCreator('Pet Booking System');
        $pdf->SetAuthor('Admin');
        $pdf->SetTitle('Booking Details');
        $pdf->SetSubject('Booking Details');
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        // Header
        $pdf->Cell(0, 10, 'Booking Details', 0, 1, 'C');
        $pdf->Ln(5);

        // Table header
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Cell(20, 10, 'Booking ID', 1, 0, 'C', 1);
        $pdf->Cell(30, 10, 'Username', 1, 0, 'C', 1);
        $pdf->Cell(40, 10, 'Email', 1, 0, 'C', 1);
        $pdf->Cell(20, 10, 'Pet ID', 1, 0, 'C', 1);
        $pdf->Cell(30, 10, 'Check-In', 1, 0, 'C', 1);
        $pdf->Cell(30, 10, 'Check-Out', 1, 0, 'C', 1);
        $pdf->Cell(20, 10, 'Status', 1, 1, 'C', 1);

        // Table data
        foreach ($bookings as $booking) {
            $pdf->Cell(20, 10, $booking['booking_id'], 1);
            $pdf->Cell(30, 10, $booking['username'], 1);
            $pdf->Cell(40, 10, $booking['email'], 1);
            $pdf->Cell(20, 10, $booking['pet_id'], 1);
            $pdf->Cell(30, 10, $booking['start_date'], 1);
            $pdf->Cell(30, 10, $booking['end_date'], 1);
            $pdf->Cell(20, 10, $booking['booking_status'], 1);
            $pdf->Ln();
        }

        // Output PDF
        $pdf->Output('bookings.pdf', 'D'); // 'D' forces download
        exit();
    } elseif ($format === 'excel') {
        // Generate Excel using PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Bookings');

        // Header
        $sheet->setCellValue('A1', 'Booking ID');
        $sheet->setCellValue('B1', 'Username');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Pet ID');
        $sheet->setCellValue('E1', 'Check-In Date');
        $sheet->setCellValue('F1', 'Check-Out Date');
        $sheet->setCellValue('G1', 'Status');

        // Data
        $row = 2;
        foreach ($bookings as $booking) {
            $sheet->setCellValue("A$row", $booking['booking_id']);
            $sheet->setCellValue("B$row", $booking['username']);
            $sheet->setCellValue("C$row", $booking['email']);
            $sheet->setCellValue("D$row", $booking['pet_id']);
            $sheet->setCellValue("E$row", $booking['start_date']);
            $sheet->setCellValue("F$row", $booking['end_date']);
            $sheet->setCellValue("G$row", $booking['booking_status']);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Output Excel
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="bookings.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    } else {
        // Default JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'bookings' => $bookings
        ]);
    }
} catch (Exception $e) {
    error_log("Error in admin_get_all_bookings.php: " . $e->getMessage());
    header('Content-Type: application/json');
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching bookings: ' . $e->getMessage()
    ]);
}
$conn->close();
?>