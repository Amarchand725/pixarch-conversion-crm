<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use Exception;
use Illuminate\Http\Request;

class NotificationController extends BaseModuleController
{
    public function __construct(){
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $permissionPrefix = $this->permissionPrefix;
        $routeInitialize  = $this->routePrefix;
        $singularLabel    = $this->singularLabel;

        $columns = [
            'data_display' => ['label' => 'Notification', 'html' => true, 'searchable' => false],
            'read_at'     => ['label' => 'Read At', 'html' => false, 'searchable' => false],
            'created_at' => ['label' => 'Created At', 'searchable' => 'created_at'],
            'action'     => ['label' => 'Action', 'html' => true, 'searchable' => false],
        ];

        $query = auth()->user()->notifications()->latest();

        $dataTable = new \App\Services\DataTableService(
            model: $query,
            columns: $columns,
            rowFormatter: function ($row) use ($routeInitialize, $permissionPrefix, $singularLabel) {
                $row['uuid'] = $row->id;
                $row->action = view('back-office.partials.actions', [
                    'model'            => $row,
                    'permissionPrefix' => $permissionPrefix,
                    'routeInitialize'  => $routeInitialize,
                    'singularLabel'    => $singularLabel,
                ])->render();

                $row->read = $row->read_at ? $row->read_at->format('M d, Y h:i A') : 'Unread';

                $data = $row->data;
                // Calculate human-readable time
                $created = $row->created_at;

                if ($created->isToday()) {
                    $humanTime = $created->diffForHumans();
                } elseif ($created->isYesterday()) {
                    $humanTime = "Yesterday at " . $created->format("h:i A");
                } else {
                    $humanTime = $created->format("M d \a\t h:i A");
                }

                $row->data_display = '
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-3">
                        <img src="'.($data['assigner_avatar'] ?? asset("back-office/assets/img/avatars/default-avatar.png")).'" 
                            class="rounded-circle" width="40" height="40" />
                    </div>
                    <div class="flex-grow-1">
                        <a href="javascript:void(0)" class="text-body fw-bold">
                            '.($data['title'] ?? 'No Title').'
                        </a>
                        <p class="mb-0">'.($data['message'] ?? '').'</p>
                        <small class="text-muted">'.$humanTime.'</small>
                    </div>
                </div>';

                return $row;
            }
        );

        if ($request->ajax() && $request->loaddata == "yes") {
            return $dataTable->ajax();
        }

        return view(strtolower($this->pathInitialize.'.index'), $this->viewWithVars(get_defined_vars()));
    }

    public function show($uuid) {
        $notify = auth()->user()
                ->unreadNotifications()
                ->where('id', $uuid)
                ->first();
        if($notify) {
            $notify->markAsRead();
            return response()->json([
                'flag' => true,
                'status' => true,
                'show' => true,
                'message' => 'Notification read successfully'
            ]);
        } else{
            return response()->json([
                'flag' => true,
                'status' => false,
                'show' => true,
                'error' => "Notification failed to read"
            ]);
        }
    }

    public function markAllRead() {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json([
            'flag' => true,
            'status' => true,
            'index' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    public function destroy($uuid)
    {
        try {
            if(auth()->user()->notifications()->where('id', $uuid)->delete()) {
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

    public function latest($uuid){
        $notification = auth()->user()
                ->unreadNotifications()
                ->where('id', $uuid)
                ->first();

        return (string) view($this->pathInitialize.'.item', get_defined_vars());
    }
}
