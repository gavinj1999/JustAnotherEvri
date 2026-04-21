<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEvriScrapeRequest;
use App\Models\EvriScrape;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class EvriScrapeController extends Controller
{
    public function store(StoreEvriScrapeRequest $request): JsonResponse
    {
        try {
            $scrape = EvriScrape::create($request->validated());

            Log::info('Evri scrape stored', [
                'id' => $scrape->id,
                'execution_id' => $scrape->execution_id,
                'date' => $scrape->date->toDateString(),
                'status' => $scrape->status,
            ]);

            return response()->json([
                'success' => true,
                'data' => ['id' => $scrape->id, 'execution_id' => $scrape->execution_id],
            ], 201);

        } catch (UniqueConstraintViolationException $e) {
            $validated = $request->validated();
            $date = $validated['date'];
            $round = $validated['round'];
            $executionId = $validated['execution_id'];

            // Execution ID collision means exact same scrape run was posted twice
            if (EvriScrape::where('execution_id', $executionId)->exists()) {
                Log::info('Duplicate scrape run ignored', ['execution_id' => $executionId]);
                return response()->json([
                    'success' => true,
                    'message' => 'Already recorded.',
                ], 200);
            }

            Log::warning('Duplicate date/round combination rejected', [
                'date' => $date,
                'round' => $round,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'A scrape result already exists for this date' . ($round ? " and round {$round}" : '') . '.',
            ], 409);
        }
    }
}
