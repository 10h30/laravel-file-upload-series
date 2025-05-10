<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    /**
     * Hiển thị trang upload file.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $uploads = Upload::latest()->get();
        return view('upload', compact('uploads'));
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
        $originalFilenames = []; // Array lưu tên gốc của các file
        $uploadedFiles = $request->file('files'); // Lấy array các đối tượng file đã upload
        $numberOfFiles = count($uploadedFiles); // Đếm số lượng file đã upload

        // Lặp qua từng file trong array $uploadedFiles
        foreach ($uploadedFiles as $file) {

            // Lấy tên file gốc từ client
            $originalFilename = $file->getClientOriginalName();
            $originalFilenames[] = $originalFilename; // Thêm tên gốc vào array

            // 3. Tạo bản ghi trong database cho model Upload:
            //    Lưu ý: Chúng ta chỉ cần lưu 'original_filename'.
            //    Các thông tin về đường dẫn file gốc và thumbnail sẽ do media-library quản lý.
            $uploadEntry = Upload::create([
                'original_filename' => $originalFilename,
            ]);

            // 4. Đây là phần quan trọng nhất - Thêm file vào Media Library:
            $uploadEntry->addMedia($file) // Thêm file vào Media Library
                ->toMediaCollection('images'); // Thêm file vào collection 'images'
        }

        // Chuyển hướng về trang trước đó
        return back()->with('success', 'You have successfully uploaded ' . $numberOfFiles . ' files')
            // Gửi kèm array các tên file gốc vào session flash data với key 'original_filenames'
            ->with('original_filenames', $originalFilenames);
    }

    public function destroy(Upload $upload)
    {
        // Spatie Media Library sẽ tự động xoá các file liên quan (file gốc và các file chuyển đổi)
        // khỏi disk khi model bị xoá.
        $upload->delete();

        // Chuyển hướng người dùng về trang trước đó với thông báo thành công
        return back()->with('success', 'You have successfully deleted ' . $upload->original_filename);
    }
}
