<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Document;
use App\Jobs\ZipDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        if (file_exists(public_path('assets/' . $filePath))) {
            return ['message' => 'File already exist!'];
        }

        $documentObj = Document::create([
            'fileName' => $documentName,
            'filePath' => $filePath,
            'zipPath' => $filePath,
            'user_id' => $user['id']
        ]);

        $document->move('assets', $filePath);

        ZipDocument::dispatch($name, $filePath);
        
        return [
            'Document' => $documentObj
        ];
    }

    public function readOne(Request $request): array {
        $documentID = $request->get('id');
        $userID = Auth::user()['id'];
        $document = Document::where('id', '=', $documentID)->where('user_id', '=', $userID)->first();
        return [
            'document' => $document
        ];
    }

    public function delete(Request $request): array {
        $documentID = $request->get('id');
        $userID = Auth::user()['id'];
        $file_path = Document::where('id', '=', $documentID)->where('user_id', '=', $userID)->first()['filePath'];
        $document = Document::where('id', '=', $documentID)->where('user_id', '=', $userID)->delete();
        unlink(public_path('assets/' . $file_path));
        return [
            'message' => $document
        ];
    }

    public function download(Request $request) {
        $documentID = $request->get('id');
        $userID = Auth::user()['id'];
        $document = Document::where('id', '=', $documentID)->where('user_id', '=', $userID)->first();
        return response()->download(public_path('assets/' . $document['filePath']));
    }
}