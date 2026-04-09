<?php

namespace App\Http\Controllers;

use App\Models\GenerationHistory;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $histories = $request->user()
            ->histories()
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $histories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'parameters' => ['required', 'array', 'min:3'],
            'parameters.*.parameter' => ['required', 'string'],
            'parameters.*.values' => ['required', 'array', 'min:2'],
            'combinations' => ['required', 'array', 'min:1'],
        ]);

        $history = $request->user()->histories()->create([
            'name' => $validated['name'] ?? 'Generation ' . now('Asia/Kuala_Lumpur')->format('Y-m-d H:i'),
            'parameters' => $validated['parameters'],
            'combinations' => $validated['combinations'],
            'combination_count' => count($validated['combinations']),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'History saved',
            'data' => $history,
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $history = $request->user()->histories()->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $history,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $history = $request->user()->histories()->findOrFail($id);
        $history->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'History deleted',
        ]);
    }
}
