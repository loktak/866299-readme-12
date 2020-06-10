USE readme;
/* 
Добавляем пользователей в таблицу с пользователями
Специально сделано разными строчками, я так учился. Дальше все пошло большими запросами.
*/
INSERT INTO users 
    (email, login, password, avatar)
VALUES
    ('larisa@yahoo.com', 'Лариса', 'qwerty12345', 'userpic-larisa-small.jpg'),
    ('pro100vlad@gmail.com', 'Владик', '01234567890', 'userpic.jpg'),
    ('vitek@yandex.ru', 'Виктор', 'superpass', 'userpic-mark.jpg');

/* 
Добавляем типы постов в таблицу с типами постов
*/
INSERT INTO content_type
    (id, type_name, icon_type)
VALUES
    (1, 'Картинка', 'photo'),
    (2, 'Видео', 'video'),
    (3, 'Текст', 'text'),
    (4, 'Цитата', 'quote'),
    (5, 'Ссылка', 'link');


/* 
Добавление всех существующих постов в таблицу с постами
*/
INSERT INTO posts
    (title, content_text, quote_author, views, user_id, type_id)
VALUES
    ('Цитата', 'Мы в жизни любим только раз, а после ищем лишь похожих', 'Неизвестный автор', 100500, 1, (SELECT id
        FROM content_type
        WHERE type_name = 'Цитата'));

INSERT INTO posts
    (title, img, views, user_id, type_id)
VALUES
    ('Наконец, обработал фотки!', 'rock-medium.jpg', 51, 3, (SELECT id
        FROM content_type
        WHERE type_name = 'Картинка')),
    ('Моя мечта', 'coast-medium.jpg', 560, 1, (SELECT id
        FROM content_type
        WHERE type_name = 'Картинка'));

INSERT INTO posts
    (title, content_text, views, user_id, type_id)
VALUES
    ('Игра престолов', 'Не могу дождаться начала финального сезона своего любимого сериала!', 55, 2, (SELECT id
        FROM content_type
        WHERE type_name = 'Текст'));

INSERT INTO posts
    (title, link, views, user_id, type_id)
VALUES
    ('Лучшие курсы', 'www.htmlacademy.ru', 1000000, 2, (SELECT id
        FROM content_type
        WHERE type_name = 'Ссылка'));

INSERT INTO posts
    (title, video, views, user_id, type_id)
VALUES
    ('Зацени видос', 'https://www.youtube.com/watch?v=eP-vjex0Wfw&list=PLQJNT2fdCJnhoGNGl-kIVbxiiyJRZOmZZ&index=11', 1200, 3, (SELECT id
        FROM content_type
        WHERE type_name = 'Видео'));

/* 
Добавление комментариев к постам
*/
INSERT INTO comments
    (content, user_id, post_id)
VALUES
    ('Так себе сериал, так и не смог себя заставить посмотреть', 4, 4),
    ('Согласен полностью с предыдущим оратором!', 4, 5),
    ('Надо будет попробовать', 1, 5);


/*
Получение списка постов с сортировкой по популярности и вместе с именами авторов и типом контента
*/
SELECT p.*, ct.icon_type
FROM posts p
    JOIN users u ON p.user_id = u.id
    JOIN content_type ct ON p.type_id = ct.id
ORDER BY p.views DESC;

/*
Получение списка постов для конкретного пользователся.
*/
SELECT *
FROM posts
WHERE user_id = 1;

/*
Получение списка комментариев для одного поста, в комментариях должен быть логин пользователя;
*/

SELECT c.*, u.login
FROM comments c
    JOIN users u ON c.user_id = u.id
WHERE c.post_id = 5;

/*
Добавление лайка к посту
*/

INSERT INTO likes
    (user_id, post_id)
VALUES
    (1, 5),
    (4, 5),
    (3, 5);

/*
Подписка на пользователя
*/
INSERT INTO subscriptions
    (user_id, userto_id)
VALUES
    (1, 4);

/*
Хэштег - дефолтный
*/
INSERT INTO hashtags
    (title)
VALUES 
    ('тестовый');

/*
Хэштег к постам по умолчанию
*/
INSERT INTO hashtags_posts
    (tag_id, post_id)
VALUES 
    (1, 1),
    (1, 2),
    (1, 3),
    (1, 4),
    (1, 5),
    (1, 6);