<?php

namespace App\Exports;

use App\Models\Transaction;
use App\Models\Cart;
use App\Models\Dish;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SalesReportExport implements 
    FromCollection, 
    WithHeadings,
    WithTitle,
    WithStyles,
    WithColumnWidths,
    WithCustomStartCell,
    WithEvents
{
    protected $month;
    protected $year;
    protected $totalSales = 0;
    protected $totalOrders = 0;
    protected $avgOrderValue = 0;
    protected $topDishes = [];
    protected $topRatedDishes = [];

    public function __construct($month = null, $year = null)
    {
        $this->month = $month ?: date('m');
        $this->year = $year ?: date('Y');
        $this->calculateStats();
    }

    /**
     * Calculate statistics for the report
     */
    private function calculateStats()
    {
        // Date range for the selected month
        $startDate = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::createFromDate($this->year, $this->month, 1)->endOfMonth()->format('Y-m-d');

        // Calculate total sales (only completed transactions)
        $this->totalSales = Transaction::where('transaction_status', 'completed')
            ->whereBetween(DB::raw('DATE(transaction_date)'), [$startDate, $endDate])
            ->sum('cash_amount');

        // Calculate total orders
        $this->totalOrders = Transaction::where('transaction_status', 'completed')
            ->whereBetween(DB::raw('DATE(transaction_date)'), [$startDate, $endDate])
            ->count();

        // Calculate average order value
        $this->avgOrderValue = $this->totalOrders > 0 ? $this->totalSales / $this->totalOrders : 0;

        // Get top dishes - updated method name here
        $this->calculateTopDishes($startDate, $endDate);
        
        // Get top rated dishes
        $this->calculateTopRatedDishes($startDate, $endDate);
    }

    // Add these methods after the calculateStats() method:

    /**
     * Get the total sales value
     * @return float
     */
    public function getTotalSales()
    {
        return $this->totalSales;
    }

    /**
     * Get the total number of orders
     * @return int
     */
    public function getTotalOrders()
    {
        return $this->totalOrders;
    }

    /**
     * Get the average order value
     * @return float
     */
    public function getAvgOrderValue()
    {
        return $this->avgOrderValue;
    }

    /**
     * Get the top dishes by order quantity
     * @return \Illuminate\Support\Collection
     */
    public function getTopDishes()
    {
        return $this->topDishes;
    }

    /**
     * Get the top rated dishes
     * @return \Illuminate\Support\Collection
     */
    public function getTopRatedDishes()
    {
        return $this->topRatedDishes;
    }

    /**
     * Get the most ordered dishes
     */
    private function calculateTopDishes($startDate, $endDate)
    {
        // Get all completed order IDs in the date range
        $orderIds = Transaction::where('transaction_status', 'completed')
            ->whereBetween(DB::raw('DATE(transaction_date)'), [$startDate, $endDate])
            ->pluck('order_id')
            ->filter()
            ->toArray();

        if (empty($orderIds)) {
            $this->topDishes = [];
            return;
        }

        // Get top dishes by order quantity
        $topDishes = Cart::withTrashed()
            ->whereIn('cart.order_id', $orderIds)
            ->whereNotNull('cart.dish_id')
            ->join('dish', 'cart.dish_id', '=', 'dish.dish_id')
            ->select(
                'dish.dish_id',
                'dish.dish_name',
                'dish.Price as price',
                DB::raw('SUM(cart.quantity) as total_ordered'),
                DB::raw('SUM(cart.quantity * dish.Price) as total_revenue')
            )
            ->groupBy('dish.dish_id', 'dish.dish_name', 'dish.Price')
            ->orderByDesc('total_ordered')
            ->limit(10)
            ->get();

        $this->topDishes = $topDishes;
    }
    

    /**
     * Get top rated dishes
     */
    private function calculateTopRatedDishes($startDate, $endDate)
    {
        // Get top rated dishes
        $topRatedDishes = Dish::select(
                'dish.dish_id',
                'dish.dish_name',
                DB::raw('AVG(ratings.rating) as avg_rating'),
                DB::raw('COUNT(ratings.rating) as rating_count')
            )
            ->join('ratings', 'dish.dish_id', '=', 'ratings.dish_id')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween(DB::raw('DATE(ratings.created_at)'), [$startDate, $endDate]);
            })
            ->groupBy('dish.dish_id', 'dish.dish_name')
            ->having('rating_count', '>', 0)
            ->orderByDesc('avg_rating')
            ->orderByDesc('rating_count')
            ->limit(10)
            ->get();

        $this->topRatedDishes = $topRatedDishes;
    }

    /**
     * Set the starting cell for the data
     */
    public function startCell(): string
    {
        return 'A10';
    }

    /**
     * Return a collection of data for the Excel export
     */
    public function collection()
    {
        $data = collect();
        
        // Add top dishes data
        foreach ($this->topDishes as $index => $dish) {
            $data->push([
                'no' => $index + 1,
                'dish_id' => $dish->dish_id,
                'dish_name' => $dish->dish_name,
                'quantity' => $dish->total_ordered,
                'unit_price' => $dish->price,
                'total_revenue' => $dish->total_revenue,
                'type' => 'Most Ordered'
            ]);
        }
        
        // Add separator
        $data->push([
            'no' => '',
            'dish_id' => '',
            'dish_name' => '',
            'quantity' => '',
            'unit_price' => '',
            'total_revenue' => '',
            'type' => ''
        ]);
        
        // Add top rated dishes
        foreach ($this->topRatedDishes as $index => $dish) {
            $data->push([
                'no' => $index + 1,
                'dish_id' => $dish->dish_id,
                'dish_name' => $dish->dish_name,
                'rating' => number_format($dish->avg_rating, 1),
                'rating_count' => $dish->rating_count,
                'total_revenue' => '',
                'type' => 'Top Rated'
            ]);
        }

        return $data;
    }

    /**
     * Set headings for the Excel sheet
     */
    public function headings(): array
    {
        return [
            'No',
            'Dish ID',
            'Dish Name',
            'Quantity/Rating',
            'Unit Price/Count',
            'Total Revenue',
            'Type'
        ];
    }

    /**
     * Set the sheet title
     */
    public function title(): string
    {
        return 'Sales Report - ' . Carbon::createFromDate($this->year, $this->month, 1)->format('F Y');
    }

    /**
     * Customize column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 15,
            'C' => 40,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
        ];
    }

    /**
     * Customize cell styles
     */
    public function styles(Worksheet $sheet)
    {
        // Add report header
        $monthYear = Carbon::createFromDate($this->year, $this->month, 1)->format('F Y');
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'SunBlends Cafe');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', 'Sales Report - ' . $monthYear);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        // Add summary statistics
        $sheet->setCellValue('A4', 'Total Sales:');
        $sheet->setCellValue('B4', '₱' . number_format($this->totalSales, 2));
        $sheet->getStyle('A4')->getFont()->setBold(true);

        $sheet->setCellValue('A5', 'Total Orders:');
        $sheet->setCellValue('B5', $this->totalOrders);
        $sheet->getStyle('A5')->getFont()->setBold(true);

        $sheet->setCellValue('A6', 'Average Order Value:');
        $sheet->setCellValue('B6', '₱' . number_format($this->avgOrderValue, 2));
        $sheet->getStyle('A6')->getFont()->setBold(true);

        $sheet->setCellValue('A8', 'Most Ordered Dishes:');
        $sheet->getStyle('A8')->getFont()->setBold(true);

        // Style the headings
        $sheet->getStyle('A10:G10')->getFont()->setBold(true);
        $sheet->getStyle('A10:G10')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');

        return [
            10 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * After sheet event to add more styling
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Get last row
                $lastRow = $event->sheet->getHighestRow();
                
                // Add borders to the entire data range
                $event->sheet->getStyle('A10:G' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                
                // Format currency cells
                $event->sheet->getStyle('E11:F' . $lastRow)->getNumberFormat()->setFormatCode('₱#,##0.00');
                
                // Add a separator between most ordered and top rated
                $separatorRow = 11 + count($this->topDishes);
                $event->sheet->getStyle('A' . $separatorRow . ':G' . $separatorRow)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFEAEAEA');
                
                // Add heading for top rated dishes
                $topRatedHeadingRow = $separatorRow + 1;
                $event->sheet->setCellValue('A' . $topRatedHeadingRow, 'Top Rated Dishes:');
                $event->sheet->getStyle('A' . $topRatedHeadingRow)->getFont()->setBold(true);
                
                // Generate footer
                $event->sheet->mergeCells('A' . ($lastRow + 2) . ':G' . ($lastRow + 2));
                $event->sheet->setCellValue('A' . ($lastRow + 2), 'Report generated on: ' . now()->format('F d, Y h:i A'));
                $event->sheet->getStyle('A' . ($lastRow + 2))->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle('A' . ($lastRow + 2))->getFont()->setItalic(true);
            },
        ];
    }
}