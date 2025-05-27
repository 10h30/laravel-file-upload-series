<div>
    {{-- Upload File Form --}}
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-700">Upload File</h1>
       {{--  <form wire:submit.prevent="save"> --}}
            <div class="mb-4">
                 @if ($files)
                    @foreach ($files as $file)
                        <img src="{{ $file->temporaryUrl() }}">
                    @endforeach
                @endif
            </div>

            <div class="mb-4">
                <input type="file" wire:model="files" wire:loading.attr="disabled" multiple>
            </div>

            <div wire:loading wire:target="files">Uploading...</div>

            <div class="mb-4">
                @error("files.*")
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

           {{--  <button type="submit"
                class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Upload
            </button> --}}

        {{-- </form> --}}
    </div>

    {{-- Display Success Message --}}
    @if (session("success"))
        <div
            class="container mx-auto mt-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-md max-w-md">
            <p class="font-bold">Success!</p>
            <p>{{ session("success") }}</p>
        </div>
    @endif

    {{-- Display Uploaded File Info --}}
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
                        <button type="button"
                            onclick="if (confirm('Are you sure you want to delete this file?')) { @this.destroy({{ $upload->id }}) }"
                            wire:loading.attr="disabled"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Delete
                        </button>

                        <div wire:loading wire:target="destroy({{ $upload->id }})" class="text-gray-500 text-sm">
                            Deleting...
                        </div>
                    </li>
                </ul>
            @endforeach
        </div>
    @endif
</div>
