<?php defined('SYSPATH') or die('No direct script access.');

class Subdomain_Route extends Kohana_Route {

    const SUBDOMAIN_WILDCARD = '*', SUBDOMAIN_EMPTY = '' ;

	public static $default_subdomains = array( self::SUBDOMAIN_EMPTY, 'www' ) ;

	/**
	 * @var  string  route SUBDOMAIN
	 */
	protected $_subdomain;

    /**
     * @var  string domains
     */
    protected $_domains;
	
	public function __construct($uri = NULL, $regex = NULL) {
		parent::__construct($uri, $regex) ;
		
        // Set default subdomains in this route rule
		$this->_subdomain = self::$default_subdomains;
        $this->_domains   = self::SUBDOMAIN_WILDCARD;
	}


    /**
     * Set one or more subdomains to execute this route
     *
	 *     Route::set('default', '(<controller>(/<action>(/<id>)))')
     *         ->subdomains(array(Route::SUBDOMAIN_EMPTY, 'www1', 'foo', 'bar'))
	 *         ->defaults(array(
	 *             'controller' => 'welcome',
	 *         ));
     *
	 * @param   array    name(s) of subdomain(s) to apply in route
     * @return $this
     */      
    public function subdomains(array $name) {
        $this->_subdomain = $name ;

        return $this ;
    }

    /**
     * @param array $domains
     *
     * @return $this
     */
    public function domains(array $domains) {
        $this->_domains = $domains;
        
        return $this;
    }
	
	public function matches($uri, array $subdomain = NULL) {
		$subdomain = ($subdomain === NULL) ? Request::$subdomain : $subdomain;
		
		if($subdomain === FALSE) {
			$subdomain = self::SUBDOMAIN_EMPTY ;
		}
		
		if (in_array(self::SUBDOMAIN_WILDCARD, $this->_subdomain) || in_array($subdomain, $this->_subdomain)) {
            if ($this->_domains === self::SUBDOMAIN_WILDCARD) {
                return parent::matches($uri) ;
            }
            
            if (!is_array($this->_domains) || !isset($_SERVER['HTTP_HOST'])) {
                return false;
            }
            
            $host_rev = strrev($_SERVER['HTTP_HOST']);
            foreach ($this->_domains as $v) {
                if ($v === $_SERVER['HTTP_HOST'] || stripos($host_rev, strrev($v).'.') === 0) {
                    return parent::matches($uri);
                }
            }
		}
		
		return FALSE;
	}
}
