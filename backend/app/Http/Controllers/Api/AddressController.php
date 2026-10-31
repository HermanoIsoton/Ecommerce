<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressesRequest;
use App\Http\Requests\UpdateAddressesRequest;
use App\Http\Resources\AddressResource;
use App\Services\AddressService;

class AddressController extends Controller
{
    public function __construct(
        private AddressService $addressService
    ) {}

    public function index()
    {
        $addresses = $this->addressService->listAll();

        return AddressResource::collection($addresses);
    }

    public function store(StoreAddressesRequest $request)
    {
        $address = $this->addressService->create($request->validated());

        return new AddressResource($address);
    }

    public function show(string $id)
    {
        $address = $this->addressService->findById($id);

        return new AddressResource($address);
    }

    public function update(UpdateAddressesRequest $request, string $id)
    {
        $address = $this->addressService->update($id, $request->validated());

        return new AddressResource($address);
    }

    public function destroy(string $id)
    {
        $this->addressService->delete($id);

        return response()->noContent();
    }
}
