<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css"
        rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans antialiased">
    @if ($errors->any())
        <div class="container mx-auto mt-10 p-6 bg-red-100 border border-red-400 text-red-700 rounded-lg shadow-md max-w-md">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-700">Upload File</h1>

        <form action="{{ route("upload.store") }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label for="files" class="block text-sm font-medium text-gray-700 mb-1">Choose files</label>
                <input type="file" name="files[]" id="files" multiple data-max-files="5" />
            </div>
            <button type="submit"
                class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Upload
            </button>
        </form>
    </div>

    {{-- Display Success Message and Uploaded File Info --}}
    @if (session("success"))
        <div
            class="container mx-auto mt-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-md max-w-md">
            <p class="font-bold">Success!</p>
            <p>{{ session("success") }}</p>

            {{-- Kiểm tra xem session có chứa 'stored_paths' (mảng đường dẫn các file đã lưu)
                 và giá trị đó có phải là một mảng hợp lệ không --}}
            @if (session("stored_paths") && is_array(session("stored_paths")))
                <div class="mt-4">
                    <p>Uploaded Files:</p>
                    {{-- Lặp qua array các đường dẫn file đã lưu. $index là chỉ số, $path là đường dẫn --}}
                    @foreach (session("stored_paths") as $index => $path)
                        <div class="border p-4 mt-2">
                            <p class="text-sm text-gray-600">Original Filename:
                                {{ session("original_filenames")[$index] }}
                            </p>
                            {{-- <p class="text-sm text-gray-600">Stored Path: {{ $path }}</p> --}}
                            <img src="{{ $path }}" alt="Uploaded Image {{ $index + 1 }}"
                                class="mt-2 rounded max-w-full h-auto border">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    @if (count($uploads) > 0)
        <div class="container mx-auto mt-10 p-10 bg-white rounded-lg shadow-md max-w-md">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Previously Uploaded Files:</h2>
            @foreach ($uploads as $upload)
                <ul>
                    <li class="flex items-center justify-between mb-4">
                        <a class="flex items-center gap-4 py-2" href="{{ $upload->url }}" target="_blank">
                            <img src="{{ $upload->thumbnail_url }}" alt="{{ $upload->original_filename }}"
                                width="50" height="50">
                            <span
                                class="text-sm text-gray-700 hover:text-blue-600">{{ $upload->original_filename }}</span>
                        </a>
                        <form action="{{ route("upload.destroy", $upload->id) }}" method="POST"
                            style="display:inline;"
                            onsubmit="return confirm('Are you sure you want to delete this file?');">
                            @csrf
                            @method("DELETE")
                            <button type="submit"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Delete</button>
                        </form>
                    </li>
                </ul>
            @endforeach
        </div>
    @endif
    <!-- FilePond JS from CDN -->
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.min.js">
    </script>
    <script>
        //FilePond.registerPlugin(FilePondPluginImagePreview);
        // Register plugins
        FilePond.registerPlugin(
            FilePondPluginImagePreview,
            FilePondPluginFileValidateSize,
            FilePondPluginFileValidateType
        );

        const inputElement = document.querySelector('input[id="files"]');
        FilePond.create(inputElement, {
            allowMultiple: true,
            maxFiles: 5,
            acceptedFileTypes: ['image/*'],
            maxFileSize: '100KB',
            labelMaxFileSizeExceeded: 'The file is too big',
            storeAsFile: true,
            onwarning: (error, file, status) => {
                console.warn('FilePond warning:', error, file, status);
                alert('Warning: ' + error.main);
            },
            onerror: (error, file, status) => {
                console.error('FilePond error:', error, file, status);
                alert('Error: ' + error.main);
            }
        });
    </script>

</body>

</html>
