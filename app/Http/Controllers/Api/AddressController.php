<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AddressService;

class AddressController extends Controller
{
    public function __construct(
        private AddressService $addressService
    ) {}
   
    public function index()
    {
        $addresss = $this->addressService->listAll();
        return response()->json($addresss);
    }

   
    public function store(Request $request)
    {
        $address = $this->addressService->create($request->all());
        return response()->json($address, 201);
    }

    
    public function show(string $id)
    {
         $address = $this->addressService->findById($id);

        return response()->json($address);
    }

   
    public function update(Request $request, string $id)
    {
          $address = $this->addressService->update($id, $request->all());

        return response()->json($address);
    }


    public function destroy(string $id)
    {
        $this->addressService->delete($id);

        return response()->noContent();
    }
}
