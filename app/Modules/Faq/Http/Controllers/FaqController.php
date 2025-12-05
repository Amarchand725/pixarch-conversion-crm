<?php

namespace App\Modules\Faq\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Modules\Faq\Repositories\Contracts\FaqContract;
use App\Modules\Faq\Http\Requests\FaqRequest;
use App\Modules\Faq\Models\Faq;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class FaqController extends BaseModuleController
{
    public function __construct(
        protected FaqContract $faqRepo
    ){
        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $permissionPrefix = $this->permissionPrefix;
        $routeInitialize  = $this->routePrefix;
        $singularLabel    = $this->singularLabel;

        $columns = [
            'question'      => ['label' => 'Question', 'searchable' => 'question'],
            'answer'     => ['label' => 'Answer', 'searchable' => 'answer'],
            'status'     => ['label' => 'Status', 'html' => true, 'searchable' => false],
            'author_id'     => ['label' => 'Author', 'html' => true, 'searchable' => false],
            'created_at' => ['label' => 'Created At', 'searchable' => 'created_at'],
            'action'     => ['label' => 'Action', 'html' => true, 'searchable' => false],
        ];

        $query = $this->faqRepo->getAll();

        $dataTable = new \App\Services\DataTableService(
            model: $query,
            columns: $columns,
            rowFormatter: function ($row) use ($routeInitialize, $permissionPrefix, $singularLabel) {
                $status = $row->status?->name ?? 'de-active';
                $row->status = '<span class="badge rounded-pill px-3 py-2 '. badgeClass($status) .'">'
                            . strtoupper($status) .
                            '</span>';

                $row->action = view('back-office.partials.action-buttons', [
                    'model'            => $row,
                    'permissionPrefix' => $permissionPrefix,
                    'routeInitialize'  => $routeInitialize,
                    'singularLabel'    => $singularLabel,
                ])->render();

                return $row;
            }
        );

        if ($request->ajax() && $request->loaddata == "yes") {
            return $dataTable->ajax();
        }

        return view(strtolower($this->pathInitialize.'.index'), $this->viewWithVars(get_defined_vars()));
    }


    public function create()
    {
        return (string) view($this->pathInitialize.'.create_content', get_defined_vars());
    }

    public function store(FaqRequest $request)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload) {
                $this->faqRepo->storeModel($payload);
            });
            return successResponse($response, $this->singularLabel. ' registered successfully.');
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit(Faq $faq)
    {
        $model = $this->faqRepo->showModel($faq);
        return (string) view($this->pathInitialize.'.edit_content', get_defined_vars());
    }

    public function update(FaqRequest $request, Faq $faq)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $faq) {
                $this->faqRepo->updateModel($faq, $payload);
            });
            return successResponse([], $this->singularLabel. ' updated successfully.');
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(Faq $faq)
    {
        $model = $this->faqRepo->showModel($faq);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    public function destroy(Faq $faq)
    {
        try {
            if($this->faqRepo->softDeleteModel($faq)) {
                return response()->json([
                    'status' => true,
                    'message' => $this->singularLabel.' Deleted Successfully'
                ]);
            } else{
                return response()->json([
                    'status' => false,
                    'error' => $this->singularLabel.' not deleted try again.'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function restore(Faq $faq)
    {
        try {
            if($this->faqRepo->restoreModel($faq)) {
                return redirect()->back()->with('message', 'Record Restored Successfully.');
            } else {
                return false;
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function forceDelete(Faq $faq)
    {
        try {
            if ($this->faqRepo->permanentlyDeleteModel($faq)) {
                return response()->json([
                    'status' => true,
                    'message' => $this->singularLabel.' Deleted Successfully'
                ]);
            } else{
                return response()->json([
                    'status' => true,
                    'error' => $this->singularLabel.' not deleted try again.'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function bulkDelete()
    {
        try {
            $this->faqRepo->bulkDelete();
            return redirect()->route(strtolower('faqs.index'))->with('success', 'Bulk delete successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->faqRepo->bulkRestore();
            return redirect()->route(strtolower('faqs.index'))->with('success', 'Bulk restore successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}