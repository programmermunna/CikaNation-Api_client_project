<?php

namespace App\Http\Requests\Api\UserIP;

use App\Http\Requests\BaseFormRequest;

class UserIpRequest extends BaseFormRequest
{
    protected array $routeRequest = [
        'api/v1/user-ip|post' => 'storeMethodRule',
        'api/v1/user-ip|put' => 'updateMethodRule',
        'api/v1/user-ip|patch' => 'updateMethodRule',
        'api/v1/user-ips|put' => 'multipleUpdateMethodRule',
    ];

    public function storeMethodRule(): void
    {
        $this->rules = [
            'number1' => 'required|min:1|max:255|numeric',
            'number2' => 'required|min:0|max:255|numeric',
            'number3' => 'required|min:0|max:255|numeric',
            'number4' => 'required|min:0|max:255|numeric',
            'description' => 'required',
        ];
    }

    public function updateMethodRule(): void
    {
        $this->rules = [
            'number1' => 'required|min:1|max:255|numeric',
            'number2' => 'required|min:0|max:255|numeric',
            'number3' => 'required|min:0|max:255|numeric',
            'number4' => 'required|min:0|max:255|numeric',
            'whitelisted' => 'required|max:255|numeric',
            'description' => 'required|max:255',
        ];
    }

    public function multipleUpdateMethodRule(): void
    {
        $this->rules = [
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.item' => 'required|array',
            'items.*.item.number1' => 'required|integer',
            'items.*.item.number2' => 'required|integer',
            'items.*.item.number3' => 'required|integer',
            'items.*.item.number4' => 'required|integer',
            'items.*.item.whitelisted' => 'required|integer',
            'items.*.item.description' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'number1.required' => 'The number 1 IP is required',
            'number2.required' => 'The number 2 IP is required',
            'number3.required' => 'The number 3 IP is required',
            'number4.required' => 'The number 4 IP is required',
            '*.min' => 'The number must be at least 0.',
            '*.max' => 'The number must not be greater than 255',

            'whitelisted.required' => 'The number whitelisted is required',
            'description.required' => 'The number whitelisted is required',

            'items.required' => 'The items field is required.',
            'items.array' => 'The items must be an array.',
            'items.*.id.required' => 'The ID is required for item :itemIndex.',
            'items.*.id.integer' => 'The ID for item :itemIndex must be an integer.',
            'items.*.item.required' => 'The item for item :itemIndex is required.',
            'items.*.item.array' => 'The item for item :itemIndex must be an array.',
            'items.*.item.number1.required' => 'The number1 field for item :itemIndex is required.',
            'items.*.item.number1.integer' => 'The number1 field for item :itemIndex must be an integer.',
            'items.*.item.number2.required' => 'The number2 field for item :itemIndex is required.',
            'items.*.item.number2.integer' => 'The number2 field for item :itemIndex must be an integer.',
            'items.*.item.number3.required' => 'The number3 field for item :itemIndex is required.',
            'items.*.item.number3.integer' => 'The number3 field for item :itemIndex must be an integer.',
            'items.*.item.number4.required' => 'The number4 field for item :itemIndex is required.',
            'items.*.item.number4.integer' => 'The number4 field for item :itemIndex must be an integer.',
            'items.*.item.whitelisted.required' => 'The whitelisted field is required.',
            'items.*.item.whitelisted.integer' => 'The whitelisted field must be an integer.',
            'items.*.item.description.required' => 'The description field for item :itemIndex is required.',
            'items.*.item.description.string' => 'The description field for item :itemIndex must be a string.',
            'items.*.item.description.max' => 'The description field for item :itemIndex may not be greater than :max characters.',
        ];
    }

    /**
     * todo set custom names for validation with wildcats
     *  e.g
     *  $validator->setAttributeNames([
     *      'items.*.id' => 'ID',
     *      'items.*.item.number1' => 'Number 1',
     *      'items.*.item.number2' => 'Number 2',
     *      'items.*.item.number3' => 'Number 3',
     *      'items.*.item.number4' => 'Number 4',
     *      'items.*.item.whitelisted' => 'Whitelisted',
     *      'items.*.item.description' => 'Description',
     *  ]);
     */
}
