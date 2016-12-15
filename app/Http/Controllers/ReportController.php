<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Response;

use App\Http\Requests;
use Carbon\Carbon;

class ReportController extends Controller
{
    private function getFirstColors()
    {
        return [
            "#FF6384",
            "#36A2EB",
            "#FFCE56",
            "#B87333",
            "#FF7F50",
            "#FBEC5D",
            "#6495ED",
            "#DC143C"
        ];
    }

    private function getFirstHoverColors()
    {
        return [
            "#FF6384",
            "#36A2EB",
            "#FFCE56",
            "#B87333",
            "#FF7F50",
            "#FBEC5D",
            "#6495ED",
            "#DC143C"
        ];
    }

    public function getReportData($name)
    {

        switch ($name) {
            case "common_sales":
                $data = $this->getCommonSalesReportData();
                break;
            case "order_statuses":
                $data = $this->getOrderStatusesData();
                break;
            case "category_pie":
                $data = $this->getCategoryPie();
                break;
            case "brand_pie":
                $data = $this->getBrandPie();
                break;
            case "test":
            default:
                $data = $this->getCategoryPie();
       //         $data = $this->calculateOrdersByMonth("open");
                break;
        }

        return Response::success($data);
    }

    private function getBrandPie()
    {
        $brands = Brand::take(20)->get();

        $labels = [];
        $dataSets = [];

        if ($brands) {
            foreach ($brands as $brand)
            {
                array_push($labels, $brand->name);

                $count = 0;
                if ($countProduct = $brand->productCount) {
                    $count = $countProduct->count;
                }
                array_push($dataSets, $count);
            }
        }

        $data = [
            'type' => 'doughnut',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'data' => $dataSets,
                        'backgroundColor' => $this->getFirstColors(),
                        'hoverBackgroundColor' => $this->getFirstHoverColors()
                    ]
                ]
            ]
        ];

        return $data;
    }

    private function getCategoryPie()
    {
        $categories = Category::take(20)->get();

        $labels = [];
        $dataSets = [];

        if ($categories) {
            foreach ($categories as $category)
            {
                array_push($labels, $category->name);

                $count = 0;
                if ($countProduct = $category->productCount) {
                    $count = $countProduct->count;
                }
                array_push($dataSets, $count);
            }
        }

        $data = [
            'type' => 'doughnut',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'data' => $dataSets,
                        'backgroundColor' => $this->getFirstColors(),
                        'hoverBackgroundColor' => $this->getFirstHoverColors()
                    ]
                ]
            ]
        ];

        return $data;
    }

    private function getMonthLabels()
    {
        return [
            'Январь',
            'Февраль',
            'Март',
            'Апрель',
            'Май',
            'Июнь',
            'Июль',
            'Август',
            'Сентябрь',
            'Октябрь',
            'Ноябрь',
            'Декабрь'
        ];
    }

    private function getTestReport()
    {
        $data = [
            'type' => 'bar',
            'data' => [
                'labels' => $this->getMonthLabels(),
                'datasets' => [
                    [
                        'label' => 'Заказов выполненно',
                        'data' => [1,3,2,7,4,6,4,3,3,9,5],
                        'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                        'borderColor' => 'rgba(75, 192, 192, 1)',
                        'borderWidth' => 1
                    ],
                    [
                        'label' => 'Заказов отменено',
                        'data' => [3,2,7,4,6,4,3,3,9,5,1],
                        'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                        'borderColor' => 'rgba(255, 99, 132, 1)',
                        'borderWidth' => 1
                    ]
                ]
            ],
            'options' => $this->startFromZeroOption()
        ];

        return $data;
    }

    private function getCommonSalesReportData()
    {
        $data = [
            'type' => 'line',
            'data' => [
                'labels' => ['Item 1', 'Item 2', 'Item 3'],
                'datasets' => [
                    [
                        'label' => 'Scatter Dataset',
                        'data' => [
                            1,2,4
                        ],
                        'borderColor' =>  "#FBEC5D",
                        'pointBorderColor' => 'black',
                        'fill' => false
                    ],
                    [
                        'label' => 'Scatter Dataset',
                        'data' => [
                            1,6,8
                        ],
                        'borderColor' => "#DC143C",
                        'pointBorderColor' => 'black',
                        'fill' => false
                    ]
                ]
            ],
            'options' => $this->startFromZeroOption()
        ];

        return $data;
    }

    private function getOrderStatusesData()
    {
        $data = [
            'type' => 'bar',
            'data' => [
                'labels' => $this->getMonthLabels(),
                'datasets' => [
                    [
                        'label' => 'Заказов выполненно',
                        'data' => $this->calculateOrdersByMonth('done'),
                        'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                        'borderColor' => 'rgba(75, 192, 192, 1)',
                        'borderWidth' => 1
                    ],
                    [
                        'label' => 'Заказов отменено',
                        'data' => $this->calculateOrdersByMonth('decline'),
                        'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                        'borderColor' => 'rgba(255, 99, 132, 1)',
                        'borderWidth' => 1
                    ]
                ]
            ],
            'options' => $this->startFromZeroOption()
        ];

        return $data;
    }

    private function initData($count)
    {
        $data = [];

        if ($count > 0) {

            for ($i = 0; $i < $count; $i++)
            {
                array_push($data, 0);
            }
        }

        return $data;
    }

    private function calculateOrdersByMonth($status)
    {
        $orders = Order::where('status', $status)
            ->lists('updated_at');

        $data = $this->initData(12);

        if ($orders) {
            foreach ($orders as $key => $orderDate)
            {
                if ($orderDate) {
                    $dateTime = Carbon::createFromFormat('Y-m-d H:i:s', $orderDate);
                    $data[$dateTime->month-1]++;
                }
            }
        }

        return $data;
    }

    private function calculateAverageCategoryCost()
    {
        $categories = Category::all();

        $data = [];
        if ($categories) {
            $labels = [];
            $costs = [];

            foreach ($categories as $category)
            {
                array_push($labels, $category->name);

                $totalCost = 0;
                $averageCost = 0;
                $products = $category->products;

                if ($products) {
                    foreach ($products as $product)
                    {
                        $totalCost += $product->cost;
                    }

                    $averageCost = $totalCost / count($products);
                }

                array_push($costs, $averageCost);
            }

            $data = [
                'labels' => $labels,
                'costs' => $costs
            ];
        }

        return $data;
    }

    private function startFromZeroOption()
    {
        $options =  [
            'scales' => [
                'yAxes' => [
                    [
                        'ticks' => [
                            'beginAtZero' => true
                        ]
                    ]
                ]
            ]
        ];

        return $options;
    }
}
