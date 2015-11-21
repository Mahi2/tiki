<?php

class Tiki_Webservice
{
	private $name;
	public $url;
	public $body;
	public $schemaVersion;
	public $schemaDocumentation;

	private $templates = array();
	private $all = false;

	public static function create( $name ) // {{{
	{
		if( ! ctype_alpha( $name ) ) {
			return;
		}

		$ws = new self;
		$ws->name = strtolower( $name );

		return $ws;
	} // }}}

	public static function getService( $name ) // {{{
	{
		$name = strtolower( $name );

		global $tikilib;
		$result = $tikilib->query( "SELECT url, body, schema_version, schema_documentation FROM tiki_webservice WHERE service = ?", array( $name ) );

		while( $row = $result->fetchRow() ) {
			$service = new self;

			$service->name = $name;
			$service->url = $row['url'];
			$service->body = $row['body'];
			$service->schemaVersion = $row['schema_version'];
			$service->schemaDocumentation = $row['schema_documentation'];

			return $service;
		}
	} // }}}

	public static function getList() // {{{
	{
		global $tikilib;

		$result = $tikilib->query( "SELECT service FROM tiki_webservice ORDER BY service" );
		$list = array();

		while( $row = $result->fetchRow() )
			$list[] = $row['service'];

		return $list;
	} // }}}

	function save() // {{{
	{
		global $tikilib;
		$tikilib->query( "DELETE FROM tiki_webservice WHERE service = ?", array( $this->name ) );

		$tikilib->query( "INSERT INTO tiki_webservice (service, url, body, schema_version, schema_documentation) VALUES(?,?,?,?,?)", 
			array(
				$this->name,
				$this->url,
				$this->body,
				$this->schemaVersion,
				$this->schemaDocumentation,
			) );
	} // }}}

	function delete() // {{{
	{
		global $tikilib;
		$tikilib->query( "DELETE FROM tiki_webservice WHERE service = ?", array( $this->name ) );
		$tikilib->query( "DELETE FROM tiki_webservice_template WHERE service = ?", array( $this->name ) );
	} // }}}

	function getParameters() // {{{
	{
		if( preg_match_all( "/%(\w+)%/", $this->url . ' ' . $this->body, $matches, PREG_PATTERN_ORDER ) )
			return array_diff( $matches[1], array( 'service', 'template' ) );
		else
			return array();
	} // }}}

	function getParameterMap( $params ) // {{{
	{
		$parameters = array();

		foreach( $this->getParameters() as $key => $name ) {
			if( isset( $params[$name] ) ) {
				$parameters[$name] = $params[$name];
			} else {
				$parameters[$name] = '';
			}
		}

		return $parameters;
	} // }}}

	function performRequest( $params ) // {{{
	{
		$built = $this->url;
		$builtBody = $this->body;

		$map = $this->getParameterMap( $params );
		foreach( $map as $name => $value ) {
			$built = str_replace( "%$name%", urlencode( $value ), $built );
			$builtBody = str_replace( "%$name%", urlencode( $value ), $builtBody );
		}

		if( $built ) {
			$ointegrate = new OIntegrate;
			$ointegrate->addAcceptTemplate( 'smarty', 'tikiwiki' );
			$ointegrate->addAcceptTemplate( 'smarty', 'html' );
			$ointegrate->addAcceptTemplate( 'javascript', 'html' );

			if( $this->schemaVersion ) {
				$ointegrate->addSchemaVersion( $this->schemaVersion );
			}

			$response = $ointegrate->performRequest( $built, $builtBody );
			
			return $response;
		}
	} // }}}

	function addTemplate( $name ) // {{{
	{
		if( ! ctype_alpha( $name ) || empty( $name ) ) {
			return;
		}

		$template = new Tiki_Webservice_Template;
		$template->webservice = $this;
		$template->name = strtolower( $name );

		$this->templates[$name] = $template;

		return $template;
	} // }}}

	function removeTemplate( $name ) // {{{
	{
		global $tikilib;

		$tikilib->query( "DELETE FROM tiki_webservice_template WHERE service = ? AND template = ?", array( $this->name, $name ) );
	} // }}}

	function getTemplates() // {{{
	{
		if( $this->all )
			return $this->templates;

		global $tikilib;
		$result = $tikilib->query( "SELECT template, last_modif, engine, output, content FROM tiki_webservice_template WHERE service = ?", array( $this->name ) );

		while( $row = $result->fetchRow() ) {
			$template = new Tiki_Webservice_Template;
			$template->webservice = $this;
			$template->name = $row['template'];
			$template->lastModif = $row['last_modif'];
			$template->engine = $row['engine'];
			$template->output = $row['output'];
			$template->content = $row['content'];

			$this->templates[$template->name] = $template;
		}

		$this->all = true;
		return $this->templates;
	} // }}}

	function getTemplate( $name ) // {{{
	{
		if( isset( $this->templates[$name] ) )
			return $this->templates[$name];

		global $tikilib;
		$result = $tikilib->query( "SELECT last_modif, engine, output, content FROM tiki_webservice_template WHERE service = ? AND template = ?", array( $this->name, $name ) );

		while( $row = $result->fetchRow() ) {
			$template = new Tiki_Webservice_Template;
			$template->webservice = $this;
			$template->name = $name;
			$template->lastModif = $row['last_modif'];
			$template->engine = $row['engine'];
			$template->output = $row['output'];
			$template->content = $row['content'];

			$this->templates[$name] = $template;
			return $template;
		}
	} // }}}

	function getName() // {{{
	{
		return $this->name;
	} // }}}
}

class Tiki_Webservice_Template
{
	public $webservice;
	public $name;
	public $engine;
	public $output;
	public $content;
	public $lastModif;

	function save() // {{{
	{
		global $tikilib;
		$tikilib->query( "DELETE FROM tiki_webservice_template WHERE service = ? AND template = ?", array( $this->webservice->getName(), $this->name ) );

		$tikilib->query( "INSERT INTO tiki_webservice_template (service, template, engine, output, content, last_modif) VALUES(?,?,?,?,?,?)",
			array(
				$this->webservice->getName(),
				$this->name,
				$this->engine,
				$this->output,
				$this->content,
				time(),
			) );
	} // }}}

	function getTemplateFile() // {{{
	{
		$token = sprintf( "%s_%s", $this->webservice->getName(), $this->name );
		$file = "temp/cache/" . md5( $token ) . '.tpl';

		if( ! file_exists($file) || $this->lastModif > filemtime($file) ) {
			file_put_contents( $file, $this->content );
		}

		return realpath($file);
	} // }}}

	function render( OIntegrate_Response $response, $outputContext ) // {{{
	{
		return $response->render( $this->engine, $this->output, $outputContext, $this->getTemplateFile() );
	} // }}}
}