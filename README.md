FkrCssURLRewriteBundle
======================

A small assetic filter for Symfony2 to fix all url paths in css documents to correct urls


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
	
	[FkrCssURLRewriteBundle]
	    git=git://github.com/fkrauthan/FkrCssURLRewriteBundle.git
		target=bundles/Fkr/CssURLRewriteBundle


Register the Fkr namespaces
-----------------------------------------
	
    // app/autoload.php
    $loader->registerNamespaces(array(
        'Fkr' => __DIR__.'/../vendor/bundles',
        // your other namespaces
    ));


Add CssURLRewriteBundle to your application kernel
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


Configuration
=============

    # app/config.yml
    fkr_css_url_rewrite:
        rewrite_if_file_exists: true


* rewrite_if_file_exists: If true only rewrites url if the resource exists in the .../BundleFolder/Resources/public/ folder.


Usage
=====

**Standard usage**

If you place your css file for example in 

	.../BundleFolder/Resources/public/css 

and you have your images in

	../BundleFolder/Resources/public/img

then you have in yours css file somthing like this

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

**Extended usage**

You can link images from other bundles by using the @ annotation. For example if you write this in you css file

	background-image: url(@BundleNameBundle/img/MyImageName.png)

Now the filter rewrite this url in your css file

	background-image: url(@BundleNameBundle/img/MyImageName.png) => background-image: url(../bundles/bundlename/img/MyImageName.png)


Licence
=======

[Resources/meta/LICENSE](https://github.com/fkrauthan/FkrCssURLRewriteBundle/blob/master/Resources/meta/LICENSE)
