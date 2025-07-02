<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WebsiteContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_key',
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'featured_image',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'content' => 'array',
        'is_published' => 'boolean',
    ];

    /**
     * Boot method untuk auto-generate slug
     */
    protected static function booted(): void
    {
        static::creating(function (WebsiteContent $content) {
            if (empty($content->slug)) {
                $content->slug = Str::slug($content->title);
            }
        });

        static::updating(function (WebsiteContent $content) {
            if ($content->isDirty('title') && empty($content->slug)) {
                $content->slug = Str::slug($content->title);
            }
        });
    }

    /**
     * Scope untuk content yang published
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope untuk ordering
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    /**
     * Get content by page key
     */
    public static function getByPageKey(string $pageKey): ?self
    {
        return static::where('page_key', $pageKey)->published()->first();
    }

    /**
     * Get content value by key
     */
    public function getContentValue(string $key, $default = null)
    {
        return data_get($this->content, $key, $default);
    }

    /**
     * Set content value by key
     */
    public function setContentValue(string $key, $value): void
    {
        $content = $this->content ?? [];
        data_set($content, $key, $value);
        $this->content = $content;
    }

    /**
     * Get page types for dropdown
     */
    public static function getPageTypes(): array
    {
        return [
            'homepage' => 'Halaman Beranda',
            'about' => 'Tentang Kami',
            'contact' => 'Kontak',
            'services' => 'Layanan',
            'news' => 'Berita',
            'faq' => 'FAQ',
            'privacy' => 'Kebijakan Privasi',
            'terms' => 'Syarat & Ketentuan',
        ];
    }

    /**
     * Get route key name for URL binding
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}