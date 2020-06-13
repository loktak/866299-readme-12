<h1 class="visually-hidden">Личные сообщения</h1>
<section class="messages tabs">
    <h2 class="visually-hidden">Сообщения</h2>
    <div class="messages__contacts">
        <ul class="messages__contacts-list tabs__list">
            <?php foreach ($interlocutors as $interlocutor): ?>
                <li class="messages__contacts-item">
                    <a class="messages__contacts-tab tabs__item <?= ((int)$receiver_id === (int)$interlocutor['receiver_id'] || (int)$receiver_id === (int)$interlocutor['sender_id']) ? 'messages__contacts-tab--active' : "" ?> "
                       href="messages.php?receiver_id=<?= ((int)$interlocutor['sender_id'] === (int)$user_data['id']) ? $interlocutor['receiver_id'] : $interlocutor['sender_id'] ?>#message_anchor">
                        <div class="messages__avatar-wrapper">
                            <img class="messages__avatar" style="width:100px"
                                 src="userpics/<?= ((int)$interlocutor['sender_id'] === (int)$user_data['id']) ? anti_xss($interlocutor['receiver_avatar']) : anti_xss($interlocutor['sender_avatar']) ?>"
                                 alt="Аватар пользователя">
                            <?= ((int)$interlocutor['new_messages'] > 0) ? '<i class="messages__indicator">'.$interlocutor['new_messages'].'</i>' : "" ?>
                        </div>
                        <div class="messages__info">
                            <span class="messages__contact-name">
                                <?= ((int)$interlocutor['sender_id'] === (int)$user_data['id']) ? anti_xss($interlocutor['receiver_name']) : anti_xss($interlocutor['sender_name']) ?>
                            </span>
                            <div class="messages__preview">
                                <p class="messages__preview-text">
                                    <?= ((int)$interlocutor['sender_id'] === (int)$user_data['id'] && !empty($interlocutor['last_message'])) ? 'Вы: '.anti_xss($interlocutor['last_message']) : anti_xss($interlocutor['last_message']) ?>
                                </p>
                                <?php $message_date = new DateTime($interlocutor['last_message_date']) ?>
                                <time class="messages__preview-time" title="<?= $message_date->format('d.m.Y H:i') ?>"
                                      datetime="<?= $message_date->format('Y-m-d H:i:s') ?> ?>">
                                    <?= last_message_date($message_date) ?>
                                </time>
                            </div>
                        </div>
                    </a>
                </li>
            <?php endforeach ?>
        </ul>
    </div>
    <div class="messages__chat">
        <div class="messages__chat-wrapper">
            <?= $chat_content ?>
        </div>
        <div class="comments">
            <form class="comments__form form" action="messages.php" method="post">
                <div class="comments__my-avatar">
                    <img class="comments__picture" src="userpics/<?= $user_data['avatar'] ?> "
                         alt="Аватар пользователя">
                </div>
                <div
                    class="form__input-section <?= (!empty($errors['message']) ? 'form__input-section--error' : "") ?>">
                    <textarea class="comments__textarea form__textarea form__input" name="message" id="message"
                              placeholder="Ваше сообщение"><?= getPostValue('message') ?></textarea>
                    <label class="visually-hidden">Ваше сообщение</label>
                    <button class="form__error-button button" type="button">!</button>
                    <div class="form__error-text">
                        <h3 class="form__error-title">Ошибка валидации</h3>
                        <p class="form__error-desc"><?= (!empty($errors['message']) ? $errors['message'] : "") ?></p>
                    </div>
                </div>
                <input type="hidden" name="receiver_id" id="receiver_id" value="<?= $receiver_id ?>">
                <button class="comments__submit button button--green" type="submit">Отправить</button>
            </form>
        </div>
    </div>
</section>
