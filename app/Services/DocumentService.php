<?php

namespace App\Services;

use ZipArchive;
use App\Models\User;
use App\Models\Document;
use App\Jobs\ZipDocument;
use Illuminate\Support\Facades\Auth;

class DocumentService
{
    /**
     * READ SECTION
     */
    public function readOne($documentID) {
        $documentModel = new Document();
        return $documentModel->getOne($documentID);
    }

    public function readAll() {
        $id = Auth::user()['id'];
        $userModel = new User();
        $user = $userModel->readOneByID($id);
        return [
            'documents' => $user->documents
        ];
    }

    public function download($documentID) {
        $document = $this->readOne($documentID);
        return response()->download(public_path('assets/' . $document['filePath']));
    }

    /**
     * CREATE SECTION
     */
    public function create($document, $name) {
        $user = Auth::user();
        $documentFullName = $document->getClientOriginalName();
        if (empty($name)) {
            $documentName = pathinfo($documentFullName, PATHINFO_FILENAME);
        } else {
            $documentName = $name;
        }
        $documentExtension = pathinfo($documentFullName, PATHINFO_EXTENSION);
        $filePath = $user['id'] . '_' . $documentName . '.' . $documentExtension;
        $zipPath = $user['id'] . '_' . $documentName . '.zip';
        if (file_exists(public_path('assets/' . $filePath))) {
            return [
                'data' => [
                    'message' => 'File already exist!'
                ],
                'status' => 400
            ];
        }

        $documentObj = Document::create([
            'fileName' => $documentName,
            'filePath' => $filePath,
            'zipPath' => $zipPath,
            'user_id' => $user['id'],
            'archive' => 0
        ]);

        $document->move('assets', $filePath);

        ZipDocument::dispatch($documentName, $filePath);

        return [
            'data' => [
                'document' => $documentObj
            ], 
            'status' => 201
        ];
    }

    /**
     * UPDATE SECTION
     */
    public function update($documentID, $password) {
        $document = $this->readOne($documentID);
        $zip = new ZipArchive();
        $zip_status = $zip->open(public_path('assets/' . $document['zipPath']), ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);

        if (!!$zip_status) {
            $zip->addFile(public_path('assets/' . $document['filePath']), $document['filePath']);
            $zip->setEncryptionName($document['filePath'], ZipArchive::EM_AES_256, $password);
            $zip->close();
            return ['message' => 'Successfully add password!'];
        } else {
            return response([
                'message' => "Failed opening archive: ". @$zip->getStatusString() . " (code: ". $zip_status .")"
            ], $zip_status);
        }
    }

    /**
     * DELETE SECTION
     */
    public function delete($documentID) {
        $documentModel = new Document();
        $documentModel->archive($documentID);
        return ['message' => 'Successfuly deleted!'];
    }
}