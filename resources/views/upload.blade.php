<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Chunk Upload</title>
</head>
<body>
<h2>Upload large file</h2>
<input type="file" id="file" />
<button onclick="upload()">Upload</button>

<script>
    async function upload() {
        const file = document.getElementById('file').files[0];
        if (!file) return alert("Choose a file");

        const chunkSize = 1024 * 1024;
        const totalChunks = Math.ceil(file.size / chunkSize);
        const uploadId = Date.now().toString();

        for (let index = 0; index < totalChunks; index++) {
            const start = index * chunkSize;
            const end = Math.min(start + chunkSize, file.size);
            const blob = file.slice(start, end);

            const formData = new FormData();
            formData.append('file', blob);
            formData.append('index', index);
            formData.append('total', totalChunks);
            formData.append('upload_id', uploadId);

            await fetch('/upload/chunk', {
                method: 'POST',
                body: formData
            }).then(res => res.json()).then(console.log);
        }

        alert('Upload completed');
    }
</script>
</body>
</html>
