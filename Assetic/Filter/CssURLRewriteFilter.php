<?php
namespace Fkr\CssURLRewriteBundle\Assetic\Filter;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\Exception\InactiveScopeException;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

class CssURLRewriteFilter implements FilterInterface
{
    private $rewriteOnlyIfFileExists;
    private $clearUrls;
    private $kernel;

    private $asset;

    public function __construct($rewriteOnlyIfFileExists, $clearUrls, KernelInterface $kernel)
    {
        $this->rewriteOnlyIfFileExists = $rewriteOnlyIfFileExists;
        $this->clearUrls = $clearUrls;
        $this->kernel = $kernel;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $this->asset = $asset;
        $bundlePath = $this->calculateBundlePath();
        if ($bundlePath === null) {
            return;
        }

        $that = $this;

        $content = $asset->getContent();
        $content = preg_replace_callback('|(url)\((["\']?)([^\)]+)\)|i', function($matches) use ($that, $bundlePath) {
            $tmpPath = $that->checkForBundleLinking($matches[3]);
            if ($tmpPath != null) {
                return $matches[1].'('.$matches[2].$tmpPath.')';
            } elseif (!$that->checkPath($matches[3])) {
                return $matches[1].'('.$matches[2].$matches[3].')';
            }

            return $matches[1].'('.$matches[2].$that->normalizeUrl($bundlePath.'/'.$matches[3]).')';
        }, $content);
        $asset->setContent($content);
    }

    public function checkForBundleLinking($path)
    {
        if (substr($path, 0, 1) == '@') {
            $findChar = strpos($path, '/');
            $bundleName = substr($path, 1, $findChar-1);
            try {
                $bundle = $this->kernel->getBundle($bundleName);

                return $this->calculateSwitchPath().'bundles/'.str_replace('_', '', $bundle->getContainerExtension()->getAlias()).substr($path, $findChar);
            } catch (\InvalidArgumentException $e) {
            }
        }

        return null;
    }

    private function calculateBundlePath()
    {
        foreach ($this->kernel->getBundles() as $bundle) {
            if (strstr($this->asset->getSourceRoot(), $bundle->getPath()) !== false) {
                if ($bundle->getContainerExtension()) {
                    return $this->calculateSwitchPath().'bundles/'.str_replace('_', '', $bundle->getContainerExtension()->getAlias()).'/'.$this->calculatePathSuffix();
                }
            }
        }

        return null;
    }

    private function calculatePathSuffix()
    {
        $sourcePath = dirname($this->asset->getSourcePath());

        return $this->getSubstringAfterNString($sourcePath, '/', 2);
    }

    private function getSubstringAfterNString($string, $searchString, $afterN)
    {
        $i=0;
        $pos = -1;
        while (($pos=strpos($string, $searchString, $pos+1)) !== false) {
            $i++;

            if ($i==$afterN) {
                return substr($string, $pos+1);
            }
        }

        return '';
    }

    private function calculateSwitchPath()
    {
        $targetPath = dirname($this->asset->getTargetPath());
        $numDirs = substr_count($targetPath, '/')+1;

        $output = '';
        for ($i=0; $i<$numDirs; $i++) {
            $output .= '../';
        }

        if (substr($targetPath, 0, 11) == '_controller') {
            try {
                $request = $this->kernel->getContainer()->get('request');
                if (substr($request->getBaseUrl(), -4) != '.php') {
                    $output = substr($output, 3);
                }
            } catch (InactiveScopeException $e) {
            }
        }

        return $output;
    }

    public function checkPath($url)
    {
        if (!$this->rewriteOnlyIfFileExists) {
            return true;
        }

        $lastChar = substr($url, -1);
        if ($lastChar=='"' || $lastChar=='\'') {
            $url = substr($url, 0, -1);
        }

        $basePath = $this->asset->getSourceRoot().'/'.dirname($this->asset->getSourcePath()).'/';
        return file_exists($basePath.$url) || file_exists($basePath.strtok($url, '?')) || file_exists($basePath.strtok($url, '#'));
    }

    public function normalizeUrl($url)
    {
        if (!$this->clearUrls) {
            return $url;
        }

        $pattern = '/\w+\/\.\.\//';
        while (preg_match($pattern, $url)) {
            $url = preg_replace($pattern, '', $url);
        }

        return $url;
    }
}
