<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address'];

    // Create a new Company
    public static function createCompany($name, $address)
    {
        return Company::create([
            'name' => $name,
            'address' => $address,
        ]);
    }

    // Read a company by ID
    public static function getCompany($id)
    {
        return Company::findOrFail($id);
    }

    // Update a company
    public function updateCompany($name, $address)
    {
        $this->name = $name;
        $this->address = $address;
        $this->save();
        return $this;
    }

    // Delete a company
    public function deleteCompany()
    {
        $this->delete();
    }
}
