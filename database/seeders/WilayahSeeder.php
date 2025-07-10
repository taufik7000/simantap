<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use Illuminate\Support\Facades\DB;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Menggunakan firstOrCreate untuk menghindari duplikasi saat seeding ulang
        // 1. Buat atau temukan Provinsi Sumatera Utara
        $province = Province::firstOrCreate(
            ['id' => '12'],
            ['name' => 'SUMATERA UTARA']
        );

        // 2. Buat atau temukan Kabupaten Simalungun
        $regency = Regency::firstOrCreate(
            ['id' => '1208'],
            [
                'province_id' => $province->id,
                'name' => 'KABUPATEN SIMALUNGUN'
            ]
        );

        // 3. Data Kecamatan dan Desa/Kelurahan untuk Simalungun
        $data = [
            '120801' => ['name' => 'Bandar', 'villages' => ['Bah Lias', 'Bandar Jawa', 'Bandar Pulo', 'Bandar Rakyat', 'Landbouw', 'Marihat Bandar', 'Nagori Bandar', 'Pematang Kerasaan', 'Pematang Kerasaan Rejo', 'Perlanaan', 'Sidotani', 'Sugarang Bayu', 'Timbaan', 'Perdagangan I', 'Perdagangan II', 'Perdagangan III']],
            '120802' => ['name' => 'Bandar Huluan', 'villages' => ['Bah Gunung', 'Bandar Betsy I', 'Bandar Betsy II', 'Bandar Tongah', 'Dolok Parmonangan', 'Laras', 'Naga Jaya I', 'Naga Jaya II', 'Naga Soppa', 'Tanjung Hataran']],
            '120803' => ['name' => 'Bandar Masilam', 'villages' => ['Bandar Gunung', 'Bandar Masilam', 'Bandar Masilam II', 'Bandar Rejo', 'Bandar Silou', 'Bandar Tinggi', 'Gunung Serawan', 'Lias Baru', 'Panombean Baru', 'Partimbalan']],
            '120804' => ['name' => 'Bosar Maligas', 'villages' => ['Adil Makmur', 'Boluk', 'Bosar Maligas', 'Dusun Pengkolan', 'Gunung Bayu', 'Marihat Butar', 'Marihat Tanjung', 'Mayang', 'Mekar Rejo', 'Nanggar Bayu', 'Parbutaran', 'Sei Mangkei', 'Sei Torop', 'Sidomulyo', 'Talun Saragih', 'Teladan', 'Tempel Jaya']],
            '120805' => ['name' => 'Dolog Masagal', 'villages' => ['Bah Bolon', 'Bangun Pane', 'Bintang Mariah', 'Dolog Huluan', 'Pamatang Sinaman', 'Parjalangan', 'Partuahan', 'Raya Huluan', 'Raya Usang', 'Sinaman Labah']],
            '120806' => ['name' => 'Dolok Batu Nanggar', 'villages' => ['Aman Sari', 'Bah Tobu', 'Bahung Huluan', 'Bahung Kahean', 'Bandar Selamat', 'Dolok Ilir I', 'Dolok Ilir II', 'Dolok Kataran', 'Dolok Mainu', 'Dolok Merangir I', 'Dolok Merangir II', 'Dolok Tenera', 'Kahean', 'Padang Mainu', 'Serbelawan', 'Silenduk']],
            '120807' => ['name' => 'Dolok Panribuan', 'villages' => ['Bandar Dolok', 'Dolok Parmonangan', 'Dolok Tomuan', 'Gunung Mariah', 'Lumban Gorat', 'Marihat Dolok', 'Marihat Marsada', 'Marihat Pondok', 'Marihat Raja', 'Negeri Dolok', 'Palia Naopat', 'Pondok Buluh', 'Siatasan', 'Tiga Dolok', 'Ujung Bondar']],
            '120808' => ['name' => 'Dolok Pardamean', 'villages' => ['Buttu Bayu Panei Raja', 'Dolok Saribu', 'Nagori Bayu', 'Parik Sabungan', 'Sibuntuon', 'Sihemun Baru', 'Sihemun Jaya', 'Sipoldas', 'Sitalasari', 'Sirube-rube Gunung Purba', 'Tanjung Saribu']],
            '120809' => ['name' => 'Dolok Silau', 'villages' => ['Bawang', 'Bosi Sinombah', 'Cingkes', 'Dolok Mariah', 'Huta Saing', 'Mariah Dolok', 'Marubun Lokkung', 'Paribuan', 'Perasmian', 'Saran Padang', 'Silau Marawan', 'Tanjung Purba', 'Togur', 'Ujung Bawang']],
            '120810' => ['name' => 'Girsang Sipangan Bolon', 'villages' => ['Girsang', 'Parapat', 'Sibaganding', 'Sipangan Bolon', 'Sipangan Bolon Mekar', 'Tiga Raja']],
            '120811' => ['name' => 'Gunung Malela', 'villages' => ['Bandar Siantar', 'Bangun', 'Bukit Maraja', 'Dolok Malela', 'Dolok Sinumbah', 'Lingga', 'Marihat Bukit', 'Nagori Malela', 'Pematang Asilum', 'Pematang Bandar', 'Pematang Sah Kuda', 'Sah Kuda Bayu', 'Senio', 'Serapuh', 'Silau Malela', 'Silulu']],
            '120812' => ['name' => 'Gunung Maligas', 'villages' => ['Bandar Malela', 'Gunung Maligas', 'Huta Dipar', 'Karang Anyer', 'Karang Rejo', 'Karang Sari', 'Rabuhit', 'Silau Bayu', 'Silulu']],
            '120813' => ['name' => 'Haranggaol Horison', 'villages' => ['Haranggaol', 'Purba Horison', 'Purba Pasir', 'Purba Tongah', 'Rihit']],
            '120814' => ['name' => 'Hatonduhan', 'villages' => ['Buntu Bayu', 'Buntu Turunan', 'Hatonduhan', 'Jawa Tongah', 'Parhundalian Jawadipar', 'Rambung Merah', 'Sarang Padang', 'Tangga Batu', 'Tonduhan']],
            '120815' => ['name' => 'Huta Bayu Raja', 'villages' => ['Bahal Batu', 'Bosar Bayu', 'Dolok Sinumbah', 'Huta Bayu', 'Jawa Baru', 'Maligas Bayu', 'Manrayap', 'Mancuk', 'Mariah Hombang', 'Pokan Baru', 'Pulo Bayu', 'Raja Maligas', 'Raja Maligas I', 'Silakkidir', 'Talam', 'Tanjung Muda']],
            '120816' => ['name' => 'Jawa Maraja Bah Jambi', 'villages' => ['Bah Jambi', 'Bah Tangan', 'Jawa Maraja', 'Meku', 'Moho', 'Pardomuan', 'Pokan Baru', 'Tanjung Maraja']],
            '120817' => ['name' => 'Jorlang Hataran', 'villages' => ['Bah Sampuran', 'Dipar Hataran', 'Dolok Marlawan', 'Dolok Parriasan', 'Jorlang Hataran', 'Kasindir', 'Pagar Pinang', 'Panombean', 'Pinang Ratus', 'Pining', 'Sibunga-bunga', 'Tiga Balata', 'Tiga Raja']],
            '120818' => ['name' => 'Panei', 'villages' => ['Bah Bolon', 'Bah Liran', 'Bangun Das Mariah', 'Bangun Rakyat', 'Bangun Sitolu Bah', 'Janggir Leto', 'Marjandi', 'Mekar Sari', 'Nauli Baru', 'Panei Tongah', 'Rawang Pardomuan Nauli', 'Siborna', 'Sigodang', 'Sigodang Baru', 'Simbang', 'Sipoldas', 'Sitalas']],
            '120819' => ['name' => 'Panombeian Panei', 'villages' => ['Banuh Raya', 'Marjandi', 'Marjandi Embong', 'Panombeian', 'Pematang Panei', 'Pematang Panombeian', 'Simbolon Tengkoh', 'Simpang Panei', 'Sinah Kasih', 'Sipoldas', 'Talun Kondot']],
            '120820' => ['name' => 'Pamatang Bandar', 'villages' => ['Bandar Manis', 'Gajing', 'Jawa-jawa', 'Kandangan', 'Kerasaan I', 'Kerasaan II', 'Mariah Bandar', 'Pardomuan', 'Pematang Bandar', 'Purba Ganda', 'Purwosari', 'Talun Madear', 'Talun Pojok', 'Wonorejo']],
            '120821' => ['name' => 'Pamatang Sidamanik', 'villages' => ['Bandar Manik', 'Gorak', 'Jorlang Huluan', 'Pematang Sidamanik', 'Pematang Tambun Raya', 'Sait Buttu Saribu', 'Sarinembah', 'Sihilon', 'Simantin', 'Sipolha Horison']],
            '120822' => ['name' => 'Pamatang Silima Huta', 'villages' => ['Bandar Saribu', 'Mardingin', 'Naga Bosar', 'Naga Saribu', 'Saribu Jandi', 'Silima Huta', 'Sinar Naga Mariah', 'Ujung Saribu', 'Ujung Mariah', 'Siboras']],
            '120823' => ['name' => 'Purba', 'villages' => ['Bandar Sauhur', 'Bunga Sampang', 'Hinalang', 'Huta Raja', 'Kinalang', 'Pematang Purba', 'Purba Dolok', 'Purba Sipinggan', 'Purba Tongah', 'Seribu Jandi', 'Tano Tinggir', 'Tiga Runggu', 'Urung Pane', 'Urung Purba']],
            '120824' => ['name' => 'Raya', 'villages' => ['Bahapal', 'Bah Bolon', 'Bah Merek', 'Baringin Raya', 'Bongguran Kariahan', 'Dalig Raya', 'Dolok Mariah', 'Lantosan Rogas', 'Pematang Raya', 'Raya', 'Raya Bayu', 'Raya Bosi', 'Sihubu Raya', 'Silou Buttu', 'Silou Huluan', 'Simbou Baru', 'Sondi Raya']],
            '120825' => ['name' => 'Raya Kahean', 'villages' => ['Ambarisan', 'Bah Bulian', 'Bah Tonang', 'Bangun Raya', 'Banjaran', 'Banu Raya', 'Durian Buttu', 'Gunung Datas', 'Marubun Siboras', 'Padang Sambus', 'Panduman', 'Puli Buah', 'Raya Kahean', 'Sindar Raya']],
            '120826' => ['name' => 'Siantar', 'villages' => ['Dolok Hataran', 'Dolok Marlawan', 'Karang Bangun', 'Laras Dua', 'Lestari Indah', 'Marihat Baris', 'Nusa Harapan', 'Pantoan Maju', 'Pematang Silampuyang', 'Rambung Merah', 'Sejahtera', 'Siahap', 'Siantar Estate', 'Silampuyang', 'Silau Manik', 'Sitalasari', 'Suka Maju']],
            '120827' => ['name' => 'Sidamanik', 'villages' => ['Ambarisan', 'Bah Biak', 'Bah Butong I', 'Bah Butong II', 'Bahal Gajah', 'Birong Ulu Manriah', 'Bukit Rejo', 'Kebun Sayur', 'Mekar Sari', 'Pematang Sidamanik', 'Sari Matondang', 'Sidamanik', 'Sido Rame', 'Tiga Bolon', 'Tiga Juhar']],
            '120828' => ['name' => 'Silimakuta', 'villages' => ['Bangun Mariah', 'Purba Sinombah', 'Purbatua', 'Purbatua Etek', 'Purbatua Baru', 'Saribu Dolok', 'Sinar Baru']],
            '120829' => ['name' => 'Silou Kahean', 'villages' => ['Bandar Maruhur', 'Buttu Bayu', 'Damakitang', 'Dolok Marawa', 'Dolok Saribu', 'Mariah Buttu', 'Nagori Dolog', 'Nagori Tani', 'Pardomuan', 'Pardomuan Tongah', 'Pematang Silou Kahean', 'Silou Dunia', 'Silou Paribuan', 'Simanabun', 'Sinasih', 'Tani']],
            '120830' => ['name' => 'Tanah Jawa', 'villages' => ['Bah Jambi', 'Bah Kisat', 'Baja Dolok', 'Baliju', 'Balimbingan', 'Bayu Bagasan', 'Bosar Galugur', 'Maligas Tongah', 'Mancuk', 'Marubun Bayu', 'Marubun Jaya', 'Mekar Mulia', 'Muara Mulia', 'Panembean', 'Pardamean', 'Parbalokan', 'Pematang Tanah Jawa', 'Tanah Jawa', 'Tanjung Pasir', 'Totap Majawa']],
            '120831' => ['name' => 'Tapian Dolok', 'villages' => ['Batu Silangit', 'Dolok Kahean', 'Dolok Maraja', 'Dolok Simbolon', 'Dolok Ulu', 'Gunung Monako', 'Naga Dolok', 'Nagur Usang', 'Negeri Bayu Muslimin', 'Pamatang Dolok Kahean', 'Pematang Gunung', 'Purba Sari', 'Sinaksak']],
            '120832' => ['name' => 'Ujung Padang', 'villages' => ['Aek Gerger', 'Bangun Das', 'Bangun Sordang', 'Banjar Hulu', 'Desa Karyawan', 'Dusun Ulu', 'Huta Koje', 'Huta Parik', 'Kampung Lalang', 'Pagar Batu', 'Pagar Bosi', 'Pulo Pitu Marihat', 'Riah Naposo', 'Sayur Matinggi', 'Sei Merbau', 'Siringan-ringan', 'Sordang Bolon', 'Sordang Baru', 'Tanjung Rapuan', 'Taratak', 'Ujung Padang']],
        ];

        foreach ($data as $districtId => $districtData) {
            $kecamatan = District::create([
                'id' => $districtId,
                'regency_id' => $regency->id,
                'name' => $districtData['name'],
            ]);

            if (!empty($districtData['villages'])) {
                foreach ($districtData['villages'] as $villageName) {
                    $villageId = $districtId . str_pad((Village::where('district_id', $districtId)->count() + 1), 4, '0', STR_PAD_LEFT);
                    Village::create([
                        'id' => $villageId,
                        'district_id' => $kecamatan->id,
                        'name' => $villageName
                    ]);
                }
            }
        }
    }
}