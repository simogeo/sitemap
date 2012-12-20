<?php
/**
 * @package linea21.plugins
 * @subpackage sitemap
 * @author Simon Georget <simon@linea21.com>
 * @version $id SVN
 * @access public
 * @license http://opensource.org/licenses/MIT
 *
 * Inspired from
 * http://stackoverflow.com/questions/3948947/can-i-use-wget-to-generate-a-sitemap-of-a-website-given-its-url
 */

class sitemap {

	private $config = array();
	private	$locations = array();
	private  $log = '';
	private  $logtag = 'p';

	public function __construct($url, $sitemap_path, $working_path = './', $sitemap_name = 'sitemap.xml', $priority = '0.500') {

		try{

			$this->config['url'] = $url;
			$this->config['working_path'] = $working_path;
			$this->config['sitemap_name'] = $sitemap_name;
			$this->config['sitemap_url'] = $this->config['url'].$this->config['sitemap_name'];
			$this->config['sitemap_path'] = $sitemap_path;
			$this->config['wgetlog_file'] = $this->config['working_path'].'wgetlog.txt';
			$this->config['sedlog_file'] = $this->config['working_path'].'sedlog.txt';
			$this->config['priority'] = $priority;

			// changing working path for exec() calls
			chdir($this->config['working_path']);
			
			$this->checkPermissions($this->config['sitemap_path']);
			$this->checkPermissions($this->config['working_path']);

		}
		catch(Exception $e){
			return $e->getMessage();
		}

	}

	public function deleteSitemap() {

		$r = unlink($this->config['sitemap_path'].$this->config['sitemap_name']);
		if($r && !$quiet) $this->log("Deleting  current sitemap : ".$url.$sitemap_name .".");

	}
	
	public function checkPermissions($folder) {
	
		if(!is_writable($folder)) die('Error: '.$folder.' is not writable. Please check permissions on given folder.');
	
	}

	public function exists() {

		return file_exists($this->config['sitemap_path'].$this->config['sitemap_name']);

	}

	public function getVar($varname) {

		return $this->config[$varname];

	}

	public function getConfig() {

		return $this->config;

	}

	public function getCounter() {

		return count($this->locations);

	}

	public function getLocations() {

		return $this->locations;

	}

	public function log($str) {

		$this->log .= "<".$this->logtag.">".$str."</".$this->logtag.">\n";

	}

	public function setLogTag($tag) {

		$this->logtag = $tag;

	}

	public function getLog() {

		return $this->log;

	}
	public function lastModified($format = '"m.d.y H:i') {

		if($this->exists($this->config['sitemap_path'].$this->config['sitemap_name'])) {

			return date($format, filemtime($this->config['sitemap_path'].$this->config['sitemap_name']));

		} else {

			return false;

		}

	}
	/**
	 * generate()
	 * Run the full script to generate sitemap
	 */
	public function generate() {

		$cmd   = "wget --spider --recursive --no-verbose -P sitemapCrawler --output-file=". $this->config['wgetlog_file'] ." ". $this->config['url'];
		exec($cmd);

		$this->log("Wget() - crawling website : ". $this->config['url'] .".");

		$cmd  = 'sed -n "s@.\+ URL:\([^ ]\+\) .\+@\1@p" wgetlog.txt | sed "s@&@\&amp;@" > ' . $this->config['sedlog_file'];
		exec($cmd);

		$this->log("Sed() - Retrieving urls from : ". $this->config['url'] .".");

		$this->setLocations();
		$this->saveFile();
		$this->clean();

		return true;

	}

	/**
	 * setLocations()
	 * Convert sed log file to array
	 * and populate $this->locations attributes
	 */
	public function setLocations() {

		// convert file to PHP array
		$lines = file($this->config['sedlog_file'], FILE_IGNORE_NEW_LINES);

		// we remove duplicates
		foreach($lines as $line)
		{
			if(!in_array($line, $this->locations)) {
				array_push($this->locations, $this->formatUrl($line));
				$this->log("Adding entry : '".$this->formatUrl($line) ."'.");
			} else {
				$this->log("Removing duplicate entry : '".$this->formatUrl($line)."'.");
			}
		}

	}

	/**
	 * saveFile()
	 * generate xml sitemap file
	 */
	public function saveFile() {
		// finally we create the sitemap file
		if($fp = fopen($this->config['sitemap_path'].$this->config['sitemap_name'], "w+")) {

			$out = '<?xml version="1.0" encoding="UTF-8"?>
			<urlset
			xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
			xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
			xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
			http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">\n';

			foreach ($this->locations as $loc) {
				$out .= "<url>\n\t<loc>".$loc."</loc>\n\t<priority>".$this->config['priority']."</priority>\n</url>\n";
			}
			$out .= '</urlset>';

			fputs($fp, $out);
			fclose($fp);

			$this->log("Sitemap has been written to '".$this->config['sitemap_path'].$this->config['sitemap_name']."'.");
		} else {
			$this->log("Error: cannot save '".$this->config['sitemap_path'].$this->config['sitemap_name']."' file.");
		}
	}

	/**
	 * clean()
	 * delete all temp files
	 */
	public function clean() {

		unlink($this->config['wgetlog_file']);
		$this->log("Deleting  file : ".$this->config['wgetlog_file'].".");

		unlink($this->config['sedlog_file']);
		$this->log("Deleting  file : ".$this->config['sedlog_file'].".");

		$this->sureRemoveDir($this->config['working_path'].'sitemapCrawler', true);
		$this->log("Deleting  wget() folder : ".$this->config['working_path'] .'sitemapCrawler'.".");

	}

	private function formatUrl($url) {
		
		$url = str_replace('&amp;', '&', $url); // necessary if some '&' are already '&amp' encoded
		$url = str_replace('&', '&amp;', $url);
		
		return $url;
		
	}

	/**
	 * SureRemoveDir()
	 * Recursive delete directories
	 */
	private function sureRemoveDir($dir, $DeleteMe) {

		if(!$dh = @opendir($dir)) return;
		while (($obj = readdir($dh))) {
			if($obj=='.' || $obj=='..') continue;
			if (!@unlink($dir.'/'.$obj)) sureRemoveDir($dir.'/'.$obj, true);
		}
		if ($DeleteMe){
			closedir($dh);
			@rmdir($dir);
		}

	}

}

?>