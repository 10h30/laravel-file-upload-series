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
        // Kiểm tra request có chứa file không? và có dáp ứng các yêu cầu không
        $request->validate([
            'files' => 'required|array', // Tên input là 'files[]' trong HTML
            'files.*' => 'required|image|mimes:jpg,jpeg,png|max:2048', // max = 2MB mỗi file
        ]);

        // Tạo biến mới để lưu đường dẫn và tên file gốc
        $storedFilePaths = []; // Array lưu đường dẫn các file đã lưu thành công
        $originalFilenames = []; // Array lưu tên gốc của các file
        $uploadedFiles = $request->file('files'); // Lấy array các đối tượng file đã upload
        $numberOfFiles = count($uploadedFiles); // Đếm số lượng file đã upload


        // Lặp qua từng file trong array $uploadedFiles
        foreach ($uploadedFiles as $file) {

            // Lấy tên file gốc từ client
            $originalFilename = $file->getClientOriginalName();
            $originalFilenames[] = $originalFilename; // Thêm tên gốc vào array

            // Chuẩn bị các phần của tên file
            $filenameWithoutExtension = pathinfo($originalFilename, PATHINFO_FILENAME); // Lấy tên file không có phần mở rộng
            $extension = $file->getClientOriginalExtension(); // Lấy phần mở rộng
            $directory = 'uploads'; // Thư mục lưu file trên disk
            $disk = 'public'; // Disk public sẽ sử dụng (được định nghĩa trong config/filesystems.php)

            // Xác định tên file duy nhất
            $finalFilename = $originalFilename; // Bắt đầu với tên gốc
            $counter = 1;

            // Kiểm tra xem file đã tồn tại chưa
            while (Storage::disk($disk)->exists($directory . '/' . $finalFilename)) {
                // Nếu tồn tại, tạo tên mới với hậu tố 1,2,3,...
                $finalFilename = $filenameWithoutExtension . '-' . $counter . '.' . $extension;
                $counter++;
            }

            // Lưu file bằng storeAs với tên file mới
            $storedFilePath = $file->storeAs($directory, $finalFilename, $disk); // Trả về đường dẫn tương đối: 'uploads/ten_file_cuoi_cung.jpg'
            $storedFilePaths[] = $storedFilePath; // Thêm đường dẫn file đã lưu vào array $storedFilePaths
        }


        // Chuyển hướng về trang trước đó
        return back()->with('success', 'You have successfully uploaded ' . $numberOfFiles . ' files')
            // Gửi kèm array các đường dẫn file đã lưu vào session flash data với key 'stored_paths'
            ->with('stored_paths', $storedFilePaths)
            // Gửi kèm array các tên file gốc vào session flash data với key 'original_filenames'
            ->with('original_filenames', $originalFilenames);
    }
}
