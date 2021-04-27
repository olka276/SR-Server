<?php

namespace App\Http\Controllers;

use App\Services\FileService;
use http\Exception\BadConversionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FileController
 * @package App\Http\Controllers
 */
class FileController extends Controller
{
    /**
     * @var FileService
     */
    private $fileService;

    /**
     * FileController constructor.
     * @param FileService $fileService
     */
    public function __construct(FileService $fileService) {
        $this->fileService = $fileService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadFile(Request $request) {
        $data = $request->only([
            'base64',
            'name',
            'extension'
        ]);

        $validator = Validator::make($data, [
            'base64' => 'required|string',
            'name' => [
                'required',
                Rule::unique('files')->where(function ($query) use ($data) {
                    return $query->where('name', $data['name'])
                        ->where('extension', $data['extension']);
                })
            ],
            'extension' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag()
                ], Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $file = $this->fileService->saveFile(
                $data['base64'],
                $data['name'],
                $data['extension']
            );
        } catch (BadConversionException $e) {
            return response()->json([
                'message' => 'File conversion failure.'
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'message' => 'File created successfully.',
            'file' => $file
        ], Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function downloadFile(Request $request): JsonResponse
    {
        $data = $request->all();

        try {
            $base64 = $this
                ->fileService
                ->getBase64($data['name'], $data['extension']);
        } catch (FileNotFoundException $e) {
            return response()->json([
                'message' => 'File not found.'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'File fetched successfully.',
            'file' => $base64
        ], Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListOfFiles(): JsonResponse
    {
        $files = $this
            ->fileService
            ->getAllFiles();

        return response()->json([
            'message' => 'Files fetched successfully.',
            'file' => $files
        ], Response::HTTP_OK);
    }
}
