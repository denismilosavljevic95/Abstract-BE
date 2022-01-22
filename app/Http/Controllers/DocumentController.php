<?php

namespace App\Http\Controllers;

use ZipArchive;
use App\Models\User;
use App\Models\Document;
use App\Jobs\ZipDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{

    public function readAll(Request $request): array {
        $id = Auth::user()['id'];
        $user = User::find($id);
        $documents = $user->documents;
        return [
            'documents' => $documents
        ];
    }
    
    public function create(Request $request): array {
        $user = Auth::user();

        $name = $request->input('name');
        $document = $request->file('document');

        $documentFullName = $document->getClientOriginalName();
        if (empty($name)) {
            $documentName = pathinfo($documentFullName, PATHINFO_FILENAME);
        } else {
            $documentName = $name;
        }
        $documentExtension = pathinfo($documentFullName, PATHINFO_EXTENSION);
        $filePath = $documentName . '.' . $documentExtension;
        $zipPath = $documentName . '.zip';

        if (file_exists(public_path('assets/' . $filePath))) {
            return ['message' => 'File already exist!'];
        }

        $documentObj = Document::create([
            'fileName' => $documentName,
            'filePath' => $filePath,
            'zipPath' => $zipPath,
            'user_id' => $user['id'],
            'archive' => 0
        ]);

        $document->move('assets', $filePath);

        ZipDocument::dispatch($name, $filePath);
        
        return [
            'Document' => $documentObj
        ];
    }

    public function readOne(Request $request): array {
        $document = $this->getDocument($request);
        return [
            'document' => $document
        ];
    }

    public function update(Request $request): array {
        $document = $this->getDocument($request);
        $zip = new ZipArchive();
        $zip_status = $zip->open(public_path('assets/' . $document['zipPath']), ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);

        if (!!$zip_status) {
            $zip->addFile(public_path('assets/' . $document['filePath']), $document['filePath']);
            $zip->setEncryptionName($document['filePath'], ZipArchive::EM_AES_256, $request->input('password'));
            $zip->close();
            return ['message' => 'Successfuly add password!'];
        } else {
            return ['message' => "Failed opening archive: ". @$zip->getStatusString() . " (code: ". $zip_status .")"];
        }
    }

    public function delete(Request $request): array {
        $documentID = $request->get('id');
        $userID = Auth::user()['id'];
        Document::where('id', '=', $documentID)->where('user_id', '=', $userID)->update(['archive' => 1]);
        return ['message' => 'Successfuly deleted!'];
    }

    public function download(Request $request) {
        $document = $this->getDocument($request);
        return response()->download(public_path('assets/' . $document['filePath']));
    }

    private function getDocument(Request $request) {
        $documentID = $request->get('id') ?? $request->input('id');
        $userID = Auth::user()['id'];
        return Document::where('id', '=', $documentID)->where('user_id', '=', $userID)->first();
    }
}