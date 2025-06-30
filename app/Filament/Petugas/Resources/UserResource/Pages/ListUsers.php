<?php

namespace App\Filament\Petugas\Resources\UserResource\Pages;

use App\Filament\Petugas\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'Semua' => ListRecords\Tab::make()
                ->query(fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'warga')))
                ->badge(User::role('warga')->count()),

            'Menunggu Verifikasi' => ListRecords\Tab::make()
                ->query(function (Builder $query) {
                    return $query->whereHas('roles', fn ($q) => $q->where('name', 'warga'))
                                ->whereNull('verified_at')
                                ->whereNotNull('nik')
                                ->whereNotNull('nomor_kk')
                                ->whereNotNull('alamat')
                                ->whereNotNull('foto_ktp')
                                ->whereNotNull('foto_kk')
                                ->whereNotNull('foto_tanda_tangan')
                                ->whereNotNull('foto_selfie_ktp');
                })
                ->badge(
                    User::role('warga')
                        ->whereNull('verified_at')
                        ->whereNotNull('nik')
                        ->whereNotNull('nomor_kk')
                        ->whereNotNull('alamat')
                        ->whereNotNull('foto_ktp')
                        ->whereNotNull('foto_kk')
                        ->whereNotNull('foto_tanda_tangan')
                        ->whereNotNull('foto_selfie_ktp')
                        ->count()
                )
                ->badgeColor('warning'),
                
            'Belum Lengkap' => ListRecords\Tab::make()
                ->query(function (Builder $query) {
                    return $query->whereHas('roles', fn ($q) => $q->where('name', 'warga'))
                        ->where(function (Builder $q) {
                            $q->whereNull('nik')
                              ->orWhereNull('nomor_kk')
                              ->orWhereNull('alamat')
                              ->orWhereNull('foto_ktp')
                              ->orWhereNull('foto_kk')
                              ->orWhereNull('foto_tanda_tangan')
                              ->orWhereNull('foto_selfie_ktp');
                        });
                })
                ->badge(
                     User::role('warga')
                        ->where(function (Builder $q) {
                             $q->whereNull('nik')
                              ->orWhereNull('nomor_kk')
                              ->orWhereNull('alamat')
                              ->orWhereNull('foto_ktp')
                              ->orWhereNull('foto_kk')
                              ->orWhereNull('foto_tanda_tangan')
                              ->orWhereNull('foto_selfie_ktp');
                        })->count()
                )
                ->badgeColor('danger'),

            'Sudah Diverifikasi' => ListRecords\Tab::make()
                ->query(fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'warga'))->whereNotNull('verified_at'))
                ->badge(User::role('warga')->whereNotNull('verified_at')->count())
                ->badgeColor('success'),
        ];
    }
}