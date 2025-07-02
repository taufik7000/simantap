<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('website_contents', function (Blueprint $table) {
            $table->id();
            $table->string('page_key')->unique(); // 'homepage', 'about', 'contact', etc.
            $table->string('title');
            $table->string('slug')->unique();
            $table->json('content'); // Menyimpan berbagai field content dalam JSON
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('featured_image')->nullable();
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert default content
        DB::table('website_contents')->insert([
            [
                'page_key' => 'homepage',
                'title' => 'Beranda',
                'slug' => 'beranda',
                'content' => json_encode([
                    'hero_title' => 'Administrasi Terpadu Simalungun',
                    'hero_subtitle' => 'Layanan administrasi digital yang mudah, cepat, dan terpercaya untuk masyarakat Kabupaten Simalungun.',
                    'hero_cta_text' => 'Daftar Sekarang',
                    'hero_secondary_cta' => 'Lihat Layanan',
                    'features' => [
                        [
                            'title' => 'Proses Cepat',
                            'description' => 'Layanan administrasi yang dapat diproses dalam hitungan menit, bukan hari.',
                            'icon' => 'heroicon-o-bolt'
                        ],
                        [
                            'title' => 'Terpercaya',
                            'description' => 'Sistem keamanan tingkat tinggi dengan enkripsi data end-to-end.',
                            'icon' => 'heroicon-o-shield-check'
                        ],
                        [
                            'title' => 'Mudah Digunakan',
                            'description' => 'Interface yang user-friendly, dapat diakses dari berbagai perangkat.',
                            'icon' => 'heroicon-o-device-phone-mobile'
                        ]
                    ],
                    'statistics' => [
                        ['label' => 'Pengguna Aktif', 'value' => '15,000+'],
                        ['label' => 'Jenis Layanan', 'value' => '25+'],
                        ['label' => 'Tingkat Kepuasan', 'value' => '98%'],
                        ['label' => 'Akses Online', 'value' => '24/7']
                    ]
                ]),
                'meta_title' => 'SIMANTAP - Simalungun Administrasi Terpadu',
                'meta_description' => 'Platform digital untuk layanan administrasi Kabupaten Simalungun yang mudah, cepat, dan terpercaya.',
                'is_published' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page_key' => 'about',
                'title' => 'Tentang Kami',
                'slug' => 'tentang-kami',
                'content' => json_encode([
                    'intro_title' => 'Tentang SIMANTAP',
                    'intro_description' => 'SIMANTAP (Simalungun Administrasi Terpadu) adalah platform digital yang dikembangkan untuk mempermudah masyarakat Kabupaten Simalungun dalam mengakses berbagai layanan administrasi pemerintahan.',
                    'vision' => 'Menjadi platform administrasi digital terdepan yang memberikan pelayanan prima kepada masyarakat Simalungun.',
                    'mission' => [
                        'Menyediakan layanan administrasi yang cepat, akurat, dan mudah diakses',
                        'Meningkatkan transparansi dan akuntabilitas pelayanan publik',
                        'Mendorong transformasi digital di lingkungan pemerintahan',
                        'Memberikan kemudahan bagi masyarakat dalam mengurus dokumen administrasi'
                    ],
                    'values' => [
                        ['title' => 'Integritas', 'description' => 'Menjunjung tinggi kejujuran dan transparansi dalam setiap pelayanan'],
                        ['title' => 'Inovasi', 'description' => 'Terus berinovasi untuk memberikan solusi terbaik bagi masyarakat'],
                        ['title' => 'Pelayanan Prima', 'description' => 'Mengutamakan kepuasan dan kemudahan masyarakat dalam setiap layanan']
                    ]
                ]),
                'meta_title' => 'Tentang SIMANTAP - Simalungun Administrasi Terpadu',
                'meta_description' => 'Mengenal lebih dekat SIMANTAP, platform administrasi digital Kabupaten Simalungun yang mengutamakan pelayanan prima.',
                'is_published' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page_key' => 'contact',
                'title' => 'Kontak',
                'slug' => 'kontak',
                'content' => json_encode([
                    'intro_title' => 'Hubungi Kami',
                    'intro_description' => 'Tim kami siap membantu Anda. Jangan ragu untuk menghubungi kami jika membutuhkan bantuan atau memiliki pertanyaan.',
                    'office_address' => 'Jl. Sisingamangaraja No. 1, Pematang Siantar, Kabupaten Simalungun, Sumatera Utara 21118',
                    'phone' => '(0622) 123-4567',
                    'email' => 'info@simantap.simalungunkab.go.id',
                    'whatsapp' => '+62 812-3456-7890',
                    'office_hours' => [
                        'Senin - Jumat: 08:00 - 16:00 WIB',
                        'Sabtu: 08:00 - 12:00 WIB',
                        'Minggu: Tutup'
                    ],
                    'social_media' => [
                        ['platform' => 'Facebook', 'url' => 'https://facebook.com/simantap'],
                        ['platform' => 'Instagram', 'url' => 'https://instagram.com/simantap'],
                        ['platform' => 'Twitter', 'url' => 'https://twitter.com/simantap']
                    ]
                ]),
                'meta_title' => 'Kontak - SIMANTAP Simalungun',
                'meta_description' => 'Hubungi tim SIMANTAP untuk bantuan dan informasi lebih lanjut mengenai layanan administrasi digital Simalungun.',
                'is_published' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_contents');
    }
};