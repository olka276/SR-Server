<?php


namespace App\Repositories;


use App\Models\File;

/**
 * Class FileRepository
 * @package App\Repositories
 */
class FileRepository
{
    /**
     * @param $data
     * @return File
     */
    public function create($data): File
    {
        $file = new File($data);
        $file->save();
        return $file;
    }

    /**
     * @param $attributes
     * @return File
     */
    public function findByAttribute($attributes): File
    {
        $query = File::query();

        foreach($attributes as $key=>$value) {
            $query = $query->where($key, $value);
        }

        return $query->first();
    }

    /**
     * @return mixed
     */
    public function getAll() {
        return File::orderBy('created_at', 'desc')->get();
    }
}
