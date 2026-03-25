<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index()
    {
        return view('admin.users.index');
    }

    public function datatable(Request $request): JsonResponse
    {
        $query = $this->userService->getDatatableData();

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        return DataTables::eloquent($query)
            ->addColumn('role', fn (User $user) => $user->roles->pluck('display_name')->implode(', '))
            ->addColumn('status_badge', function (User $user) {
                $colors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
                $color = $colors[$user->status] ?? 'secondary';
                return '<span class="badge bg-' . $color . '">' . ucfirst($user->status) . '</span>';
            })
            ->addColumn('actions', function (User $user) {
                $btns = '<button class="btn btn-sm btn-outline-info btn-view-user" data-id="'.$user->id.'" title="View"><i class="fas fa-eye"></i></button> ';
                $btns .= '<button class="btn btn-sm btn-outline-primary btn-edit-user" data-id="'.$user->id.'" title="Edit"><i class="fas fa-edit"></i></button> ';
                if ($user->status === 'pending') {
                    $btns .= '<button class="btn btn-sm btn-outline-success btn-approve-inline" data-id="'.$user->id.'" title="Approve"><i class="fas fa-check"></i></button> ';
                    $btns .= '<button class="btn btn-sm btn-outline-warning btn-reject-inline" data-id="'.$user->id.'" title="Reject"><i class="fas fa-ban"></i></button> ';
                }
                $btns .= '<button class="btn btn-sm btn-outline-danger btn-delete-user" data-id="'.$user->id.'" title="Delete"><i class="fas fa-trash"></i></button>';
                return $btns;
            })
            ->rawColumns(['status_badge', 'actions'])
            ->toJson();
    }

    public function show(int $id): JsonResponse
    {
        $user = User::with('roles')->findOrFail($id);

        return response()->json($user);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'unique:users,email,' . $id],
            'phone'             => ['nullable', 'string', 'max:20'],
            'status'            => ['required', 'in:pending,approved,rejected'],
            'city'              => ['nullable', 'string', 'max:255'],
            'address'           => ['nullable', 'string', 'max:500'],
            'organization_name' => ['nullable', 'string', 'max:255'],
            'rejection_reason'  => ['nullable', 'required_if:status,rejected', 'string', 'max:500'],
        ]);

        $user = User::findOrFail($id);

        $user->update($request->only([
            'name', 'email', 'phone', 'status', 'city',
            'address', 'organization_name', 'rejection_reason',
        ]));

        return response()->json(['message' => __('User updated successfully.')]);
    }

    public function approve(int $id): JsonResponse
    {
        $this->userService->approve($id);

        return response()->json(['message' => __('User approved successfully.')]);
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $this->userService->reject($id, $request->input('reason'));

        return response()->json(['message' => __('User rejected.')]);
    }

    public function destroy(int $id): JsonResponse
    {
        User::findOrFail($id)->delete();

        return response()->json(['message' => __('User deleted.')]);
    }
}
