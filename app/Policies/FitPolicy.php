<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Fit;
use Illuminate\Auth\Access\HandlesAuthorization;

class FitPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Fit');
    }

    public function view(AuthUser $authUser, Fit $fit): bool
    {
        return $authUser->can('View:Fit');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Fit');
    }

    public function update(AuthUser $authUser, Fit $fit): bool
    {
        return $authUser->can('Update:Fit');
    }

    public function delete(AuthUser $authUser, Fit $fit): bool
    {
        return $authUser->can('Delete:Fit');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Fit');
    }

    public function restore(AuthUser $authUser, Fit $fit): bool
    {
        return $authUser->can('Restore:Fit');
    }

    public function forceDelete(AuthUser $authUser, Fit $fit): bool
    {
        return $authUser->can('ForceDelete:Fit');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Fit');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Fit');
    }

    public function replicate(AuthUser $authUser, Fit $fit): bool
    {
        return $authUser->can('Replicate:Fit');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Fit');
    }

}