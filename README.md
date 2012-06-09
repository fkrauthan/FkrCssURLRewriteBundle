FkrCssURLRewriteBundle
======================

A small assetic filter for symfony to fix all url paths at css documents to correct urls


Installation
============

Bring in the vendor libraries
-----------------------------

This can be done in three different ways:

**Method #1**) Use composer

    "require": {
        "fkr/cssurlrewrite-bundle": "*"
    }


**Method #2**) Use git submodules

    git submodule add git://github.com/fkrauthan/FkrCssURLRewriteBundle.git vendor/bundles/Fkr/CssURLRewriteBundle


**Method #3**) Use deps file
	
	[FkrImagineBundle]
	    git=git://github.com/fkrauthan/FkrCssURLRewriteBundle.git
		target=bundles/Fkr/CssURLRewriteBundle


Register the Imagine and Fkr namespaces
-----------------------------------------
	
    // app/autoload.php
    $loader->registerNamespaces(array(
        'Fkr' => __DIR__.'/../vendor/bundles',
        // your other namespaces
    ));


Add SimplePieBundle to your application kernel
----------------------------------------------
	
	// app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Fkr\CssURLRewriteBundle\FkrCssURLRewriteBundle(),
            // ...
        );
    }


Usage
=====

If you place your css file for example  in 

	.../BundleFolder/Resources/public/css 

and you have your images in

	Resources/public/img

then you should write in your css file 

	background-image: url(../img/MyImageName.png)

Now you have to call
 
	app/console assets:install
	
Now if you have somthing like this in your template

	{% stylesheets filter='css_url_rewrite,?yui_css' 
		'@BundleName/Resources/public/css/mycssfile.css'
	%}
		<link rel="stylesheet" href="{{ asset_url }}" type="text/css" />
	{% endstylesheets %}

Now the filter rewrites your url in your css file

	background-image: url(../img/MyImageName.png) => background-image: url(../bundles/bundlename/css/../img/MyImageName.png)
	
And everything works fine.
