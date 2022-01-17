# cms

réalisation des base d'un cms en symfony

## Instalation

après avoir cloner le répo effectuer la commande

``composer install``

modifier vos information dans le fichier .env afin qu'elle correspondent à votre connection à votre base de donnée

à la ligne : ```DATABASE_URL="mysql://username:password@127.0.0.1:3306/cms?serverVersion=5.7"```

puis faite ```php bin/console doctrine:database:create```
puis ```php bin/console make:migration```
puis ```php bin/console doctrine:migrations:migrate```

et enfin faite ```symfony server:start```
et rendez-vous à cette adresse : ``http://127.0.0.1:8000``
