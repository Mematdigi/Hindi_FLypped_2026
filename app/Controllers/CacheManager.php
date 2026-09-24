<?php
namespace App\Controllers;

class CacheManager extends BaseController
{
    // ⚠️ CRITICAL: Replace with YOUR actual IP address
    private $allowedIPs = ['YOUR_IP_HERE', '127.0.0.1'];
    
    public function __construct()
    {
        // Security check
        if (!in_array($this->request->getIPAddress(), $this->allowedIPs)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }
    
    public function index()
    {
        $cache = \Config\Services::cache();
        $cacheDir = WRITEPATH . 'cache/';
        
        $files = glob($cacheDir . '*');
        $totalSize = 0;
        $count = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $totalSize += filesize($file);
                $count++;
            }
        }
        
        $html = '<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <title>Cache Manager - Hindi Flypped</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .stat { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .stat strong { display: inline-block; width: 200px; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
        .btn-danger { background: #dc3545; }
        .success { color: green; }
        .warning { color: orange; }
    </style>
</head>
<body>
    <h1>🚀 Hindi Flypped Cache Manager</h1>
    
    <div class="stat">
        <strong>Total Cached Files:</strong> ' . number_format($count) . '
    </div>
    
    <div class="stat">
        <strong>Total Cache Size:</strong> ' . round($totalSize / 1024 / 1024, 2) . ' MB
    </div>
    
    <div class="stat">
        <strong>OpCache Status:</strong> ' . (function_exists('opcache_get_status') ? '<span class="success">✓ Enabled</span>' : '<span class="warning">✗ Disabled</span>') . '
    </div>';
    
        if (function_exists('opcache_get_status')) {
            $opcache = opcache_get_status();
            $html .= '
    <div class="stat">
        <strong>OpCache Memory:</strong> ' . round($opcache['memory_usage']['used_memory'] / 1024 / 1024, 2) . ' MB used / ' . 
                 round($opcache['memory_usage']['free_memory'] / 1024 / 1024, 2) . ' MB free
    </div>
    
    <div class="stat">
        <strong>OpCache Hit Rate:</strong> ' . round($opcache['opcache_statistics']['opcache_hit_rate'], 2) . '%
    </div>';
        }
        
        $html .= '
    <div style="margin-top: 30px;">
        <a href="' . base_url('cache-manager/clear') . '" class="btn btn-danger" onclick="return confirm(\'Clear all cache?\')">🗑️ Clear Cache</a>
        <a href="' . base_url() . '" class="btn">🏠 Back to Site</a>
    </div>
</body>
</html>';
        
        return $html;
    }
    
    public function clear()
    {
        $cache = \Config\Services::cache();
        $cache->clean();
        
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        
        return redirect()->to('/cache-manager')->with('message', 'Cache cleared successfully!');
    }
    
    public function stats()
    {
        $cache = \Config\Services::cache();
        $cacheDir = WRITEPATH . 'cache/';
        
        $files = glob($cacheDir . '*');
        $stats = [
            'total_files' => 0,
            'total_size' => 0,
            'by_type' => [],
        ];
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $stats['total_files']++;
                $stats['total_size'] += filesize($file);
            }
        }
        
        return $this->response->setJSON($stats);
    }
}