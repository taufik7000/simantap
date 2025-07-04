<?php

namespace App\Filament\Warga\Resources\PermohonanResource\Pages;

use App\Filament\Warga\Resources\PermohonanResource;
use App\Models\Layanan;
use App\Models\Permohonan;
use App\Services\FormValidationService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreatePermohonan extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = PermohonanResource::class;

    // Tunjuk ke view custom kita
    protected static string $view = 'filament.warga.pages.create-permohonan';

    // Properti untuk dikirim ke view
    public Layanan $layanan;
    public array $jenisPermohonanData = [];

    // Properti untuk menampung data dari form
    public $selected_jenis_id;

    public function mount(): void
    {
        $layananId = request()->query('layanan_id');
        abort_if(!$layananId, 404);

        $this->layanan = Layanan::findOrFail($layananId);

        // Siapkan data untuk dikirim ke view Blade
        if ($this->layanan->description && is_array($this->layanan->description)) {
            foreach ($this->layanan->description as $index => $syarat) {
                $this->jenisPermohonanData[] = [
                    'id' => $index, // Gunakan index sebagai ID unik sementara
                    'nama' => $syarat['nama_syarat'],
                    'deskripsi' => $syarat['deskripsi_syarat'],
                    'formulir_master_id' => $syarat['formulir_master_id'] ?? null,
                    // BARU: Form fields untuk data collection
                    'form_fields' => $syarat['form_fields'] ?? [],
                    // BARU: File requirements
                    'file_requirements' => $syarat['file_requirements'] ?? [],
                ];
            }
        }
    }

    // Fungsi ini akan dipanggil dari frontend untuk memproses form
    public function submitPermohonan(Request $request)
    {
        // Ambil jenis permohonan yang dipilih berdasarkan ID
        $jenisPermohonanId = $request->input('selected_jenis_id');
        $jenisPermohonan = null;
        foreach ($this->jenisPermohonanData as $data) {
            if ($data['id'] == $jenisPermohonanId) {
                $jenisPermohonan = $data;
                break;
            }
        }

        abort_if(!$jenisPermohonan, 404, 'Jenis permohonan tidak valid.');

        // Validasi dinamis
        [$rules, $messages] = FormValidationService::validateDynamicForm($jenisPermohonan, $request);
        $validatedData = $request->validate($rules, $messages);

        // Process file uploads
        $berkasData = [];
        if ($request->hasFile('berkas_pemohon')) {
            // Cari detail file_requirements yang sesuai
            $fileReqsMap = collect($jenisPermohonan['file_requirements'] ?? [])->keyBy('file_key');

            foreach ($request->file('berkas_pemohon') as $key => $file) {
                if ($file) {
                    $path = $file->store('berkas-permohonan', 'private');
                    $fileReq = $fileReqsMap->get($key);
                    $berkasData[] = [
                        'file_key' => $key,
                        'nama_dokumen' => $fileReq['file_name'] ?? $key,
                        'path_dokumen' => $path,
                        'original_name' => $file->getClientOriginalName(),
                    ];
                }
            }
        }

        // Ambil data pemohon dari request
        $dataPemohonInput = $validatedData['data_pemohon'] ?? [];
        // Gabungkan dengan nama jenis permohonan
        $dataPemohonFinal = array_merge(
            $dataPemohonInput,
            ['jenis_permohonan' => $jenisPermohonan['nama']]
        );

        // Create permohonan
        $permohonan = Permohonan::create([
            'user_id' => auth()->id(),
            'layanan_id' => $this->layanan->id,
            'data_pemohon' => $dataPemohonFinal,
            'berkas_pemohon' => $berkasData,
            'status' => 'baru'
        ]);

        return redirect()->route('filament.warga.resources.permohonans.view', $permohonan->kode_permohonan)
            ->with('success', 'Permohonan berhasil diajukan!');
    }
}