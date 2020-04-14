USE readme;
/* 
Добавляем пользователей в таблицу с пользователями
Специально сделано разными строчками, я так учился. Дальше все пошло большими запросами.
*/
INSERT INTO users
SET email = 'larisa@yahoo.com', login = 'Лариса', password = 'qwerty12345', avatar = 'userpic-larisa-small.jpg';
INSERT INTO users
SET email = 'pro100vlad@gmail.com', login = 'Владик', password = '01234567890', avatar = 'userpic.jpg';
INSERT INTO users
SET email = 'vitek@yandex.ru', login = 'Виктор', password = 'superpass', avatar = 'userpic-mark.jpg';
INSERT INTO users
SET email = 'spirinars@yandex.ru', login = 'Арсений', password = 'parol';

/* 
Добавляем типы постов в таблицу с типами постов
*/
INSERT INTO content_type (id, type_name, icon_type) 
VALUES
(1, 'Текст', 'text'),
(2, 'Цитата', 'quote'),
(3, 'Картинка', 'photo'),
(4, 'Видео', 'video'),
(5, 'Ссылка', 'link');


/* 
Добавление всех существующих постов в таблицу с постами
*/
INSERT INTO posts (title, content_text, quote_author, views, user_id, type_id)
VALUES 
('Цитата', 'Мы в жизни любим только раз, а после ищем лишь похожих', 'Неизвестный автор', 100500, (SELECT id FROM users WHERE login = 'Лариса'), (SELECT id FROM content_type WHERE type_name = 'Цитата'));

INSERT INTO posts (title, img, views, user_id, type_id)
VALUES 
('Наконец, обработал фотки!', 'rock-medium.jpg', 51, (SELECT id FROM users WHERE login = 'Виктор'), (SELECT id FROM content_type WHERE type_name = 'Картинка')),
('Моя мечта', 'coast-medium.jpg', 560, (SELECT id FROM users WHERE login = 'Лариса'), (SELECT id FROM content_type WHERE type_name = 'Картинка'));

INSERT INTO posts (title, content_text, views, user_id, type_id)
VALUES
('Игра престолов', 'Не могу дождаться начала финального сезона своего любимого сериала!', 55, (SELECT id FROM users WHERE login = 'Владик'), (SELECT id FROM content_type WHERE type_name = 'Текст'));

INSERT INTO posts (title, link, views, user_id, type_id)
VALUES
('Лучшие курсы', 'www.htmlacademy.ru', 1000000, (SELECT id FROM users WHERE login = 'Владик'), (SELECT id FROM content_type WHERE type_name = 'Ссылка'));

/* 
Добавление комментариев к постам
*/
INSERT INTO comments (content, user_id, post_id)
VALUES
('Так себе сериал, так и не смог себя заставить посмотреть', (SELECT id FROM users WHERE login = 'Арсений'), (SELECT id FROM posts WHERE id = 4)),
('Согласен полностью с предыдущим оратором!', (SELECT id FROM users WHERE login = 'Арсений'), (SELECT id FROM posts WHERE id = 5)),
('Надо будет попробовать', (SELECT id FROM users WHERE login = 'Лариса'), (SELECT id FROM posts WHERE id = 5)); /*сразу вопрос по post id, лучше искать по id или по контенту?*/


/*
Получение списка постов с сортировкой по популярности и вместе с именами авторов и типом контента
*/
SELECT p.id, p.views, p.post_date, u.login, ct.icon_type 
FROM posts p 
JOIN users u ON p.user_id = u.id
JOIN content_type ct ON p.type_id = ct.id
ORDER BY p.views DESC;

/*
Получение списка постов для конкретного пользователся.
*/
SELECT * FROM posts WHERE user_id = 1;

/*
Получение списка комментариев для одного поста, в комментариях должен быть логин пользователя;
*/

SELECT c.content, c.comment_date, u.login, p.id
FROM comments c
JOiN users u ON c.user_id = u.id
JOIN posts p ON c.post_id = p.id
WHERE p.id = 5;

/*
Добавление лайка к посту
*/

INSERT INTO likes (user_id, post_id)
VALUES ((SELECT user_id FROM posts WHERE id = 1), (SELECT id FROM posts WHERE id = 5));

/*
Подписка на пользователя
*/
INSERT INTO subscribtions (user_id, userto_id)
VALUES ((SELECT id FROM users WHERE login = 'Лариса'), (SELECT id FROM users WHERE login = 'Арсений'));