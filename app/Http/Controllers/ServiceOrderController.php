<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceOrderController extends Controller
{
    public function create(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'vehiclePlate' => 'required|string|max:50',
            'entryDateTime' => 'required|date',
            'exitDateTime' => 'required|date',
            'priceType' => 'required|numeric',
            'price' => 'required|numeric',
            'userId' => 'required|integer|exists:users,id'
        ]);

        if ($validatedData->fails()) {
            return response()->json(['message' => 'Erro na validação dos campos'], 422);
        }

        ServiceOrder::create([
            'vehiclePlate' => $request->vehiclePlate,
            'entryDateTime' => $request->entryDateTime,
            'exitDateTime' => $request->exitDateTime,
            'priceType' => $request->priceType,
            'price' => $request->price,
            'userId' => $request->userId
        ]);


        return response()->json(['message' => 'Ordem de serviço criada com sucesso'], 201);
    }

    public function index(Request $request)
    {
        $vehiclePlate = $request->input('vehiclePlate');

        $page = $request->input('page', 1);

        $perPage = 5;

        $serviceOrders = ServiceOrder::with('user')
            ->when($vehiclePlate, function ($query) use ($vehiclePlate) {
                $query->where('vehiclePlate', 'like', "%$vehiclePlate%");
            })
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json($serviceOrders);
    }
}
