<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans antialiased">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-700">Upload File</h1>
        <form action="{{ route("upload.store") }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label for="file" class="block text-sm font-medium text-gray-700 mb-1">Choose file</label>
                <input type="file" name="file" id="file"
                    class="block w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-full file:border-0
                              file:text-sm file:font-semibold
                              file:bg-blue-50 file:text-blue-700
                              hover:file:bg-blue-100" />
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

            @if (session("stored_path"))
                <div class="mt-4">
                    <p class="text-sm text-gray-600">Original Filename: {{ session("original_filename", "N/A") }}</p>
                    <p class="text-sm text-gray-600">Stored Path: {{ session("stored_path") }}</p>
                    <img src="{{ session("stored_path") }}" alt="Uploaded Image"
                        class="mt-2 rounded max-w-full h-auto border">
                </div>
            @endif
        </div>
    @endif

</body>

</html>
