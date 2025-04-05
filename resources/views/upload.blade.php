<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Upload</title>
</head>
<body>
<input type="file" id="fileInput" />
<button onclick="uploadFile()">Upload</button>
<div id="progress">Progress: 0%</div>

<script>
    async function uploadFile() {
        const fileInput = document.getElementById('fileInput');
        const file = fileInput.files[0];
        if (!file) return alert('Please select a file');

        const chunkSize = 1024 * 1024; // 1MB
        const totalChunks = Math.ceil(file.size / chunkSize);
        const uploadId = Date.now() + '-' + Math.random().toString(36).substring(2);

        for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
            const start = chunkIndex * chunkSize;
            const end = Math.min(start + chunkSize, file.size);
            const chunk = file.slice(start, end);

            const formData = new FormData();
            formData.append('uploadId', uploadId);
            formData.append('fileName', file.name);
            formData.append('chunkIndex', chunkIndex);
            formData.append('totalChunks', totalChunks);
            formData.append('chunk', chunk);

            await fetch('/upload', {
                method: 'POST',
                body: formData
            });

            document.getElementById('progress').innerText =
                `Progress: ${Math.round(((chunkIndex + 1) / totalChunks) * 100)}%`;
        }

        alert('Upload complete!');
    }
</script>
</body>
</html>
