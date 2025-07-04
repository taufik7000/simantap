@extends('filament-panels::components.layout')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-200">Buat Permohonan: {{ $layanan->name }}</h1>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Pilih Jenis Permohonan</h2>

        @foreach($jenisPermohonanData as $jenis)
        <div class="border dark:border-gray-700 rounded-lg p-4 mb-4 cursor-pointer transition-all hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-gray-700/50 jenis-permohonan-option"
             data-jenis-id="{{ $jenis['id'] }}">
            <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ $jenis['nama'] }}</h3>
            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 prose dark:prose-invert max-w-none">{!! $jenis['deskripsi'] !!}</div>

            <div class="flex items-center gap-2 mt-3">
                 @if(!empty($jenis['form_fields']))
                    <span class="text-xs bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300 px-2 py-1 rounded-full">
                        {{ count($jenis['form_fields']) }} isian data
                    </span>
                @endif

                @if(!empty($jenis['file_requirements']))
                    <span class="text-xs bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300 px-2 py-1 rounded-full">
                        {{ count($jenis['file_requirements']) }} berkas diperlukan
                    </span>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div id="dynamic-form-container" class="hidden">
        {{-- Form ini akan submit ke method `submitPermohonan` di backend --}}
        <form action="{{ route('filament.warga.pages.create-permohonan', ['layanan_id' => $layanan->id]) }}" method="POST" enctype="multipart/form-data" id="permohonan-form">
            @csrf
            {{-- Input tersembunyi untuk menyimpan ID jenis permohonan yang dipilih --}}
            <input type="hidden" name="selected_jenis_id" id="selected_jenis_id">

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6" id="data-form-section">
                <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Isi Data Permohonan</h2>
                <div id="dynamic-fields" class="space-y-6"></div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6" id="file-upload-section">
                <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Upload Berkas</h2>
                <div id="dynamic-file-uploads" class="space-y-6"></div>
            </div>

            <div class="flex justify-end">
                 <button type="submit" class="fi-btn fi-btn-size-md fi-btn-color-primary">
                    Ajukan Permohonan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const jenisPermohonanData = @json($jenisPermohonanData);
    let selectedJenisId = null;

    document.querySelectorAll('.jenis-permohonan-option').forEach(el => {
        el.addEventListener('click', function() {
            selectJenisPermohonan(this.dataset.jenisId);
        });
    });

    function selectJenisPermohonan(jenisId) {
        selectedJenisId = jenisId;
        const selectedJenis = jenisPermohonanData.find(j => j.id == jenisId);

        // Update UI
        document.querySelectorAll('.jenis-permohonan-option').forEach(el => {
            el.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-gray-700/50');
        });
        document.querySelector(`[data-jenis-id="${jenisId}"]`).classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-gray-700/50');

        // Set value untuk input hidden
        document.getElementById('selected_jenis_id').value = jenisId;

        // Generate form dinamis
        generateDynamicForm(selectedJenis);

        // Tampilkan form container
        document.getElementById('dynamic-form-container').classList.remove('hidden');
    }

    function generateDynamicForm(jenis) {
        const dynamicFieldsContainer = document.getElementById('dynamic-fields');
        const dynamicFilesContainer = document.getElementById('dynamic-file-uploads');

        dynamicFieldsContainer.innerHTML = '';
        dynamicFilesContainer.innerHTML = '';

        // Generate form fields jika ada
        const hasFormFields = jenis.form_fields && jenis.form_fields.length > 0;
        document.getElementById('data-form-section').style.display = hasFormFields ? 'block' : 'none';
        if (hasFormFields) {
            jenis.form_fields
                .sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0))
                .forEach(field => {
                    const fieldHtml = generateFieldHtml(field);
                    dynamicFieldsContainer.insertAdjacentHTML('beforeend', fieldHtml);
                });
        }

        // Generate file uploads jika ada
        const hasFileReqs = jenis.file_requirements && jenis.file_requirements.length > 0;
        document.getElementById('file-upload-section').style.display = hasFileReqs ? 'block' : 'none';
        if (hasFileReqs) {
            jenis.file_requirements.forEach(fileReq => {
                const fileHtml = generateFileUploadHtml(fileReq);
                dynamicFilesContainer.insertAdjacentHTML('beforeend', fileHtml);
            });
        }
    }

    function generateFieldHtml(field) {
        const required = field.is_required ? 'required' : '';
        const helpText = field.help_text ? `<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">${field.help_text}</p>` : '';
        let inputHtml = '';
        const commonClasses = 'block w-full transition duration-75 rounded-lg shadow-sm focus:ring-1 focus:ring-inset disabled:opacity-70 dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600 focus:border-primary-500 focus:ring-primary-500 dark:focus:border-primary-500 dark:focus:ring-primary-500';

        switch (field.field_type) {
            case 'text':
                inputHtml = `<input type="text" name="data_pemohon[${field.field_name}]" class="${commonClasses}" ${required}>`;
                break;
            case 'textarea':
                inputHtml = `<textarea name="data_pemohon[${field.field_name}]" rows="4" class="${commonClasses}" ${required}></textarea>`;
                break;
            case 'select':
                let options = '<option value="">Pilih...</option>';
                if (field.field_options) {
                    field.field_options.forEach(opt => {
                        options += `<option value="${opt.value}">${opt.label}</option>`;
                    });
                }
                inputHtml = `<select name="data_pemohon[${field.field_name}]" class="${commonClasses}" ${required}>${options}</select>`;
                break;
            case 'date':
                inputHtml = `<input type="date" name="data_pemohon[${field.field_name}]" class="${commonClasses}" ${required}>`;
                break;
            case 'number':
                inputHtml = `<input type="number" name="data_pemohon[${field.field_name}]" class="${commonClasses}" ${required}>`;
                break;
            case 'email':
                inputHtml = `<input type="email" name="data_pemohon[${field.field_name}]" class="${commonClasses}" ${required}>`;
                break;
            case 'checkbox':
                if (field.field_options) {
                    inputHtml = '<div class="space-y-2">';
                    field.field_options.forEach(opt => {
                        inputHtml += `
                            <label class="flex items-center gap-x-3">
                                <input type="checkbox" name="data_pemohon[${field.field_name}][]" value="${opt.value}" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600 dark:bg-gray-700 dark:border-gray-600">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">${opt.label}</span>
                            </label>`;
                    });
                    inputHtml += '</div>';
                }
                break;
            case 'radio':
                if (field.field_options) {
                    inputHtml = '<div class="space-y-2">';
                    field.field_options.forEach(opt => {
                        inputHtml += `
                            <label class="flex items-center gap-x-3">
                                <input type="radio" name="data_pemohon[${field.field_name}]" value="${opt.value}" class="h-4 w-4 border-gray-300 text-primary-600 focus:ring-primary-600 dark:bg-gray-700 dark:border-gray-600" ${required}>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">${opt.label}</span>
                            </label>`;
                    });
                    inputHtml += '</div>';
                }
                break;
        }

        return `
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                    ${field.field_label}
                    ${field.is_required ? '<span class="text-red-500">*</span>' : ''}
                </label>
                ${inputHtml}
                ${helpText}
            </div>
        `;
    }

    function generateFileUploadHtml(fileReq) {
        const required = fileReq.is_required ? 'required' : '';
        const accept = getAcceptAttribute(fileReq.file_type);
        const description = fileReq.file_description ? `<p class="text-sm text-gray-600 dark:text-gray-400 mb-3">${fileReq.file_description}</p>` : '';
        
        return `
            <div class="border dark:border-gray-700 rounded-lg p-4">
                <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                    ${fileReq.file_name}
                    ${fileReq.is_required ? '<span class="text-red-500">*</span>' : ''}
                </label>
                
                ${description}
                
                <input type="file" name="berkas_pemohon[${fileReq.file_key}]" 
                       accept="${accept}" ${required}
                       class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Tipe: ${accept}. Ukuran Maksimal: ${fileReq.max_size || 2}MB.
                </p>
            </div>
        `;
    }

    function getAcceptAttribute(fileType) {
        switch (fileType) {
            case 'image': return '.jpg,.jpeg,.png';
            case 'pdf': return '.pdf';
            case 'document': return '.pdf,.doc,.docx';
            default: return '*/*';
        }
    }
</script>
@endsection