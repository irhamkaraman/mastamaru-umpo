<?php

namespace App\Policies;

use App\Models\CertificateTemplate;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CertificateTemplatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_certificate_template');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CertificateTemplate $certificateTemplate): bool
    {
        return $user->can('view_certificate_template');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_certificate_template');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CertificateTemplate $certificateTemplate): bool
    {
        return $user->can('update_certificate_template');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CertificateTemplate $certificateTemplate): bool
    {
        return $user->can('delete_certificate_template');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_certificate_template');
    }
}
