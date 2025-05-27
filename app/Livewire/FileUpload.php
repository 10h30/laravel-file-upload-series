<?php

namespace App\Livewire;

use App\Models\Upload;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class FileUpload extends Component
{
    use WithFileUploads;  // Sử dụng trait WithFileUploads để xử lý upload file trong Livewire.

    #[Validate(['files.*' => 'image|max:2048'])] // Thuộc tính Validate dùng để kiểm tra các file upload phải là hình ảnh và dung lượng tối đa 2MB cho mỗi file.
    public $files = [];  // Khai báo array cho danh sách các file được chọn upload.

    public $persistedUploads = []; // Khai báo array cho danh sách các file đã được upload thành công (được lưu trongatabase).

    /**
     * Lưu các file đã chọn vào thư mục lưu trữ và cập nhật cơ sở dữ liệu.
     *
     * @return void
     */
    public function updatedFiles()
    {
        $this->validate();
        //dd('Hello');
        $uploadedFiles = $this->files;
        $numberOfFiles = count($uploadedFiles); // Đếm số lượng file đã upload

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

        $this->updatePersistedUploads();
        $this->reset('files');
        session()->flash('success',  'You have successfully uploaded ' . $numberOfFiles . ' files');
    }

    /**
     * Xóa file đã upload khỏi thư mục lưu trữ và cơ sở dữ liệu.
     *
     * @param  \App\Models\Upload  $upload Model Upload cần xóa.
     * @return void
     */
    public function destroy(Upload $upload) {
        $upload->delete();
        $this->updatePersistedUploads();
        session()->flash('success', 'You have successfully deleted ' . $upload->original_filename);
    }

    public function updatePersistedUploads() {
        $this->persistedUploads = Upload::latest()->get();
    }
    /**
     * Hàm được gọi khi component được khởi tạo.
     * Lấy danh sách các file đã upload từ cơ sở dữ liệu.
     *
     * @return void
     */
    public function mount()
    {
        $this->updatePersistedUploads();
    }
    /**
     * Render view cho component.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.file-upload', [
            'uploads' => $this->persistedUploads
        ]);
    }
}
