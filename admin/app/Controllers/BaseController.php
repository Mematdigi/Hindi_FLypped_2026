<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 * class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
    }

    // ==============================================================
    // INDEXNOW AUTOMATION FUNCTION (flyppedhindi.com)
    // ==============================================================
    protected function submitToIndexNow($urlList) 
    {
        // Yahan Hindi domain aur nayi API key set ki gayi hai
        $host = 'flyppedhindi.com'; 
        $key = '842f3634bc5f4f17b17dd869fc18a65d'; 
        $endpoint = 'https://api.indexnow.org/indexnow';
        
        $data = [
            'host' => $host,
            'key' => $key,
            'keyLocation' => "https://{$host}/{$key}.txt",
            'urlList' => $urlList
        ];
        
        $payload = json_encode($data);
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($payload)
        ]);
        
        $response = curl_exec($ch);
        // API ka HTTP status code nikalne ke liye
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Logs mein status save karne ke liye taaki aap baad mein 200/202 check kar sakein
        if ($httpcode == 200 || $httpcode == 202) {
            log_message('info', " INDEXNOW SUCCESS: API returned {$httpcode} for URL(s): " . implode(', ', $urlList));
        } else {
            log_message('error', "❌ INDEXNOW ERROR: API returned {$httpcode}. Response: {$response}");
        }
        
        return $httpcode;
    }
}