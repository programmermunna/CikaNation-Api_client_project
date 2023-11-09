<?php

namespace App\Http\Requests\Api\Cashflow;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager as Image;


class StoreCashflowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'item_name'  => 'required|string|max:255',
            'item_price' => 'required|numeric',
            'image'      => 'required|mimes:png,jpg,jpeg|max:4096',
            'upload'     => 'nullable|string',
        ];
    }


    protected function prepareForValidation()
    {
        $imagePath = '';

        if ($this->hasFile('image')) {
            $folder     = config('filesystems.disks.do.folder');
            $image      = $this->file('image');
            $webpImage  = (new Image)->make($image->getRealPath())->resize(200, 200)->encode('webp',90);
            $image_name = md5(time()) . '.' . 'webp';
            $imagePath  = $folder.'/cashflow/uploads/' . $image_name;
            Storage::disk('do')->put($imagePath, $webpImage, 'public');
        }
        
        $this->merge([
            'upload' => $imagePath
        ]);
    }
}