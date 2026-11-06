<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressesRequest;
use App\Http\Requests\UpdateAddressesRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Services\AddressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $address = DB::transaction(function () use ($request) {
            $user = $request->user()->newQuery()
                ->lockForUpdate()
                ->findOrFail($request->user()->id);
            $isFirstAddress = ! Address::query()
                ->where('user_id', $user->id)
                ->exists();

            return $this->addressService->create([
                ...$request->validated(),
                'user_id' => $user->id,
                'is_primary' => $isFirstAddress,
            ]);
        });

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
        $address = DB::transaction(function () use ($request, $id) {
            $user = $request->user()->newQuery()
                ->lockForUpdate()
                ->findOrFail($request->user()->id);
            $address = $user->role === 'admin'
                ? $this->addressService->findById($id)
                : Address::query()
                    ->where('user_id', $user->id)
                    ->findOrFail($id);
            $data = $request->validated();

            if (($data['is_primary'] ?? false) === true) {
                Address::query()
                    ->where('user_id', $address->user_id)
                    ->whereKeyNot($address->id)
                    ->update(['is_primary' => false]);
            }

            $address->update($data);

            return $address->refresh();
        });

        return new AddressResource($address);
    }

    public function destroy(Request $request, string $id)
    {
        DB::transaction(function () use ($request, $id) {
            $user = $request->user()->newQuery()
                ->lockForUpdate()
                ->findOrFail($request->user()->id);
            $address = $user->role === 'admin'
                ? $this->addressService->findById($id)
                : Address::query()
                    ->where('user_id', $user->id)
                    ->findOrFail($id);
            $wasPrimary = $address->is_primary;
            $ownerId = $address->user_id;
            $address->delete();

            if ($wasPrimary) {
                $nextAddress = Address::query()
                    ->where('user_id', $ownerId)
                    ->orderBy('id')
                    ->first();

                $nextAddress?->update(['is_primary' => true]);
            }
        });

        return response()->noContent();
    }
}
