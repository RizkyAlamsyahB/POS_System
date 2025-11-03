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
 *     class Home extends BaseController
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

        // E.g.: $this->session = service('session');
    }

    /**
     * Auto-create directory if not exists
     * 
     * @param string $path Directory path to create
     * @param int $permissions Directory permissions (default: 0755)
     * @return bool True if directory exists or was created successfully
     */
    protected function ensureDirectoryExists(string $path, int $permissions = 0755): bool
    {
        if (!is_dir($path)) {
            return mkdir($path, $permissions, true);
        }
        return true;
    }

    /**
     * Get upload path for specific type and auto-create if needed
     * 
     * @param string $type Upload type (e.g., 'products', 'users', 'categories')
     * @return string Full path to upload directory
     */
    protected function getUploadPath(string $type): string
    {
        $uploadPath = FCPATH . 'uploads/' . $type;
        $this->ensureDirectoryExists($uploadPath);
        return $uploadPath;
    }
}
