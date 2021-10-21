<?php

namespace Velldoris\Controllers\Back;

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
class BaseController extends Controller
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
     * @var array
     */
    protected $helpers = ['filesystem', 'html', 'form', 'cookie', 'velldoris'];
    
    protected $default_data = [];
    
    protected $velldoris_cookie_config = null;
    protected $velldorisModel = null;
    protected $resourceModel = null;
    protected $templateModel = null;
    protected $templateVariableModel = null;
    protected $languageModel = null;
    protected $variableModel = null;
    protected $variableValueModel = null;
    protected $affiliateService = null;
    
    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        
        // Preload any models, libraries, etc, here.
        
        // E.g.: $this->session = \Config\Services::session();
        
        $this->velldoris_cookie_config = config('VelldorisCookie');
        $this->velldorisModel = model('Velldoris');
        $this->resourceModel = model('Resource');
        $this->templateModel = model('Template');
        $this->templateVariableModel = model('TemplateVariable');
        $this->languageModel = model('Language');
        $this->variableModel = model('Variable');
        $this->variableValueModel = model('VariableValue');
        $this->affiliateService = service('Affiliate');
        
        $velldoris_cookie_config = [
            'prefix' => $this->velldoris_cookie_config->prefix,
            'expires' => $this->velldoris_cookie_config->expires,
            'path' => $this->velldoris_cookie_config->path,
            'domain' => $this->velldoris_cookie_config->domain,
            'secure' => $this->velldoris_cookie_config->secure,
            'samesite' => $this->velldoris_cookie_config->samesite,
        ];
        
        $this->default_data =
            [
                'title' => '',
                'resources_tree' => $this->resourceModel->getResourcesTree(),
                'velldoris_cookie_config' => json_encode($velldoris_cookie_config),
            ];
    }
}