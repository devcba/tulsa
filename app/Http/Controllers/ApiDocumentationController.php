<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

class ApiDocumentationController extends Controller
{
    /**
     * Return the OpenAPI specification for the application.
     */
    public function __invoke(): JsonResponse
    {
        $specPath = resource_path('docs/openapi.json');

        if (! File::exists($specPath)) {
            abort(404, 'OpenAPI specification not found.');
        }

        $contents = File::get($specPath);
        $spec = json_decode($contents, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            abort(500, 'Invalid OpenAPI specification.');
        }

        $spec['servers'] = [
            [
                'url' => url('/api'),
                'description' => 'API base URL',
            ],
        ];

        return response()->json($spec);
    }
}
