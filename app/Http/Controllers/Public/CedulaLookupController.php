<?php

namespace App\Http\Controllers\Public;

use App\Domain\Patients\Services\EcuadorianIdService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CedulaLookupController extends Controller
{
    public function __construct(
        protected EcuadorianIdService $idService
    ) {}

    /**
     * Look up and validate Ecuadorian Cedula with Civil Registry.
     */
    public function lookup(Request $request, string $cedula): JsonResponse
    {
        $result = $this->idService->lookup($cedula);

        if (!$result['is_valid']) {
            return response()->json($result, 422);
        }

        return response()->json($result);
    }
}
