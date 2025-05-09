<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans antialiased">
    <?php if($errors->has("file")): ?>
        <div
            class="container mx-auto mt-10 p-6 bg-red-100 border border-red-400 text-red-700 rounded-lg shadow-md max-w-md">
            <?php echo e($errors->first("file")); ?></div>
    <?php endif; ?>
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-700">Upload File</h1>
        <form action="<?php echo e(route("upload.store")); ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label for="files" class="block text-sm font-medium text-gray-700 mb-1">Choose files</label>
                <input type="file" name="files[]" id="files" multiple
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

    
    <?php if(session("success")): ?>
        <div
            class="container mx-auto mt-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-md max-w-md">
            <p class="font-bold">Success!</p>
            <p><?php echo e(session("success")); ?></p>

            
            <?php if(session("stored_paths") && is_array(session("stored_paths"))): ?>
                <div class="mt-4">
                    <p>Uploaded Files:</p>
                    
                    <?php $__currentLoopData = session("stored_paths"); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="border p-4 mt-2">
                            <p class="text-sm text-gray-600">Original Filename:
                                <?php echo e(session("original_filenames")[$index]); ?>

                            </p>
                            <p class="text-sm text-gray-600">Stored Path: <?php echo e($path); ?></p>
                            <img src="<?php echo e($path); ?>" alt="Uploaded Image <?php echo e($index + 1); ?>"
                                class="mt-2 rounded max-w-full h-auto border">
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if(count($uploads) > 0): ?>
        <div class="container mx-auto mt-10 p-10 bg-white rounded-lg shadow-md max-w-md">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Previously Uploaded Files:</h2>
            <?php $__currentLoopData = $uploads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $upload): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <ul>
                    <li class="flex items-center justify-between mb-4">
                        <a class="flex items-center gap-4 py-2" href="<?php echo e($upload->url); ?>" target="_blank">
                            <img src="<?php echo e($upload->url); ?>" alt="<?php echo e($upload->original_filename); ?>" width="50" height="50">
                            <span class="text-sm text-gray-700 hover:text-blue-600"><?php echo e($upload->original_filename); ?></span>
                        </a>
                        <form action="<?php echo e(route("upload.destroy", $upload->id)); ?>" method="POST"
                            style="display:inline;"
                            onsubmit="return confirm('Are you sure you want to delete this file?');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field("DELETE"); ?>
                            <button type="submit"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Delete</button>
                        </form>
                    </li>
                </ul>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

</body>

</html>
<?php /**PATH /Users/thuanbui/Herd/upload/resources/views/upload.blade.php ENDPATH**/ ?>