<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class MinifyHTML implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Do nothing
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $buffer = $response->getBody();
        
        if (strpos($response->getHeaderLine('Content-Type'), 'text/html') !== false) {
            // Remove HTML comments (except IE conditionals)
            $buffer = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $buffer);
            
            // Remove whitespace between tags
            $buffer = preg_replace('/>\s+</', '><', $buffer);
            
            // Remove multiple spaces
            $buffer = preg_replace('/\s+/', ' ', $buffer);
            
            $response->setBody($buffer);
        }
        
        return $response;
    }
}
