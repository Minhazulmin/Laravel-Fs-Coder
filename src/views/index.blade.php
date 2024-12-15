<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel Folder & File Management</title>
    <!-- Add Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <div class="container mx-auto my-10">
        <h1 class="text-3xl font-bold text-center mb-8">Folder & File Management</h1>

        @if (session('success'))
            <div id="success-alert"
                class="p-4 mb-4 text-green-800 bg-green-200 rounded-lg flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button onclick="dismissAlert('success-alert')" class="ml-2 text-green-800 hover:text-green-600">
                    &times;
                </button>
            </div>
        @endif

        @if (session('error'))
            <div id="error-alert" class="p-4 mb-4 text-red-800 bg-red-200 rounded-lg flex items-center justify-between">
                <span>{{ session('error') }}</span>
                <button onclick="dismissAlert('error-alert')" class="ml-2 text-red-800 hover:text-red-600">
                    &times;
                </button>
            </div>
        @endif

        <!-- Nav tabs for different functionalities -->
        <ul class="flex border-b mb-6" id="folderFileTabs" role="tablist">
            <li class="mr-1">
                <a class="inline-block py-2 px-4 text-blue-600 border-b-2 border-blue-600" id="create-folder-tab"
                    href="#create-folder" role="tab">Create Folder</a>
            </li>
            <li class="mr-1">
                <a class="inline-block py-2 px-4 text-gray-600 hover:text-blue-600" id="create-file-tab"
                    href="#create-file" role="tab">Create File</a>
            </li>
            <li>
                <a class="inline-block py-2 px-4 text-gray-600 hover:text-blue-600" id="edit-file-tab" href="#edit-file"
                    role="tab">Edit File</a>
            </li>
        </ul>

        <!-- Tab content -->
        <div class="tab-content" id="folderFileTabsContent">
            <!-- Create Folder Tab -->
            <div class="tab-pane active" id="create-folder" role="tabpanel">
                <h4 class="text-xl font-semibold mb-4">Create New Folder</h4>
                <form action="{{ route('fscoder.createFolder') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="parent_path" class="block text-gray-700 font-medium mb-2">Select Parent
                            Folder:</label>
                        <select name="parent_path" id="parent_path" class="block w-full px-4 py-2 border rounded-lg"
                            required>
                            <option value="">-- Select Folder --</option>
                            @foreach ($items as $item)
                                <option value="{{ $item['path'] }}">{{ $item['tree'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="new_folder_name" class="block text-gray-700 font-medium mb-2">New Folder
                            Name:</label>
                        <input type="text" name="new_folder_name" id="new_folder_name"
                            class="block w-full px-4 py-2 border rounded-lg" required>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Create Folder</button>
                </form>
            </div>

            <!-- Create File Tab -->
            <div class="tab-pane hidden" id="create-file" role="tabpanel">
                <h4 class="text-xl font-semibold mb-4">Create New File</h4>
                <form action="{{ route('fscoder.createFile') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="parent_path" class="block text-gray-700 font-medium mb-2">Select Folder to Create
                            File:</label>
                        <select name="parent_path" id="parent_path" class="block w-full px-4 py-2 border rounded-lg"
                            required>
                            <option value="">-- Select Folder --</option>
                            @foreach ($items as $item)
                                <option value="{{ $item['path'] }}">{{ $item['tree'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="new_file_name" class="block text-gray-700 font-medium mb-2">New File Name:</label>
                        <input type="text" name="new_file_name" id="new_file_name"
                            class="block w-full px-4 py-2 border rounded-lg" required>
                    </div>

                    <div class="mb-4">
                        <label for="file_content" class="block text-gray-700 font-medium mb-2">File Content:</label>
                        <textarea name="file_content" id="file_content" rows="12" class="block w-full px-4 py-2 border rounded-lg"
                            required></textarea>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Create File</button>
                </form>
            </div>

            <!-- Edit File Tab -->
            <div class="tab-pane hidden" id="edit-file" role="tabpanel">
                <h4 class="text-xl font-semibold mb-4">Edit Existing File</h4>
                <form action="{{ route('fscoder.editFile') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="file_path" class="block text-gray-700 font-medium mb-2">Select File to Edit:</label>
                        <select name="file_path" id="file_path" class="block w-full px-4 py-2 border rounded-lg"
                            required onchange="loadFileContent(this)">
                            <option value="">-- Select File --</option>
                            @foreach ($items as $item)
                                <option value="{{ $item['path'] }}">{{ $item['tree'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="file_content_edit" class="block text-gray-700 font-medium mb-2">Edit File
                            Content:</label>
                        <textarea name="file_content_edit" id="file_content_edit" rows="15"
                            class="block w-full px-4 py-2 border rounded-lg" required>{{ old('file_content_edit') }}</textarea>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Update File</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Tab functionality
        document.querySelectorAll("[id$='-tab']").forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll(".tab-pane").forEach(pane => pane.classList.add('hidden'));
                document.querySelector(this.getAttribute('href')).classList.remove('hidden');

                document.querySelectorAll("[id$='-tab']").forEach(tab => tab.classList.remove(
                    'text-blue-600', 'border-blue-600'));
                this.classList.add('text-blue-600', 'border-blue-600');
            });
        });
        // alert
        function dismissAlert(alertId) {
            const alertElement = document.getElementById(alertId);
            if (alertElement) {
                alertElement.style.transition = "opacity 0.3s ease";
                alertElement.style.opacity = "0";
                setTimeout(() => alertElement.remove(), 300);
            }
        }

        function loadFileContent(selectElement) {
            const filePath = selectElement.value;
            if (filePath) {
                fetch('/load-file-content', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            file_path: filePath
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.content) {
                            document.getElementById('file_content_edit').value = data.content;
                        } else {
                            document.getElementById('file_content_edit').value = '';
                            alert('Failed to load file content.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while loading the file content.');
                    });
            }
        }
    </script>

</body>

</html>
