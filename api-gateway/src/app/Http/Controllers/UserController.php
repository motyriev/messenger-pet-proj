<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    private DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }
    public function getDashboard(Request $request, int $userId): JsonResponse
    {
        try {
            $dashboard = $this->dashboardService->getDashboard($userId);
            return response()->json($dashboard);
        } catch (\Throwable $e) {
            Log::error('Error fetching dashboard', [
                'userId' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Failed to fetch dashboard'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}