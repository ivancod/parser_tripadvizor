<?php
ob_start();
include "../params/connect.php";
include "../params/function.php";
include "../config.php";

include 'phpQuery.php';

function curl($url, $element, $client_id, $type, $obj){
  if(strpos($url, "Hotel_Review") != false) {
    $curl = curl_init($url); // Инициализируем curl по указанному адресуЫЫ
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // Записать http ответ в переменную, а не выводить в буфер
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // Этот параметр нужен для работы HTTPS
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // Этот параметр нужен для работы HTTPS
    $page = curl_exec($curl); // Получаем в переменную $page HTML код страницы
    $document = phpQuery::newDocument($page); // Загружаем полученную страницу в phpQuery
    $pagination = $document->find('.pageNum'); 

    $elements = $document->find($element); // Находим все ссылки с классом ".blog-title a" 
    foreach ($elements as $el) {
      $elem_pq = pq($el); // pq - аналог $ в jQuery
      $url = 'https://www.tripadvisor.ru'.$elem_pq->find('.hotels-review-list-parts-ReviewTitle__reviewTitleText--3QrTy')->attr('href');

      $curl = curl_init($url); // Инициализируем curl по указанному адресу
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // Записать http ответ в переменную, а не выводить в буфер
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // Этот параметр нужен для работы HTTPS
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // Этот параметр нужен для работы HTTPS
      $page = curl_exec($curl); // Получаем в переменную $page HTML код страницы
      $document = phpQuery::newDocument($page); // Загружаем полученную страницу в phpQuery
      $date = $document->find('.ratingDate');
      if(db_array(mysql_query("SELECT * FROM `".PREFIX."global_reviews` WHERE `url` = '".link_read_tripadvisor(pq($el), '.hotels-review-list-parts-ReviewTitle__reviewTitleText--3QrTy')."'"))) break;
      read_tripadvisor2($elem_pq, $client_id, $type, $obj, $document); 
    }
     
    $pag;
    foreach ($pagination as $el) $pag = pq($el)->attr('href'); // pq - аналог $ в jQuery
    $pos = strpos($pag, "-Reviews-or");
    $count = strlen($pag);
    $str = substr($pag, $pos+11, $count); 
    $pos2 = strpos($str, "-");
    $str2 = substr($str, 0, $pos2) + 1;
    $l = substr($pag, 0, $pos);
    $r = substr($pag, $pos+12+$pos2, $count);
    for($i = 5; $i <= $str2; $i++){
      if(($i % 5) == false){
        $url  = $l.'-Reviews-or'.$i.'-'.$r;
        $curl = curl_init('https://www.tripadvisor.ru'.$url); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // Записать http ответ в переменную, а не выводить в буфер
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // Этот параметр нужен для работы HTTPS
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // Этот параметр нужен для работы HTTPS
        $page = curl_exec($curl); // Получаем в переменную $page HTML код страницы
        $document = phpQuery::newDocument($page); // Загружаем полученную страницу в phpQuery
        $elements = $document->find($element); // Находим все ссылки с классом ".blog-title a" 
        foreach ($elements as $el) {
          $elem_pq = pq($el); // pq - аналог $ в jQuery

          $url = 'https://www.tripadvisor.ru'.$elem_pq->find('.hotels-review-list-parts-ReviewTitle__reviewTitleText--3QrTy')->attr('href');
          $curl = curl_init($url); // Инициализируем curl по указанному адресу
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // Записать http ответ в переменную, а не выводить в буфер
          $page = curl_exec($curl); // Получаем в переменную $page HTML код страницы
          $document = phpQuery::newDocument($page); // Загружаем полученную страницу в phpQuery
           if(db_array(mysql_query("SELECT * FROM `".PREFIX."global_reviews` WHERE `url` = '".link_read_tripadvisor(pq($el), '.hotels-review-list-parts-ReviewTitle__reviewTitleText--3QrTy')."'"))) break;
          read_tripadvisor2($elem_pq, $client_id, $type, $obj, $document); 
        }
      }
    }
  } else {
    $curl = curl_init($url); // Инициализируем curl по указанному адресу
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // Записать http ответ в переменную, а не выводить в буфер
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // Этот параметр нужен для работы HTTPS
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // Этот параметр нужен для работы HTTPS
    $page = curl_exec($curl); // Получаем в переменную $page HTML код страницы
    $document = phpQuery::newDocument($page); // Загружаем полученную страницу в phpQuery
    $pagination = $document->find('.pageNum'); 

    $elements = $document->find($element); // Находим все ссылки с классом ".blog-title a"

    foreach ($elements as $el) 
    {
      if(db_array(mysql_query("SELECT * FROM `".PREFIX."global_reviews` WHERE `url` = '".link_read_tripadvisor(pq($el), '.quote a')."'"))) break;
      read_tripadvisor1(pq($el), $client_id, $type, $obj);
    }
    
    $pag;
    foreach ($pagination as $el) $pag = pq($el)->attr('href'); // pq - аналог $ в jQuery
    $pos = strpos($pag, "-Reviews-or");
    $count = strlen($pag);
    $str = substr($pag, $pos+11, $count); 
    $pos2 = strpos($str, "-");
    $str2 = substr($str, 0, $pos2) + 1;
    $l = substr($pag, 0, $pos);
    $r = substr($pag, $pos+12+$pos2, $count);
    for($i = 5; $i <= $str2; $i++){
      if(($i % 5) == false){
        $url  = $l.'-Reviews-or'.$i.'-'.$r;
        $curl = curl_init('https://www.tripadvisor.ru'.$url); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); 
        $page = curl_exec($curl); 
        $document = phpQuery::newDocument($page); 
        $elem = $document->find($element);

        foreach($elem as $el) 
        {
          if(db_array(mysql_query("SELECT * FROM `".PREFIX."global_reviews` WHERE `url` = '".link_read_tripadvisor(pq($el), '.quote a')."'"))) break;
          read_tripadvisor1(pq($el), $client_id, $type, $obj);
        }
      }
    }
  }
}


###


function read_tripadvisor($review, $element){
  return $review->find($element)->text();
}
function link_read_tripadvisor($review, $element){
  return 'https://www.tripadvisor.ru'.$review->find($element)->attr('href');
}
function date_read_tripadvisor($review, $element){
  $date;
  $bad_date = str_replace(' г.', '', $review->find($element)->attr('title'));
  $bad_date = str_replace(' ', '.', $bad_date);
  $rus_months = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
  foreach($rus_months as $key => $value) {
    if(strpos($bad_date, $value) != false) {
      $key++;
      $y = $bad_date[strlen($bad_date) - 4].$bad_date[strlen($bad_date) - 3].$bad_date[strlen($bad_date) - 2].$bad_date[strlen($bad_date) - 1];
      $d;
      if(strpos($bad_date, ".") == 2) { $d = $bad_date[0].$bad_date[1]; } else { $d = '0'.$bad_date[0]; }
      if($key < 10) {
        $date = $d.'.0'.$key.'.'.$y;
      } else {
        $date = $d.'.'.$key.'.'.$y;
      }
    }
  }
  $date = strtotime($date);
  return $date;
}
function photo_read_tripadvisor($review, $element){
  $str = str_replace('/photo-l/', '/photo-o/', $review->find($element));
  if(preg_match_all('/https(.*?)jpg/',$str,$matches)) 
  {
    $photos = '';
    foreach ($matches[0] as $p) $photos .= ', '.$p;
    return substr($photos, 2, strlen($photos));
  }
}
function rating_read_tripadvisor($review, $element){
  return preg_replace('~[^0-9]+~', '', $review->find($element));
}
function writeDB_tripadvisor($user, $title, $link, $text, $date, $photo, $rating, $client_id, $type, $obj, $date_update){
  mysql_query("INSERT INTO `".PREFIX."global_reviews` (`client_id`,   `type`,    `url`,    `user`,    `title_review`, `date`,    `rating`,   `text`,     `photo`,    `object`, `date_update`) 
                                               VALUES (".$client_id.",".$type.",'".$link."','".$user."','".$title."','".$date."',".$rating.",'".$text."','".$photo."','".$obj."','".$date_update."')");
}

function read_tripadvisor1($review, $client_id, $type, $obj){
  $user  = read_tripadvisor($review, '.info_text div:first-child');
  $title = read_tripadvisor($review, '.noQuotes');
  $link  = link_read_tripadvisor($review, '.quote a');
  $text  = read_tripadvisor($review, '.partial_entry');
  if(strripos($text, 'Еще') == strlen($text) - 6) $text = substr($text, 0, strlen($text) - 6); 
  $date  = date_read_tripadvisor($review, '.ratingDate');
  $photo = photo_read_tripadvisor($review, '.imgWrap .noscript');
  $rating = rating_read_tripadvisor($review, '.ui_bubble_rating');
  $date_update = strtotime(date('Y-m-d'));
  // запросы в базу 
  writeDB_tripadvisor($user, $title, $link, $text, $date, $photo, $rating, $client_id, $type, $obj, $date_update);
}


function read_tripadvisor2($review, $client_id, $type, $obj, $date){
  $user  = read_tripadvisor($review, '.ui_header_link');
  $title = read_tripadvisor($review, '.hotels-review-list-parts-ReviewTitle__reviewTitleText--3QrTy');
  $link  = link_read_tripadvisor($review, '.hotels-review-list-parts-ReviewTitle__reviewTitleText--3QrTy');
  $text  = read_tripadvisor($review, '.hotels-review-list-parts-ExpandableReview__reviewText--3oMkH');
  if(strripos($text, 'Еще') == strlen($text) - 6) $text = substr($text, 0, strlen($text) - 6); 
  $date  = date_read_tripadvisor($date, '.ratingDate');
  $photo = photo_read_tripadvisor($review, '.media-image-ResponsiveImage__default--1s-9x');
  $rating = rating_read_tripadvisor($review, '.ui_bubble_rating');
  $date_update = strtotime(date('Y-m-d'));
  // запросы в базу 
  writeDB_tripadvisor($user, $title, $link, $text, $date, $photo, $rating, $client_id, $type, $obj, $date_update);
}


$links = db_array(mysql_query("SELECT * FROM `".PREFIX."clients_to_g-links` WHERE `client_id` = ".$CLIENT_ID));
if($links)
{
  foreach ($links as $link)
  {
    if((strpos($link['url'], "Restaurant_Review") != false) OR 
       (strpos($link['url'], "Attraction_Review") != false))  curl($link['url'], '.rev_wrap', $link['client_id'], 0, $link['rev_id']);

    if(strpos($link['url'], "Hotel_Review") != false) curl($link['url'], '.hotels-community-tab-common-Card__section--4r93H', $link['client_id'], 0, $link['rev_id']);
  }
}

// mysql_query("INSERT INTO `".PREFIX."clients_to_g-links` (`client_id`, `url`, `site`, `rev_id`) 
//                                                  VALUES (".$_GET['client_id'].",'".$_GET['url']."','www.tripadvisor.ru','".$_GET['obj']."')");
