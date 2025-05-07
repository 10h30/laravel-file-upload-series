<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Upload extends Model
{
    protected $fillable = [
        'filename',
        'original_filename'
    ];

    public function getUrlAttribute(): string
    {
        // Đảm bảo 's3' là tên disk chính xác được sử dụng để lưu trữ các file này.
        $disk = 's3';
        if ($this->filename) {
            return Storage::disk($disk)->url($this->filename);
        }
        return ''; // Hoặc xử lý một cách thích hợp nếu tên tệp là null
    }
}
