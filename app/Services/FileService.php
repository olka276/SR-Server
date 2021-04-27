<?php


namespace App\Services;

use App\Models\File;
use App\Repositories\FileRepository;
use http\Exception\BadConversionException;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

/**
 * Class FileService
 * @package App\Services
 */
class FileService
{
    /**
     *
     */
    const DIR_PREFIX = 'public/uploads/';
    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * FileService constructor.
     * @param FileRepository $fileRepository
     */
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

        $path = self::DIR_PREFIX.$name.".".$extension;

        Storage::put($path, $file);

        $fileData = [
            'name' => $name,
            'extension' => $extension,
            'storage_path' => $path
        ];

        return $this
            ->fileRepository
            ->create($fileData);
    }

    /**
     * @param $name
     * @param $extension
     * @return string
     */
    public function getBase64($name, $extension): string
    {
        $file = $this->fileRepository->findByAttribute([
            'name' => $name,
            'extension' => $extension
        ]);

        if(is_null($file)) {
            throw new FileNotFoundException('File not found');
        }

        $filepath = $file->storage_path;

        return base64_encode(Storage::get($filepath));
    }

    /**
     * @return File[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllFiles() {
        return $this->fileRepository->getAll();
    }
}
