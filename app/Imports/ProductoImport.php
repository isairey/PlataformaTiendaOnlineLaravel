<?php

namespace App\Imports;

use App\Models\ProductosModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductoImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new ProductosModel([
            'codigo' => $row['codigo'],
            'categoria' => $row['categoria'],
            'marca' => $row['marca'],
            'talla' => $row['talla'],
            'referencia' => $row['referencia'],
            'color' => $row['color'],
            'precio' => $row['precio'],
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
