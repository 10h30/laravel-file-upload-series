<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;

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
        $storedFilePaths = []; // Array lưu đường dẫn các file đã lưu thành công
        $originalFilenames = []; // Array lưu tên gốc của các file
        $uploadedFiles = $request->file('files'); // Lấy array các đối tượng file đã upload
        $numberOfFiles = count($uploadedFiles); // Đếm số lượng file đã upload

        $manager = new ImageManager(Driver::class);

        // Lặp qua từng file trong array $uploadedFiles
        foreach ($uploadedFiles as $file) {

            // Lấy tên file gốc từ client
            $originalFilename = $file->getClientOriginalName();
            $originalFilenames[] = $originalFilename; // Thêm tên gốc vào array

            // Chuẩn bị các phần của tên file
            $filenameWithoutExtension = pathinfo($originalFilename, PATHINFO_FILENAME); // Lấy tên file không có phần mở rộng
            $extension = $file->getClientOriginalExtension(); // Lấy phần mở rộng
            $directory = 'uploads'; // Thư mục lưu file trên disk
            $disk = 'minio'; // Disk S3 sẽ sử dụng (được định nghĩa trong config/filesystems.php)

            // Xác định tên file duy nhất
            $finalFilename = $originalFilename; // Bắt đầu với tên gốc
            $counter = 1;

            // Kiểm tra xem file đã tồn tại chưa
            while (Storage::disk($disk)->exists($directory . '/' . $finalFilename)) {
                // Nếu tồn tại, tạo tên mới với hậu tố 1,2,3,...
                $finalFilename = $filenameWithoutExtension . '-' . $counter . '.' . $extension;
                $counter++;
            }

            // Lưu file bằng storeAs với tên file mới và trả về đường dẫn tương đối: 'uploads/ten_file_cuoi_cung.jpg'
            $storedFilePath = $file->storeAs($directory, $finalFilename, $disk);

            // Tạo thumbnail bằng Intervention Image
            $thumbnail = $manager->read($file->getRealPath())
                ->resize(100, 100); // Resize to fit 100x100, maintaining aspect ratio

            // Đường dẫn tương đối của file thumbnail: 'uploads/thumbnail-ten_file_cuoi_cung.jpg'
            $thumbnailStoragePath = $directory . '/thumbnail-' . $finalFilename;

            // Lưu thumbnail vào disk
            Storage::disk($disk)->put($thumbnailStoragePath, $thumbnail->encode());

            // Trả về Temporary URL của file
            $urlFilePath = Storage::disk($disk)->temporaryUrl($thumbnailStoragePath, now()->addMinutes(5));

            // Thêm đường dẫn file đã lưu vào array $storedFilePaths
            $storedFilePaths[] = $urlFilePath;

            // Tạo bản ghi mới trong table uploads của database
            Upload::create([
                'filename' => $storedFilePath,
                'original_filename' => $originalFilename,
                'thumbnail' => $thumbnailStoragePath,
            ]);

        }

        // Chuyển hướng về trang trước đó
        return back()->with('success', 'You have successfully uploaded ' . $numberOfFiles . ' files')
            // Gửi kèm array các đường dẫn file đã lưu vào session flash data với key 'stored_paths'
            ->with('stored_paths', $storedFilePaths)
            // Gửi kèm array các tên file gốc vào session flash data với key 'original_filenames'
            ->with('original_filenames', $originalFilenames);
    }

    public function destroy(Upload $upload)
    {
        // Xoá file vật lý khỏi disk 'public' dựa vào đường dẫn lưu trong $upload->filename
          // The disk used for storing was 's3'.
        $disk = 'minio';

        if (Storage::disk($disk)->exists($upload->filename)) {
            Storage::disk($disk)->delete($upload->filename);
        }

        if (Storage::disk($disk)->exists($upload->thumbnail)) {
            Storage::disk($disk)->delete($upload->thumbnail);
        }

        // Xoá bản ghi tương ứng trong database
        $upload->delete();

        // Chuyển hướng người dùng về trang trước đó với thông báo thành công
        return back()->with('success', 'You have successfully deleted ' . $upload->original_filename);
    }
}
