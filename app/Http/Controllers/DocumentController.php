<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DocumentService;

class DocumentController extends Controller
{
    protected $documentService;

    public function __construct(DocumentService $DocumentService)
    {
        $this->documentService = $DocumentService;
    }

    public function readAll(Request $request) {
        return $this->documentService->readAll();
    }
    
    public function create(Request $request) {
        $name = $request->input('name') ?? null;
        $document = $request->file('document');

        $response = $this->documentService->create($document, $name);

        return response($response['data'], $response['status']);
    }

    public function readOne(Request $request) {
        $document = $this->documentService->readOne($request->get('id'));
        return [
            'document' => $document
        ];
    }

    public function update(Request $request) {
        if(empty($request->input('password'))) {
            return response([
                'message' => 'Password cannot be empty!'
            ], 400);
        }

        return $this->documentService->update($request->input('id'), $request->input('password'));
    }

    public function delete(Request $request) {
        return $this->documentService->delete($request->get('id'));
    }

    public function download(Request $request) {
        return $this->documentService->download($request->get('id'));
    }
}