<?php

namespace App\Http\Controllers;

use App\Http\Requests\RejectContractRequest;
use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Models\Activity;
use App\Models\Contract;
use App\Models\User;
use App\Services\ContractNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $query = Contract::query()->with(['creator', 'approver', 'documents']);
        $search = trim((string) $request->query('q', ''));

        if ($search !== '') {
            $query->where(function ($inner) use ($search) {
                $inner->where('number', 'like', '%'.$search.'%')
                    ->orWhere('subject', 'like', '%'.$search.'%');
            });
        }

        $status = $request->query('status');
        if (in_array($status, Contract::STATUSES, true)) {
            $query->where('status', $status);
        }

        $from = $this->dateOrNull($request->query('from'));
        $to = $this->dateOrNull($request->query('to'));

        if ($from) {
            $query->whereDate('activation_date', '>=', $from);
        }
        if ($to) {
            $query->whereDate('activation_date', '<=', $to);
        }
        if ($request->filled('creator') && ctype_digit((string) $request->query('creator'))) {
            $query->where('created_by', (int) $request->query('creator'));
        }

        $this->applySort($query, (string) $request->query('sort', 'created'), $request->query('dir') === 'asc' ? 'asc' : 'desc');

        $contracts = $query->paginate(5)->withQueryString();
        $counts = Contract::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $dialog = null;
        $dialogContract = null;

        if ($request->user()->isSupervisor()) {
            foreach (['reject', 'approve', 'delete'] as $key) {
                if (! $request->filled($key)) {
                    continue;
                }

                $candidate = Contract::with(['creator', 'approver', 'documents'])->find($request->query($key));
                if (! $candidate) {
                    break;
                }
                if ($key !== 'delete' && ! $candidate->isPending()) {
                    break;
                }

                $dialog = $key;
                $dialogContract = $candidate;
                break;
            }
        }

        $detail = null;
        if (! $dialog && $request->filled('detail')) {
            $detail = Contract::with(['creator', 'approver', 'documents'])->find($request->query('detail'));
        }

        $previewDocument = null;
        if ($detail && $detail->documents->isNotEmpty()) {
            $previewDocument = $detail->documents->first();
            if ($request->filled('preview')) {
                $chosen = $detail->documents->firstWhere('id', (int) $request->query('preview'));
                if ($chosen) {
                    $previewDocument = $chosen;
                }
            }
        }

        return view('contracts.index', [
            'contracts' => $contracts,
            'counts' => $counts,
            'totalContracts' => (int) $counts->sum(),
            'creators' => User::query()->where('role', 'staff')->orderBy('name')->get(),
            'activeStatus' => in_array($status, Contract::STATUSES, true) ? $status : 'all',
            'detail' => $detail,
            'previewDocument' => $previewDocument,
            'dialog' => $dialog,
            'dialogContract' => $dialogContract,
            'selectedId' => $dialogContract ? $dialogContract->id : ($detail ? $detail->id : null),
        ]);
    }

    public function create()
    {
        return view('contracts.form', [
            'contract' => null,
            'previewNumber' => ContractNumber::peek(),
        ]);
    }

    public function store(StoreContractRequest $request)
    {
        $contract = ContractNumber::withLock(function () use ($request) {
            $contract = Contract::create([
                'number' => ContractNumber::issue(),
                'subject' => $request->input('subject'),
                'activation_date' => $request->input('activation_date'),
                'created_by' => $request->user()->id,
                'status' => Contract::STATUS_PENDING,
            ]);

            $stored = $this->storeUploads($contract, $request->file('documents') ?: []);
            Activity::record($request->user(), $contract, $request->user()->name.' membuat '.$contract->number);

            if ($stored > 0) {
                Activity::record($request->user(), $contract, $request->user()->name.' mengunggah lampiran '.$contract->number);
            }

            return $contract;
        });

        return redirect()
            ->route('contracts.index', ['detail' => $contract->id])
            ->with('status', $contract->number.' disimpan sebagai Menunggu.');
    }

    public function edit(Contract $contract)
    {
        if (! $contract->isPending()) {
            return redirect()
                ->route('contracts.index', ['detail' => $contract->id])
                ->with('status', 'Kontrak yang sudah disetujui atau ditolak tidak bisa diedit.');
        }

        $contract->load(['documents', 'creator']);

        return view('contracts.form', [
            'contract' => $contract,
            'previewNumber' => $contract->number,
        ]);
    }

    public function update(UpdateContractRequest $request, Contract $contract)
    {
        if (! $contract->isPending()) {
            return redirect()
                ->route('contracts.index', ['detail' => $contract->id])
                ->with('status', 'Kontrak yang sudah disetujui atau ditolak tidak bisa diedit.');
        }

        DB::transaction(function () use ($request, $contract) {
            $contract->update([
                'subject' => $request->input('subject'),
                'activation_date' => $request->input('activation_date'),
            ]);

            $remove = $request->input('remove_documents', []);
            if ($remove) {
                $documents = $contract->documents()->whereIn('id', $remove)->get();
                foreach ($documents as $document) {
                    Storage::delete($document->path);
                    $document->delete();
                }
            }

            $stored = $this->storeUploads($contract, $request->file('documents') ?: []);

            if ($stored > 0) {
                Activity::record($request->user(), $contract, $request->user()->name.' mengunggah lampiran '.$contract->number);
            } else {
                Activity::record($request->user(), $contract, $request->user()->name.' mengubah '.$contract->number);
            }
        });

        return redirect()
            ->route('contracts.index', ['detail' => $contract->id])
            ->with('status', $contract->number.' diperbarui.');
    }

    public function approve(Contract $contract)
    {
        if (! $contract->isPending()) {
            return redirect()
                ->route('contracts.index')
                ->with('status', 'Kontrak ini sudah tidak menunggu tinjauan.');
        }

        $contract->update([
            'status' => Contract::STATUS_APPROVED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        Activity::record(auth()->user(), $contract, auth()->user()->name.' menyetujui '.$contract->number);

        return redirect()
            ->route('contracts.index')
            ->with('status', $contract->number.' disetujui.');
    }

    public function reject(RejectContractRequest $request, Contract $contract)
    {
        if (! $contract->isPending()) {
            return redirect()
                ->route('contracts.index')
                ->with('status', 'Kontrak ini sudah tidak menunggu tinjauan.');
        }

        $contract->update([
            'status' => Contract::STATUS_REJECTED,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => $request->input('reason'),
        ]);

        Activity::record($request->user(), $contract, $request->user()->name.' menolak '.$contract->number);

        return redirect()
            ->route('contracts.index')
            ->with('status', $contract->number.' ditolak.');
    }

    public function destroy(Contract $contract)
    {
        $number = $contract->number;

        DB::transaction(function () use ($contract) {
            Storage::deleteDirectory('contracts/'.$contract->id);
            $contract->delete();
            Activity::record(auth()->user(), null, auth()->user()->name.' menghapus '.$contract->number);
        });

        return redirect()
            ->route('contracts.index')
            ->with('status', $number.' dihapus.');
    }

    private function storeUploads(Contract $contract, array $files)
    {
        $count = 0;

        foreach ($files as $file) {
            if (! $file) {
                continue;
            }

            $name = basename($file->getClientOriginalName());
            $path = $file->store('contracts/'.$contract->id);
            $contract->documents()->create([
                'original_name' => $name !== '' ? $name : 'dokumen',
                'path' => $path,
                'mime' => $file->getMimeType() ?: 'application/octet-stream',
                'size' => $file->getSize() ?: 0,
            ]);
            $count++;
        }

        return $count;
    }

    private function applySort($query, $sort, $dir)
    {
        if ($sort === 'creator') {
            $query->leftJoin('users as sort_creators', 'sort_creators.id', '=', 'contracts.created_by')
                ->select('contracts.*')
                ->orderBy('sort_creators.name', $dir)
                ->orderBy('contracts.id', 'desc');

            return;
        }

        if ($sort === 'approver') {
            $query->leftJoin('users as sort_approvers', 'sort_approvers.id', '=', 'contracts.approved_by')
                ->select('contracts.*')
                ->orderBy('sort_approvers.name', $dir)
                ->orderBy('contracts.id', 'desc');

            return;
        }

        $columns = [
            'number' => 'contracts.number',
            'subject' => 'contracts.subject',
            'activation' => 'contracts.activation_date',
            'status' => 'contracts.status',
            'approved' => 'contracts.approved_at',
            'created' => 'contracts.created_at',
        ];

        $query->orderBy(isset($columns[$sort]) ? $columns[$sort] : 'contracts.created_at', $dir)
            ->orderBy('contracts.id', 'desc');
    }

    private function dateOrNull($value)
    {
        if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        return $value;
    }
}
