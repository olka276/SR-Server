<?php


namespace App\Services;

use App\Models\File;
use App\Repositories\FileRepository;
use http\Exception\BadConversionException;
use Illuminate\Support\Facades\Storage;

/**
 * Class FileService
 * @package App\Services
 */
class FileService
{
    private $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param $base64
     * @param $name
     * @param $extension
     * @return File
     */
    public function saveFile($base64, $name, $extension): File
    {
        $file = base64_decode($base64);

        if(is_null($file)) {
            throw new BadConversionException('File created from base64 is null');
        }

        $path = 'public/'.$name.".".$extension;

        Storage::put($path, $file);

        $fileData = [
            'name' => $name,
            'extension' => $extension,
            'storage_path' => Storage::url($path)
        ];

        return $this
            ->fileRepository
            ->create($fileData);
    }
}
