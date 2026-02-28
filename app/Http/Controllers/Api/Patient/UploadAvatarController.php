<?php

namespace App\Http\Controllers\Api\Patient;

use App\Domains\Patient\Models\Patient;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadAvatarController extends Controller
{
    public function __invoke(Request $request, Patient $patient): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $patient->addMediaFromRequest('avatar')
            ->toMediaCollection('avatar');

        return response()->json([
            'data' => [
                'avatar_url' => $patient->fresh()->avatar_url,
            ],
        ]);
    }
}
