<?php

namespace App\Services;

use App\Models\Rule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class RuleService
{
    private FileStorageService $fileStorageService;

    public function __construct(FileStorageService $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }

    public function getAllRules(): Collection
    {
        return Rule::all();
    }

    public function getAllActiveRules(): Collection
    {
        return Rule::where('is_active', true)->get();
    }

    public function getRuleById(int $id): ?Rule
    {
        return Rule::find($id);
    }

    public function createRule(array $ruleData): Rule
    {
        return Rule::create($ruleData);
    }

    public function updateRule(Rule $rule, array $ruleData): bool
    {
        return $rule->update($ruleData);
    }

    public function deleteRule(Rule $rule): bool
    {
        if ($rule->icon_image) {
            $this->fileStorageService->deleteFile($rule->icon_image);
        }

        return $rule->delete();
    }

    public function toggleRuleStatus(int $ruleId): bool
    {
        $rule = Rule::find($ruleId);

        if (! $rule) {
            return false;
        }

        return $rule->update(['is_active' => ! $rule->is_active]);
    }

    public function updateRuleOrder(int $id, int $position): void
    {
        $rule = Rule::findOrFail($id);
        $rules = Rule::where('section', $rule->section)->orderBy('order')->get()
            ->reject(fn ($r) => $r->id === $rule->id)->values();
        $rules->splice($position, 0, [$rule]);

        foreach ($rules as $index => $r) {
            if ($r->order !== $index) {
                $r->update(['order' => $index]);
            }
        }
    }

    public function storeRuleIcon(UploadedFile $file): string
    {
        return $this->fileStorageService->storeFile(
            $file,
            FileStorageService::FEATURE_ICON_PATH
        );
    }

    public function updateRuleIcon(Rule $rule, UploadedFile $file): string
    {
        if ($rule->icon_image) {
            $this->fileStorageService->deleteFile($rule->icon_image);
        }

        return $this->storeRuleIcon($file);
    }
}
