<?php
	namespace Fkr\CssURLRewriteBundle\Assetic\Filter;

	use Assetic\Asset\AssetInterface;
	use Assetic\Filter\FilterInterface;


	class CssURLRewriteFilter implements FilterInterface {
		
		public function filterLoad(AssetInterface $asset) {
		}
		
		public function filterDump(AssetInterface $asset) {
			global $bundlePath;
			$bundlePath = $this->__calculateBundlePath($asset);
			
			$content = $asset->getContent();
			$content = preg_replace_callback('|(url)\((["\']?)(.+)\)|i', function($matches) {
				global $bundlePath;
				return $matches[1].'('.$matches[2].$bundlePath.'/'.$matches[3].')';
			}, $content);
			
			$asset->setContent($content);
		}
		
		private function __calculateBundlePath(AssetInterface $asset) {
			$path = dirname($asset->getSourcePath());
			$path = substr($path, strpos($path, '/public/')+8);
			return $this->__calculateSwitchPath($asset).$this->__calculateBundleName($asset).'/'.$path;
		}
		
		private function __calculateBundleName(AssetInterface $asset) {
			$routePathSplitted = explode('/', $asset->getSourceRoot());
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
		
		private function __calculateSwitchPath(AssetInterface $asset) {
			$targetPath = dirname($asset->getTargetPath());
			$numDirs = substr_count($targetPath, '/')+1;
			
			$output = '';
			for($i=0; $i<$numDirs; $i++) {
				$output .= '../';
			}
			
			return $output;
		}

	}
