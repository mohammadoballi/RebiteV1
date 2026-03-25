<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function register(array $data): User
    {
        $role = $data['role'] ?? 'donor';
        unset($data['role'], $data['password_confirmation']);

        $data['password'] = Hash::make($data['password']);
        $data['status'] = User::STATUS_PENDING;

        $user = $this->userRepository->create($data);
        $user->addRole($role);

        return $user;
    }

    public function approve(int $id): bool
    {
        return $this->userRepository->approve($id);
    }

    public function reject(int $id, string $reason): bool
    {
        return $this->userRepository->reject($id, $reason);
    }

    public function updateProfile(int $id, array $data): bool
    {
        unset($data['password'], $data['status'], $data['role_type']);

        return $this->userRepository->update($id, $data);
    }

    public function changePassword(int $id, string $password): bool
    {
        return $this->userRepository->update($id, [
            'password' => Hash::make($password),
        ]);
    }

    public function uploadAvatar(int $id, UploadedFile $file): string
    {
        $user = $this->userRepository->findOrFail($id);

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $file->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        return $path;
    }

    public function getDatatableData()
    {
        return $this->userRepository->getDatatableQuery();
    }
}
