<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TraderProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // On autorise, la vérification des rôles peut se faire dans le contrôleur si besoin
        return true;
    }

    public function rules(): array
    {
        return [
            'enseigne' => ['sometimes', 'string', 'max:255'],
            'adresse'  => ['sometimes', 'string', 'max:255'],
            'kbis' => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096', 
        ];
    }

    public function messages()
    {
        return [
            'enseigne.required' => "L'enseigne est obligatoire.",
            'adresse.required'  => "L'adresse est obligatoire.",
            'kbis.mimes'        => "Le document Kbis doit être un fichier PDF, JPG, JPEG ou PNG.",
            'kbis.max'          => "Le fichier Kbis ne doit pas dépasser 4 Mo.",
        ];
    }
}