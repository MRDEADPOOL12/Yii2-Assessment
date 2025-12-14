<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Subscription;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
            'type' => ['required', 'string', Rule::in([Subscription::TYPE_TRIAL, Subscription::TYPE_PAID])],
        ];
    }

    public function messages(): array
    {
        return [
            'plan_id.required' => 'Please select a plan',
            'plan_id.exists' => 'Invalid plan selected',
            'type.required' => 'Please select subscription type',
            'type.in' => 'Invalid subscription type',
        ];
    }

    public function validatedData(): array
    {
        return array_merge($this->validated(), [
            'user_id' => $this->user()->id,
            'status' => Subscription::STATUS_ACTIVE,
            'started_at' => now(),
        ]);
    }
}
