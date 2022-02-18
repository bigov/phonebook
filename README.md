# LAN Phonebook

Телефонный справочник в режиме WiKi для корпоративной локальной сети

Иерархический список подразделений и сотрудников

![Tree structure view](assets/title.png)

Диалог поиска данных

![Search dialog](assets/search.png)

Карточка сотрудника

![Emplyerr view](assets/employer.png)


## Install

Для работы требуется активировать ``rewrite_module`` веб-сервера, и установить PHP-модули ``php-sqlite3``, ``php_mbstring``.

Для установки разместите содержимое репозитория в каталог веб-сервера и скопируйте установочный файл данных в рабочую базу:
```bash
cd /var/www/html/
git clone --recursive https://github.com/bigov/phonebook.git
cp phonebook/db/phones.sqlite.INSTALL phonebook/dbphones.sqlite
```

Подробности настройки см. в [Wiki](https://github.com/bigov/phonebook/wiki)
