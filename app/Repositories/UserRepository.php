<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function getPending(): Collection
    {
        return $this->query()->pending()->get();
    }

    public function getApproved(): Collection
    {
        return $this->query()->approved()->get();
    }

    public function getByRole(string $role): Collection
    {
        return $this->query()->byRole($role)->get();
    }

    public function approve(int $id): bool
    {
        return $this->model->findOrFail($id)->update([
            'status' => User::STATUS_APPROVED,
            'rejection_reason' => null,
        ]);
    }

    public function reject(int $id, string $reason): bool
    {
        return $this->model->findOrFail($id)->update([
            'status' => User::STATUS_REJECTED,
            'rejection_reason' => $reason,
        ]);
    }

    public function getDatatableQuery()
    {
        return $this->query()->with('roles')->select([
            'id', 'name', 'email', 'phone', 'role_type',
            'status', 'city', 'created_at',
        ]);
    }
}
