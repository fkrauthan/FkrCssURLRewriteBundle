<?php
	namespace Fkr\CssURLRewriteBundle\Assetic\Filter;

	use Assetic\Asset\AssetInterface;
	use Assetic\Filter\FilterInterface;


	class CssURLRewriteFilter implements FilterInterface {
		
		private $rewriteIfFileExists;
		private $asset;
		
		public function __construct($rewriteIfFileExists) {
			$this->rewriteIfFileExists = $rewriteIfFileExists;
		}
		
		public function filterLoad(AssetInterface $asset) {
		}
		
		public function filterDump(AssetInterface $asset) {
			$this->asset = $asset;
			
			$bundlePath = $this->__calculateBundlePath();
			$that = $this;
			
			$content = $asset->getContent();
			$content = preg_replace_callback('|(url)\((["\']?)(.+)\)|i', function($matches) use ($that, $bundlePath) {
				if(!$that->checkPath($matches[3])) {
					return $matches[1].'('.$matches[2].$matches[3].')';
				}
				
				return $matches[1].'('.$matches[2].$bundlePath.'/'.$matches[3].')';
			}, $content);
			
			$asset->setContent($content);
		}
		
		public function checkPath($url) {
			if(!$this->rewriteIfFileExists) {
				return true;
			}
			
			$lastChar = substr($url, -1);
			if($lastChar=='"' || $lastChar=='\'') {
				$url = substr($url, 0, -1);
			}
			return file_exists($this->asset->getSourceRoot().'/'.dirname($this->asset->getSourcePath()).'/'.$url);
		}
		
		private function calculateBundlePath() {
			$path = dirname($this->asset->getSourcePath());
			$path = substr($path, strpos($path, '/public/')+8);
			return $this->__calculateSwitchPath().$this->__calculateBundleName().'/'.$path;
		}
		
		private function calculateBundleName() {
			$routePathSplitted = explode('/', $this->asset->getSourceRoot());
			$numElements = count($routePathSplitted);
			$bundleName = strtolower(substr($routePathSplitted[$numElements-1], 0, strrpos($routePathSplitted[$numElements-1], 'Bundle')));
			$prefix = 'bundles/';
			
			if($numElements >= 5 && $routePathSplitted[$numElements-3]=='Bundle') {
				return $prefix.strtolower($routePathSplitted[$numElements-4]).strtolower($routePathSplitted[$numElements-2]).$bundleName;
			}
			else if($numElements >= 3 && $routePathSplitted[$numElements-2]=='Bundle') {
				return $prefix.strtolower($routePathSplitted[$numElements-3]).$bundleName;
			}
			else {
				return $prefix.strtolower($routePathSplitted[$numElements-2]).$bundleName;
			}
		}
		
		private function calculateSwitchPath() {
			$targetPath = dirname($this->asset->getTargetPath());
			$numDirs = substr_count($targetPath, '/')+1;
			
			$output = '';
			for($i=0; $i<$numDirs; $i++) {
				$output .= '../';
			}
			
			return $output;
		}

	}
