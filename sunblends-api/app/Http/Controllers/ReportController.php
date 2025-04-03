<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\SalesReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Generate and download a sales report
     */
    public function exportSalesReport(Request $request)
    {
        // Validate request
        $request->validate([
            'month' => 'nullable|numeric|between:1,12',
            'year' => 'nullable|numeric|min:2000',
            'format' => 'required|in:xlsx,pdf,csv'
        ]);
        
        // Get month and year from request or use current if not provided
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        // Generate file name
        $fileName = 'sales_report_' . $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT);
        
        // Return export in requested format
        $format = $request->input('format', 'xlsx');
        
        // Create export instance
        $export = new SalesReportExport($month, $year);
        
        // Special handling for PDF format
        if ($format === 'pdf') {
            try {
                // Extract data needed for the PDF
                $data = [
                    'month' => $month,
                    'year' => $year,
                    'totalSales' => $export->getTotalSales(),
                    'totalOrders' => $export->getTotalOrders(),
                    'avgOrderValue' => $export->getAvgOrderValue(),
                    'topDishes' => $export->getTopDishes(),
                    'topRatedDishes' => $export->getTopRatedDishes()
                ];
                
                // Load the PDF view with the data
                $pdf = PDF::loadView('PDF', $data);
                
                // Return the PDF as a download
                return $pdf->download($fileName . '.pdf');
            } catch (\Exception $e) {
                \Log::error("PDF export failed: " . $e->getMessage());
                return response()->json(['error' => 'PDF generation failed: ' . $e->getMessage()], 500);
            }
        }
        
        // For other formats (xlsx, csv)
        return Excel::download($export, $fileName . '.' . $format);
    }
}