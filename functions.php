<?php

function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!file_exists($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

/** 
*The function cuts the text and adds a link to the full text if required 

*The function takes two values: text ($text) and the maximum number of symbols($symbols).
*1.Splitting the text into separate words and entering them in the $words array
*   The "strlen" function counts the number of characters in each word and sums them in the variable $text_length
*   Each calculated value is added to the $cropped_text array
*   The loop stops working if $text_length >= $symbols
*2.Using the "implode" function, we get the cropped text from the $cropping_text array and write it to the $text variable
*3.The $post_text variable is responsible for displaying text in HTML code. In it, we know the tags and the actual text itself from the $text variable
*4.The $post_full_text_link variable is responsible for displaying a link to the full text, if it was cropped. We enter the html code of the link in it
*5 Entering a condition
*   If the value of $text_length is greater than $symbols, add a colon at the end of the line and a link from $post_full_text_link to the entire text.
*   If the value of $text_length is less than $symbols, just print the $post_text variable


* @param string $text
* @param int $symbols
* @return string
* @author Arseny Spirin <spirinars@ya.ru>
*/
function crop_text( $text, $symbols = 300) {     
  
  $words = explode(" ", $text);  
  
  $text_lenght = 0;
  
  foreach ($words as $word) {
      $text_lenght = $text_lenght + strlen($word);
      $cropped_text[] = $word; // изначально я так и писал, но по какой-то причине, выводилось в массиве только последнее слово из всего текста. Вероятно ошибка была где-то еще.
      if ($text_lenght >= $symbols) {
          break;
      }  
  };

  $text = implode(" ", $cropped_text);
  
  $post_text = "<p>". $text. "</p>";

  if ($text_lenght > $symbols) {
      $text .= "...";
      $post_full_text_link = '<a class="post-text__more-link" "href="#">Читать далее</a>';
      $post_text = "<p>". $text. "</p>". $post_full_text_link;
      print($post_text);
  }
  else {
      print($post_text);
  }
}

/** 
*This function replaces special characters with mnemonic characters 
*
* @param string $user_content
* @return string
* @author Arseny Spirin <spirinars@ya.ru>
*/
function anti_xss($user_content) {
  return htmlspecialchars($user_content, ENT_QUOTES);
}