<?php

namespace App\Services;

class FormValidationService
{
    public static function validateDynamicForm($jenisPermohonan, $request)
    {
        $rules = [];
        $messages = [];

        // Validasi form fields
        if (isset($jenisPermohonan['form_fields'])) {
            foreach ($jenisPermohonan['form_fields'] as $field) {
                $fieldRules = [];

                if ($field['is_required']) {
                    $fieldRules[] = 'required';
                }

                // Add type-specific validation
                switch ($field['field_type']) {
                    case 'email':
                        $fieldRules[] = 'email';
                        break;
                    case 'number':
                        $fieldRules[] = 'numeric';
                        break;
                    case 'date':
                        $fieldRules[] = 'date';
                        break;
                }

                // Add custom validation rules
                if (isset($field['validation_rules']) && is_array($field['validation_rules'])) {
                    $fieldRules = array_merge($fieldRules, $field['validation_rules']);
                }

                $rules["data_pemohon.{$field['field_name']}"] = $fieldRules;
                $messages["data_pemohon.{$field['field_name']}.required"] = "{$field['field_label']} wajib diisi";
            }
        }

        // Validasi file requirements
        if (isset($jenisPermohonan['file_requirements'])) {
            foreach ($jenisPermohonan['file_requirements'] as $fileReq) {
                $fileRules = [];

                if ($fileReq['is_required']) {
                    $fileRules[] = 'required';
                }

                $fileRules[] = 'file';
                if (!empty($fileReq['max_size'])) {
                     $fileRules[] = 'max:' . ($fileReq['max_size'] * 1024); // Convert MB to KB
                }

                // Add file type validation
                if (!empty($fileReq['file_type'])) {
                    switch ($fileReq['file_type']) {
                        case 'image':
                            $fileRules[] = 'mimes:jpg,jpeg,png';
                            break;
                        case 'pdf':
                            $fileRules[] = 'mimes:pdf';
                            break;
                        case 'document':
                            $fileRules[] = 'mimes:pdf,doc,docx';
                            break;
                    }
                }

                $rules["berkas_pemohon.{$fileReq['file_key']}"] = $fileRules;
                $messages["berkas_pemohon.{$fileReq['file_key']}.required"] = "{$fileReq['file_name']} wajib diupload";
            }
        }

        return [$rules, $messages];
    }
}