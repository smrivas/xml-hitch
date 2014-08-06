<?php

/**
 * This file is part of the Hitch Demo package
 *
 * (c) Marc Roulias <marc@lampjunkie.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// include the demo class loader
//include 'ClassLoader.php';

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;

use Hitch\HitchManager;
use Hitch\Mapping\ClassMetadataFactory;
use Hitch\Mapping\Loader\AnnotationLoader;

// Composer autoloader
$loader = include __DIR__ . '/../vendor/autoload.php';

$loader->setUseIncludePath(true);

// set path to doctrine-common lib
$VENDOR_LIB = __DIR__.'/../vendor/';
$DOCTRINE_COMMON_LIB = $VENDOR_LIB.'doctrine/common/lib';

// make sure doctrine-common exists
if(!is_dir($DOCTRINE_COMMON_LIB)){
  die('<span style="color: red;">Make sure to download and install the doctrine-common (<a href="https://github.com/doctrine/common">https://github.com/doctrine/common</a>) library to: ' . $DOCTRINE_COMMON_LIB . ' !!!</span>');
}

set_include_path(
    '.' .
    PATH_SEPARATOR . $VENDOR_LIB .
    PATH_SEPARATOR . $DOCTRINE_COMMON_LIB .
    PATH_SEPARATOR . get_include_path()
);

// create our new HitchManager
$hitch = new HitchManager();
$hitch->setClassMetaDataFactory(
    new ClassMetadataFactory(
        new AnnotationLoader(new AnnotationReader()),
        new ArrayCache()
    )
);

// pre-build the class meta data cache
$hitch->registerRootClass('Hitch\\Demo\\Entity\\Catalog');
$hitch->buildClassMetaDatas();

// load XML file to parse
$xml = file_get_contents("demo.xml");

// parse the xml into a Catalog object
$catalog = $hitch->unmarshall($xml, 'Hitch\\Demo\\Entity\\Catalog');

// print general information
echo "Version: " . $catalog->getMeta()->getVersion() . "<br />";
echo "Category: " . $catalog->getMeta()->getCategory() . "<br />";
echo "Response Time: " . $catalog->getMeta()->getResponseTime() . "<br />";
echo "Num Products: " . $catalog->getNumProducts() . "<br />";
echo "Total Products: " . $catalog->getTotalProducts() . "<br />";
echo '<hr />';

// display all the products
foreach($catalog->getProducts() as $product){

  echo '<h2>' . $product->getName() . ' - $' . $product->getPrice() . ' ' . $product->getUrl() . '</h2>';
  echo '<p>' . $product->getDescription() . '</p>';

  if(count($product->getComments()) > 0){

    echo '<h3>Comments:</h3>';

    foreach($product->getComments() as $comment){
      echo $comment->getUser() . ' - ' . $comment->getValue() . '<br />';
    }
  }

  if(count($product->getRatings()) > 0){

    echo "<h3>Ratings:</h3>";

    foreach($product->getRatings() as $rating){
      echo $rating->getType() . ' - ' . $rating->getValue() . '<br />';
    }
  }

  echo '<hr />';
}