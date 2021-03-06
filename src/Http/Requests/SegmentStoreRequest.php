<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SegmentStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique('segments')
                    ->where('workspace_id', auth()->user()->currentWorkspace()->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => __('The segment name must be unique.'),
        ];
    }
}
