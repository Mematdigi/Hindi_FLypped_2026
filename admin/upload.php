<?php
// --- CORS Configuration ---
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed = [
    'http://localhost', 'http://localhost:8000', 'http://localhost:8080',
    'http://127.0.0.1', 'http://127.0.0.1:8000', 
    'https://flypped.com', 'https://www.flypped.com',
    'https://flyppedhindi.com', 'https://www.flyppedhindi.com' // 🔥 Added your domain
];

if (in_array($origin, $allowed, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Vary: Origin"); 
} else {
    // Safety fallback for subdomains
    header("Access-Control-Allow-Origin: *");
}

header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

header('Content-Type: application/json');

// ---------- helpers ----------
// 🔥 FIXED: CKEditor 5 requires error messages in this exact nested JSON format
function json_error($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['error' => ['message' => $msg]]);
    exit;
}

// ---------- checks ----------
if (empty($_FILES['upload']) || $_FILES['upload']['error'] !== UPLOAD_ERR_OK) {
    $map = [
        UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the allowed size.',
        UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the form limit.',
        UPLOAD_ERR_PARTIAL    => 'The file was only partially uploaded.',
        UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder on server.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.'
    ];
    $err = $_FILES['upload']['error'] ?? UPLOAD_ERR_NO_FILE;
    json_error($map[$err] ?? 'Unknown upload error.', 400);
}

// ---------- validate & prepare ----------
$maxBytes = 8 * 1024 * 1024; // Increased to 8 MB for safe side
if ($_FILES['upload']['size'] > $maxBytes) {
    json_error('File too large. Max 8MB.', 413);
}

// Extension allowlist
$ext = strtolower(pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION));
$valid_ext = ['jpg','jpeg','png','gif','webp'];
if (!in_array($ext, $valid_ext, true)) {
    json_error('Invalid file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.', 415);
}

// MIME sniff (more reliable than extension alone)
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($_FILES['upload']['tmp_name']) ?: 'application/octet-stream';
$valid_mime = ['image/jpeg','image/png','image/gif','image/webp'];
if (!in_array($mime, $valid_mime, true)) {
    // Some servers label WEBP as octet-stream; permit by extension fallback
    if (!($ext === 'webp' && $mime === 'application/octet-stream')) {
        json_error('Invalid image MIME type.', 415);
    }
}

// ==========================================
// 🔥 SANITIZE FILENAME (ALLOW HINDI & ENGLISH)
// ==========================================
$base = pathinfo($_FILES['upload']['name'], PATHINFO_FILENAME);

// \p{L} allows letters from any language (Hindi, English, etc.)
// \p{N} allows numbers
// \-_  allows hyphens and underscores
// The 'u' modifier tells PHP to treat the string as UTF-8
$base = preg_replace('/[^\p{L}\p{N}\-_ ]/u', '', $base);

// Replace spaces with hyphens
$base = trim(preg_replace('/\s+/', '-', $base), '-');

// Fallback if the filename becomes empty
if ($base === '') {
    $base = 'post-image';
}

// Append a random number to guarantee the file name is unique every time
$filename = $base . '-' . rand(1000, 9999) . '.' . $ext;
// ==========================================

// Ensure target folder (changed to standard 'uploads')
$diskDir  = __DIR__ . '/uploads/';          // physical path: /admin/uploads/
$public   = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$public  .= '://' . $_SERVER['HTTP_HOST'];
$public  .= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); // /admin
$public  .= '/uploads/';                      // public URL base for images

// Auto-create folder if it doesn't exist
if (!is_dir($diskDir)) {
    if (!@mkdir($diskDir, 0755, true)) {
        json_error('Server cannot create uploads directory.', 500);
    }
}

if (!is_writable($diskDir)) {
    json_error('Uploads directory is not writable on the server.', 500);
}

// Avoid overwriting: add suffix if needed
$target = $diskDir . $filename;
$counter = 1;
while (file_exists($target)) {
    $filename = $base . '-' . rand(1000, 9999) . '-' . $counter++ . '.' . $ext;
    $target   = $diskDir . $filename;
}

// ---------- move ----------
if (!move_uploaded_file($_FILES['upload']['tmp_name'], $target)) {
    json_error('Could not move the uploaded file.', 500);
}

// Success response in the exact shape CKEditor 5 expects:
echo json_encode([
    'url' => $public . rawurlencode($filename)
]);