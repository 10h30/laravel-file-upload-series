<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class Upload extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'original_filename',
    ];

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->width(100)
            ->height(100)
            ->nonQueued()
            ->performOnCollections('images'); // images là tên collection lưu ảnh
    }

    public function getUrlAttribute(): string
    {

        $mediaItem = $this->getFirstMedia('images');
        if ($mediaItem) {
            // Tạo URL tạm thời cho file gốc.
            return $mediaItem->getTemporaryUrl(now()->addMinutes(5));
        }
        return ''; // Trả về chuỗi rỗng hoặc một URL placeholder nếu không tìm thấy file
    }

    // Tạo URL tạm thời mới mỗi khi thuộc tính 'thumbnail_url' được truy cập
    public function getThumbnailUrlAttribute(): string
    {
        $mediaItem = $this->getFirstMedia('images');

        if ($mediaItem) {
            // Tạo URL tạm thời cho file thumbnail (conversion)
            return $mediaItem->getTemporaryUrl(now()->addMinutes(5), 'thumbnail');
        }
        return ''; // Trả về chuỗi rỗng hoặc một URL placeholder nếu không tìm thấy thumbnail
    }
}
