<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $documents = ContractDocument::with(['contract.creator'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('original_name', 'like', '%'.$search.'%')
                        ->orWhereHas('contract', function ($contract) use ($search) {
                            $contract->where('number', 'like', '%'.$search.'%')
                                ->orWhere('subject', 'like', '%'.$search.'%');
                        });
                });
            })
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('documents.index', [
            'documents' => $documents,
            'search' => $search,
        ]);
    }

    public function preview(Contract $contract, ContractDocument $document)
    {
        $this->guard($contract, $document);

        return response()->file(Storage::path($document->path), [
            'Content-Type' => $document->mime,
            'Content-Disposition' => 'inline; filename="'.$this->safeName($document->original_name).'"',
        ]);
    }

    public function download(Contract $contract, ContractDocument $document)
    {
        $this->guard($contract, $document);

        return Storage::download($document->path, $this->safeName($document->original_name));
    }

    private function guard(Contract $contract, ContractDocument $document)
    {
        if ((int) $document->contract_id !== (int) $contract->id) {
            abort(404);
        }
    }

    private function safeName($name)
    {
        $name = str_replace(['"', '\\', "\r", "\n"], '', (string) $name);

        return $name !== '' ? $name : 'dokumen';
    }
}
