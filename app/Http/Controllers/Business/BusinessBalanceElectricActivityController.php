<?php

namespace App\Http\Controllers\Business;

use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\BalanceTransaction;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;


class BusinessBalanceElectricActivityController extends Controller
{
    public function indexApi(Business $business, Request $request)
    {
        $data = BalanceTransaction::where('')->get();

        $response = [
            'message' => "Data Telah Tervalidasi",
            'status' => 200,
            'data' => $request->tanggal
        ];

        try {
            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }
}
