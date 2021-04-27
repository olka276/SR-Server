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
}
