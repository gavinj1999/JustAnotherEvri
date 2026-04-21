<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvriScrapeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Normalize missing round to empty string so the unique(date,round) constraint works
        $this->merge([
            'round' => $this->input('round', ''),
        ]);
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d'],
            'round' => ['string', 'max:50'],
            'earnings' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'parcel_count' => ['nullable', 'integer', 'min:0'],
            'execution_time' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:completed,failed,timeout'],
            'raw_output' => ['nullable', 'string', 'max:65535'],
            'execution_id' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Status must be one of: completed, failed, timeout.',
            'date.date_format' => 'Date must be in YYYY-MM-DD format.',
        ];
    }
}
