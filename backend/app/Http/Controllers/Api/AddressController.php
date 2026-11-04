<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressesRequest;
use App\Http\Requests\UpdateAddressesRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Services\AddressService;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct(
        private AddressService $addressService
    ) {}

    public function index(Request $request)
    {
        $addresses = $request->user()->role === 'admin'
            ? $this->addressService->listAll()
            : Address::query()->where('user_id', $request->user()->id)->get();

        return AddressResource::collection($addresses);
    }

    public function store(StoreAddressesRequest $request)
    {
        $address = $this->addressService->create($request->validated());

        return new AddressResource($address);
    }

    public function show(Request $request, string $id)
    {
        $address = $request->user()->role === 'admin'
            ? $this->addressService->findById($id)
            : Address::query()
                ->where('user_id', $request->user()->id)
                ->findOrFail($id);

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
