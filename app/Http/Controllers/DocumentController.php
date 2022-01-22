<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use League\Flysystem\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        $documentName = pathinfo($documentFullName, PATHINFO_FILENAME);
        $documentExtension = pathinfo($documentFullName, PATHINFO_EXTENSION);
        $document = Document::create([
            'fileName' => $documentFullName,
            'user_id' => $user['id']
        ]);
        Log::debug($name);
        Storage::disk('local')->put($documentFullName, file_get_contents($document));
        return [
            'Document' => $document
        ];
    }

    public function readOne(Request $request): array {
        $documentID = $request->get('id');
        $userID = Auth::user()['id'];
        $document = Document::where('id', '=', $documentID)->where('user_id', '=', $userID)->get();
        return [
            'document' => $document
        ];
    }

    public function delete(Request $request): array {
        $documentID = $request->get('id');
        $userID = Auth::user()['id'];
        $document = Document::where('id', '=', $documentID)->where('user_id', '=', $userID)->delete();
        return [
            'message' => $document
        ];
    }
}