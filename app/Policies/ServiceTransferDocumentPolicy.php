<?php

namespace App\Policies;

use App\Models\User;
use Modules\ServiceTransfer\Entities\ServiceTransferDocument;

class ServiceTransferDocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('service_transfer.view');
    }

    public function view(User $user, ServiceTransferDocument $document): bool
    {
        return $user->can('service_transfer.view') || $user->can('service_transfers.documents.view');
    }

    public function create(User $user): bool
    {
        return $user->can('service_transfers.documents.upload');
    }

    public function delete(User $user, ServiceTransferDocument $document): bool
    {
        return $user->can('service_transfers.documents.delete');
    }
}
