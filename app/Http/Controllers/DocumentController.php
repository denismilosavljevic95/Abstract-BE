<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use Illuminate\Http\Request;
use App\Services\DocumentService;

class DocumentController extends Controller
{
    protected $documentService;
    protected $helpers;

    public function __construct(DocumentService $DocumentService, Helpers $Helpers)
    {
        $this->documentService = $DocumentService;
        $this->helpers = $Helpers;
    }

    public function readAll(Request $request) {
        $documents = $this->documentService->readAll();
        return $this->helpers->response($documents, 200);
    }
    
    public function create(Request $request) {
        $name = $request->input('name') ?? null;
        $document = $request->file('document');

        $response = $this->documentService->create($document, $name);

        return $this->helpers->response($response['data'], $response['status']);
    }

    public function readOne(Request $request) {
        $document = $this->documentService->readOne($request->get('id'));
        return $this->helpers->response($document, 200);
    }

    public function update(Request $request) {
        if(empty($request->input('password'))) {
            return $this->helpers->response(['message' => 'Password cannot be empty!'], 400);
        }

        $response = $this->documentService->update($request->input('id'), $request->input('password'));
        return $this->helpers->response($response, 200);
    }

    public function delete(Request $request) {
        $response = $this->documentService->delete($request->get('id'));
        return $this->helpers->response($response, 200);
    }

    public function download(Request $request) {
        return $this->documentService->download($request->get('id'));
    }
}