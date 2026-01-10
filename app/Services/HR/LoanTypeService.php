<?php

namespace App\Services\HR;

use App\Models\HR\LoanType;
use App\Repositories\HR\LoanTypeRepository;
use Illuminate\Support\Facades\Validator;

class LoanTypeService
{
    protected LoanTypeRepository $repository;

    public function __construct(LoanTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    public function getPaginated(array $filters = [], int $perPage = 15)
    {
        return $this->repository->getPaginated($filters, $perPage);
    }

    public function getActive()
    {
        return $this->repository->getActive();
    }

    public function findById(int $id): ?LoanType
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): LoanType
    {
        $validated = $this->validate($data);
        return $this->repository->create($validated);
    }

    public function update(LoanType $loanType, array $data): LoanType
    {
        $validated = $this->validate($data, $loanType);
        $this->repository->update($loanType, $validated);
        return $loanType->fresh();
    }

    public function delete(LoanType $loanType): bool
    {
        if ($loanType->loans()->count() > 0) {
            throw new \Exception('Cannot delete loan type with existing loans.');
        }
        return $this->repository->delete($loanType);
    }

    protected function validate(array $data, ?LoanType $loanType = null): array
    {
        $rules = [
            'name_json' => ['required', 'array'],
            'name_json.ar' => ['required', 'string', 'max:255'],
            'name_json.en' => ['required', 'string', 'max:255'],
            'description_json' => ['nullable', 'array'],
            'description_json.ar' => ['nullable', 'string'],
            'description_json.en' => ['nullable', 'string'],
            'max_amount' => ['required', 'numeric', 'min:0'],
            'max_installments' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
        ];

        $validator = Validator::make($data, $rules);
        return $validator->validate();
    }
}
