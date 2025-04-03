<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .summary { margin: 20px 0; }
        .footer { margin-top: 30px; text-align: center; font-style: italic; }
        /* Add a class for currency values */
        .currency:before { content: "PHP "; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SunBlends Cafe</h1>
        <h2>Sales Report - {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</h2>
    </div>
    
    <div class="summary">
        <p><strong>Total Sales:</strong> <span class="currency">{{ number_format($totalSales, 2) }}</span></p>
        <p><strong>Total Orders:</strong> {{ number_format($totalOrders) }}</p>
        <p><strong>Average Order Value:</strong> <span class="currency">{{ number_format($avgOrderValue, 2) }}</span></p>
    </div>
    
    <h3>Most Ordered Dishes</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Dish Name</th>
                <th>Quantity</th>
                <th>Total Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topDishes as $index => $dish)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $dish->dish_name }}</td>
                    <td>{{ $dish->total_ordered }}</td>
                    <td class="currency">{{ number_format($dish->total_revenue, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <h3>Top Rated Dishes</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Dish Name</th>
                <th>Rating</th>
                <th>Number of Ratings</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topRatedDishes as $index => $dish)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $dish->dish_name }}</td>
                    <td>{{ number_format($dish->avg_rating, 1) }}</td>
                    <td>{{ $dish->rating_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Report generated on: {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</body>
</html>