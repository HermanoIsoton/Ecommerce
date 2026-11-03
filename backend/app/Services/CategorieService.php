<?php

namespace App\Services;

use App\Models\Categorie;
use Illuminate\Database\Eloquent\Collection;

class CategorieService
{
    public function listAll(): Collection
    {
        return Categorie::all();
    }

    public function create(array $data): Categorie
    {
        return Categorie::create($data);
    }

    public function findById(int $id): Categorie
    {
        return Categorie::findOrFail($id);
    }

    public function update(int $id, array $data): Categorie
    {
        $categorie = Categorie::findOrFail($id);
        $categorie->update($data);

        return $categorie;
    }

    public function delete(int $id): void
    {
        $categorie = Categorie::findOrFail($id);
        $categorie->delete();
    }
}
