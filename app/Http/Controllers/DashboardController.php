<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Cachi andmed 12 tunniks, et vältida liigseid päringuid
        $agricultureData = Cache::remember('agriculture_data', now()->addHours(12), function () {
            return $this->fetchAgricultureData();
        });

        return Inertia::render('Dashboard', [
            'agricultureData' => $agricultureData
        ]);
    }

    private function fetchAgricultureData()
    {
        // Statistikaameti API URL
        $apiUrl = 'https://andmed.stat.ee/api/v1/et/stat/PM59';

        // JSON-päringu keha
        $payload = [
            "query" => [
                [
                    "code" => "Maakond",
                    "selection" => [
                        "filter" => "item",
                        "values" => ["1", "37", "39", "44", "49", "51", "57", "59", "65", "67", "70", "74", "78", "82", "84", "86"]
                    ]
                ],
                [
                    "code" => "Põllumajandusmaa liik",
                    "selection" => [
                        "filter" => "item",
                        "values" => ["1", "2", "3"]
                    ]
                ]
            ],
            "response" => ["format" => "json-stat2"]
        ];

        // Saada POST-päring
        $response = Http::post($apiUrl, $payload);

        // Kontrolli, kas API vastus õnnestus
        if ($response->failed()) {
            return [];
        }

        $data = $response->json();

        // Töötle andmed (lihtsustame struktuuri)
        return $this->processAgricultureData($data);
    }

    private function processAgricultureData($data)
    {
        // Kontrolli, kas andmed on saadud
        if (!isset($data['value']) || !isset($data['dimension']['Maakond']['category']['label'])) {
            return [];
        }

        $regions = $data['dimension']['Maakond']['category']['label'];
        $landTypes = $data['dimension']['Põllumajandusmaa liik']['category']['label'];
        $values = $data['value'];

        $processedData = [];
        $index = 0;

        foreach ($regions as $regionKey => $regionName) {
            foreach ($landTypes as $landTypeKey => $landTypeName) {
                $processedData[] = [
                    'region' => $regionName,
                    'land_type' => $landTypeName,
                    'value' => $values[$index] ?? 0
                ];
                $index++;
            }
        }

        return $processedData;
    }
}
