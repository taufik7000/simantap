@php
    $record = $getRecord();
    $status = 'Unknown';
    $colorClasses = 'bg-gray-100 text-gray-800';
    $icon = 'heroicon-s-question-mark-circle';
    $iconColor = 'text-gray-500';

    if ($record->verified_at) {
        $status = 'Terverifikasi';
        $colorClasses = 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300';
        $icon = 'heroicon-s-check-circle';
        $iconColor = 'text-green-500';
    } else {
        $requiredFields = ['nik', 'nomor_kk', 'alamat', 'foto_ktp', 'foto_kk', 'foto_tanda_tangan', 'foto_selfie_ktp'];
        $isComplete = true;
        foreach ($requiredFields as $field) {
            if (empty($record->{$field})) {
                $isComplete = false;
                break;
            }
        }
        
        if ($isComplete) {
            $status = 'Lengkap';
            $colorClasses = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300';
            $icon = 'heroicon-s-clock';
            $iconColor = 'text-yellow-500'; 
        } else {
            $status = 'Tidak Lengkap';
            $colorClasses = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300';
            $icon = 'heroicon-s-x-circle';
            $iconColor = 'text-red-500';
        }
    }
@endphp

<div @class([
    'inline-flex items-center justify-center gap-x-1.5 rounded-md px-2.5 py-1 text-xs font-medium',
    $colorClasses,
])>
    {{-- Tambahkan fill-current dan stroke-current --}}
    <x-icon :name="$icon" @class([
        'h-4 w-4',
        $iconColor,
        'fill-current', // Untuk ikon berbasis fill
        'stroke-current' // Untuk ikon berbasis stroke (opsional)
    ]) />
    <span>
        {{ $status }}
    </span>
</div>