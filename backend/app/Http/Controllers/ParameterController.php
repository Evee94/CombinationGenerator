<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\ParameterModel;
use App\Models\ParameterValueModel;
use Illuminate\Support\Facades\DB;

class ParameterController extends Controller
{
    /**
     * Get all parameters with their values
     */
    public function getAllParameters()
    {
        $parameters = ParameterModel::with('values')->get();
        return response()->json([
            'status' => 'success',
            'data' => $parameters
        ]);
    }

    /**
     * Get a specific parameter with its values
     */
    public function getParameter($id)
    {
        $parameter = ParameterModel::with('values')->find($id);
        
        if (!$parameter) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $parameter
        ]);
    }

    /**
     * Save parameters with their values (create or update)
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'parameters' => ['required', 'array', 'min:1'],
            'parameters.*.name' => ['required', 'string', 'max:255'],
            'parameters.*.description' => ['nullable', 'string', 'max:255'],
            'parameters.*.values' => ['required', 'array', 'min:2', 'max:10'],
            'parameters.*.values.*' => ['required', 'string', 'max:255'],
        ]);

        DB::beginTransaction();
        try {
            $savedParameters = [];

            foreach ($validated['parameters'] as $paramData) {
                $parameter = ParameterModel::updateOrCreate(
                    ['name' => $paramData['name']],
                    ['description' => $paramData['description'] ?? null]
                );

                // Remove old values and insert new ones
                $parameter->values()->delete();

                foreach ($paramData['values'] as $value) {
                    $parameter->values()->create(['value' => $value]);
                }

                $savedParameters[] = $parameter->load('values');
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Parameters saved successfully',
                'data' => $savedParameters,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save parameters',
            ], 500);
        }
    }

    /**
     * Get all saved parameters for the authenticated user
     */
    public function get()
    {
        $parameters = ParameterModel::with('values')->get();

        return response()->json([
            'status' => 'success',
            'data' => $parameters,
        ]);
    }
} 