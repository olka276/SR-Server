<?php

namespace App\Http\Controllers;

use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    private $fileService;

    public function __construct(FileService $fileService) {
        $this->fileService = $fileService;
    }

    public function saveFile(Request $request) {
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

        $file = $this->fileService->saveFile(
            $data['base64'],
            $data['name'],
            $data['extension']
        );

        return response()->json([
            'message' => 'File created successfully.',
            'file' => $file
        ], Response::HTTP_CREATED);

    }
}
