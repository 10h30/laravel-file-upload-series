<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * Hiển thị trang upload file.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('upload');
    }

    /**
     * Xử lý việc upload file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Tạo biến mới để lưu đường dẫn và tên file gốc
        $storedFilePath = null;
        $originalFilename = null;

        // Kiểm tra xem request có chứa file với tên là 'file' không
        if ($request->hasFile('file')) {
            // Lấy đối tượng file từ request
            $file = $request->file('file');
            // Lấy tên file gốc từ client
            $originalFilename = $file->getClientOriginalName();

            /*
            // Lưu file vào thư mục 'uploads' trên disk 'public' (thường là storage/app/public/uploads)
            // Laravel sẽ tự động tạo tên file duy nhất để tránh ghi đè
            // Phương thức store() trả về đường dẫn tương đối của file đã lưu (ví dụ: 'uploads/ten_file_duy_nhat.jpg')
            $storedFilePath = $file->store('uploads', 'public'); */

            // 3. Chuẩn bị các phần của tên file
            $filenameWithoutExtension = pathinfo($originalFilename, PATHINFO_FILENAME); // Lấy tên file không có phần mở rộng
            $extension = $file->getClientOriginalExtension(); // Lấy phần mở rộng
            $directory = 'uploads'; // Thư mục lưu file trên disk
            $disk = 'public'; // Disk sẽ sử dụng (được định nghĩa trong config/filesystems.php)

            // 4. Xác định tên file duy nhất
            $finalFilename = $originalFilename; // Bắt đầu với tên gốc
            $counter = 1;

            // Kiểm tra xem file đã tồn tại chưa
            while (Storage::disk($disk)->exists($directory . '/' . $finalFilename)) {
                // Nếu tồn tại, tạo tên mới với hậu tố 1,2,3,...
                $finalFilename = $filenameWithoutExtension . '-' . $counter . '.' . $extension;
                $counter++;
            }

            // 5. Lưu file bằng storeAs với tên file mới
            $storedFilePath = $file->storeAs($directory, $finalFilename, $disk); // Trả về đường dẫn tương đối: 'uploads/ten_file_cuoi_cung.jpg'
        }

        // Chuyển hướng về trang trước đó
        return back()->with('success', 'File uploaded successfully')
            // Gửi kèm đường dẫn file đã lưu vào session flash data
            ->with('stored_path', $storedFilePath)
            // Gửi kèm tên file gốc vào session flash data
            ->with('original_filename', $originalFilename);
    }
}
