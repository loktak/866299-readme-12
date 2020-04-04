<?php 
$posts = [
  [
      'type' => 'post-quote',
      'title' => 'Цитата',
      'post_content' => 'Мы в жизни любим только раз, а после ищем лишь похожих',
      'author' => 'Лариса',
      'avatar' => 'userpic-larisa-small.jpg'
  ],
  [
      'type' => 'post-text',
      'title' => 'Игра престолов',
      'post_content' => 'Не могу дождаться начала финального сезона своего любимого сериала!',
      'author' => 'Владик',
      'avatar' => 'userpic.jpg'
  ],
  [
      'type' => 'post-photo',
      'title' => 'Наконец, обработал фотки!',
      'post_content' => 'rock-medium.jpg',
      'author' => 'Виктор',
      'avatar' => 'userpic-mark.jpg'
  ],
  [
      'type' => 'post-photo',
      'title' => 'Моя мечта',
      'post_content' => 'coast-medium.jpg',
      'author' => 'Лариса',
      'avatar' => 'userpic-larisa-small.jpg'
  ],
  [
      'type' => 'post-link',
      'title' => 'Лучшие курсы',
      'post_content' => 'www.htmlacademy.ru',
      'author' => 'Владик',
      'avatar' => 'userpic.jpg'
  ],
  [
      'type' => 'post-text',
      'title' => 'Пишем первую функцию',
      'post_content' => 'Чтобы карточки оставались компактными и не занимали слишком много места размер содержимого надо принудительно ограничивать. Для фотографий и видео это можно сделать через CSS, для цитат и ссылок есть ограничение длины при создании поста. Остаётся текстовый контент. Его длина никак не ограничивается в момент создания, а т. к. пользователи могут писать очень длинные тексты, необходимо предусмотреть обрезание текста до приемлемой длины при показе карточки поста на странице популярного.',
      'author' => 'Владик',
      'avatar' => 'userpic.jpg'
  ]
];

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
* @author Arseny Spirin <spirinars@ya.ru>
*/
function sut($user_content) {
  $safe_content = htmlspecialchars($user_content);

  return $safe_content;
}
?>
<div class="container">
        <h1 class="page__title page__title--popular">Популярное</h1>
    </div>
    <div class="popular container">
        <div class="popular__filters-wrapper">
            <div class="popular__sorting sorting">
                <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
                <ul class="popular__sorting-list sorting__list">
                    <li class="sorting__item sorting__item--popular">
                        <a class="sorting__link sorting__link--active" href="#">
                            <span>Популярность</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link" href="#">
                            <span>Лайки</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link" href="#">
                            <span>Дата</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="popular__filters filters">
                <b class="popular__filters-caption filters__caption">Тип контента:</b>
                <ul class="popular__filters-list filters__list">
                    <li class="popular__filters-item popular__filters-item--all filters__item filters__item--all">
                        <a class="filters__button filters__button--ellipse filters__button--all filters__button--active" href="#">
                            <span>Все</span>
                        </a>
                    </li>
                    <li class="popular__filters-item filters__item">
                        <a class="filters__button filters__button--photo button" href="#">
                            <span class="visually-hidden">Фото</span>
                            <svg class="filters__icon" width="22" height="18">
                                <use xlink:href="#icon-filter-photo"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="popular__filters-item filters__item">
                        <a class="filters__button filters__button--video button" href="#">
                            <span class="visually-hidden">Видео</span>
                            <svg class="filters__icon" width="24" height="16">
                                <use xlink:href="#icon-filter-video"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="popular__filters-item filters__item">
                        <a class="filters__button filters__button--text button" href="#">
                            <span class="visually-hidden">Текст</span>
                            <svg class="filters__icon" width="20" height="21">
                                <use xlink:href="#icon-filter-text"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="popular__filters-item filters__item">
                        <a class="filters__button filters__button--quote button" href="#">
                            <span class="visually-hidden">Цитата</span>
                            <svg class="filters__icon" width="21" height="20">
                                <use xlink:href="#icon-filter-quote"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="popular__filters-item filters__item">
                        <a class="filters__button filters__button--link button" href="#">
                            <span class="visually-hidden">Ссылка</span>
                            <svg class="filters__icon" width="21" height="18">
                                <use xlink:href="#icon-filter-link"></use>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="popular__posts">
            <?php foreach ($posts as $post): ?>                
            <article class="popular__post post <?=$post['type'] ?>">
                <header class="post__header">
                    <h2><?=$post['title'] ?></h2>
                </header>
                <div class="post__main">
                    <?php if ($post['type'] === 'post-quote' ): ?>
                    <blockquote>
                    <p>
                        <?=sut($post['post_content']) ?>
                    </p>
                    <cite>Неизвестный Автор</cite>
                    </blockquote>
                    <?php elseif ($post['type'] === 'post-photo'): ?>
                    <div class="post-photo__image-wrapper">
                    <img src="img/<?=sut($post['post_content']) ?>" alt="Фото от пользователя" width="360" height="240">
                    </div>
                    <?php elseif ($post['type'] === 'post-link' ): ?>
                    <div class="post-link__wrapper">
                    <a class="post-link__external" href="http://" title="Перейти по ссылке">
                        <div class="post-link__info-wrapper">
                            <div class="post-link__icon-wrapper">
                                <img src="https://www.google.com/s2/favicons?domain=vitadental.ru" alt="Иконка">
                            </div>
                            <div class="post-link__info">
                                <h3><?=$post['title'] ?></h3>
                            </div>
                        </div>
                        <span><?=sut($post['post_content']) ?></span>
                    </a>
                </div>
                <?php else: ?>
                    <?php crop_text(sut($post['post_content'])) ?>
                <?php endif; ?>
                </div>
                <footer class="post__footer">
                    <div class="post__author">
                        <a class="post__author-link" href="#" title="Автор">
                            <div class="post__avatar-wrapper">
                                <!--укажите путь к файлу аватара-->
                                <img class="post__author-avatar" src="img/<?=$post['avatar'] ?>" alt="Аватар пользователя">
                            </div>
                            <div class="post__info">
                                <b class="post__author-name"><?=sut($post['author']) ?></b>
                                <time class="post__time" datetime="">дата</time>
                            </div>
                        </a>
                    </div>
                    <div class="post__indicators">
                        <div class="post__buttons">
                            <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                                <svg class="post__indicator-icon" width="20" height="17">
                                    <use xlink:href="#icon-heart"></use>
                                </svg>
                                <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                                    <use xlink:href="#icon-heart-active"></use>
                                </svg>
                                <span>0</span>
                                <span class="visually-hidden">количество лайков</span>
                            </a>
                            <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-comment"></use>
                                </svg>
                                <span>0</span>
                                <span class="visually-hidden">количество комментариев</span>
                            </a>
                        </div>
                    </div>
                </footer>
            </article>
            <?php endforeach; ?>
        </div>
    </div>