<?php

require_once ("simple_html_dom.php");

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

function curlGet($url, $referer = 'http://www.google.com')
{
        $output = curl_init();	//подключаем курл
        curl_setopt($output, CURLOPT_URL, $url);	//отправляем адрес страницы
        curl_setopt($output, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($output, CURLOPT_HEADER, 0);
        curl_setopt($output, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        $out = '';
        $out .= curl_exec($output);		//помещаем html-контент в строку
        curl_close($output);	//закрываем подключение
    return $out;
}

$html = curlGet("https://www.octava.com.ua");

/*
    $mainarray = [название категории[названия подкатегорий[продукция название => цена], ...], ...]
*/


function parsMainHref($link, $divName)
{

    $html = curlGet($link);
    $dom = str_get_html($html);

    $linkToContent = $dom->find($divName, 0);
    if (is_null($linkToContent)){
        echo "PIDOR!<br />";
    }
    $arrayChildren = $linkToContent->children();
    if ($arrayChildren == NULL){
        echo "PIDOR!!!!!<br />";
    }
    foreach ($arrayChildren as $children) {
        $link = $children->find('a');

        $childrenHref[] = $link[0]->href;
    }
    return $childrenHref;
}

function parsAllCategoriesLink($arrayLink, $divName)
{

        $resArray = [];
        foreach($arrayLink as $link) {
            $links = parsMainHref($link, $divName);
            array_push($resArray, ...$links);
        }
        return $resArray;
}

$mainLinks = parsMainHref('https://www.octava.com.ua', 'div.box-category-top');
array_pop($mainLinks);

$allLinks = parsAllCategoriesLink($mainLinks, 'div.box-subcategory');
var_dump($allLinks);

function namePriseProduct($lastLink) // ПАрсит
{
    $html = curlGet($lastLink);
    $lastContent = str_get_html($html);

    $divName = $lastContent->find('div.product-list div.name');
    $divCost = $lastContent->find('div.product-list div.price');
    $div_product = $lastContent->find('div.product-list', 0);
    $divArray = $div_product->children();

    foreach($divArray as $product)
    {
        $res = $product->children(2);
        $strWithoutChars = preg_replace('/[^0-9]/', '', $res->plaintext);
        $resArrayCost[] = $strWithoutChars;

        $res = $product->children(1);
        $resArrayName[] = $res->plaintext;
        $resArray = array_combine($resArrayName, $resArrayCost);
    }
    return $resArray;
}

function allNamePriseProduct($allLinks)
{
    $resArray = [];
    foreach ($allLinks as $link)
    {
        $priseName = namePriseProduct($link);
        $resArray = array_merge($resArray, $priseName);
    }
    return $resArray;
}


//$mainLinks = parsMainHref('https://www.octava.com.ua', 'div.box-category-top');
//array_pop($mainLinks);
//
//$allLinks = parsAllCategoriesLink($mainLinks, 'div.box-subcategory');
//$pidor = allNamePriseProduct($allLinks);

//var_dump($pidor);
//$test = mainInformation("https://www.octava.com.ua/akusticheskie-fortepiano-pianino-i-royali/akusticheskoe-pianino");
//var_dump($test);
//$mainLincs = parsMainHref("https://www.octava.com.ua", 'div.box-category-top');
//$arrlinks = parsAllCategories($mainLincs, 'caticons-wrapper');
/*

$html = curlGet($linkToContent->href);
$selections = str_get_html($html);

$linkToContentNext = $selections->find('div.caticons-wrapper a', 0);

$lastLink = $linkToContentNext->href;

function mainInformation($lastLink)
{
    $html = curlGet($lastLink);
    $lastContent = str_get_html($html);

    $divName = $lastContent->find('div.product-list div.name');
    $divCost = $lastContent->find('div.product-list div.price');
    $div_product = $lastContent->find('div.product-list', 0);
    $divArray = $div_product->children();

    foreach($divArray as $product)
    {
        $res = $product->children(2);
        $strWithoutChars = preg_replace('/[^0-9]/', '', $res->plaintext);
        $resArrayCost[] = $strWithoutChars;

        $res = $product->children(1);
        $resArrayName[] = $res->plaintext;
        $resArray = array_combine($resArrayName, $resArrayCost);
    }
    return $resArray;
}

var_dump(mainInformation($lastLink));
//var_dump($resArray);
//echo gettype($divArray);
//
//echo count($divArray);
//$i = 0;
//foreach ($divName as $poh)
//{
//    $i++;
//    $arrName[] = $poh->plaintext;
//}
//$j = 0;
//foreach ($divCost as $poh)
//{
//    echo "<br /> test <br />";
//    $j++;
//    $arrCost[] = $poh->plaintext;
//}
//
//echo $i . "----" . $j . "<br />";
//$nameOfProduct = $lastContent->find('div[class=name] a', 0);
//$nameOfProduct = $nameOfProduct->plaintext;
//
//
//
//
//
//
//$prise = $lastContent->find('.price', 0);
//$prise = $prise->plaintext;
//echo $nameOfProduct . "----" . $prise . "<br />";
//$nextDom = $link->href;

//$courses = $dom->find(".courses-list--item-body--price");
//$i = 0;
//foreach($courses as $course) {
//    echo $course->plaintext;
//    $index = $dom->find(".courses-list--item-body--title");
//    echo $index[$i]->plaintext . "---$i---" ."<br>";
//    $i++;
//}
