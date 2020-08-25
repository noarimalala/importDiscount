# importDiscount
Installation du projet pour tester les fonctionnalités demandées:

- version php "~7.1.3||~7.2.0||~7.3.0"
- requirement pour PHP, activez les extensions suivantes:(bcmath,
ctype, curl, ext-dom, gd, hash, iconv, intl, mbstring, openssl,
 pdo_mysql, simplexml, soap, xsl, zip)
- Décommenter les lignes suivantes dans le fichier de configuration apache httpd.conf
 LoadModule rewrite_module modules/mod_rewrite.so
 LoadModule version_module modules/mod_version.so
- créez une base de données nommée "kaliop"
- importez le fichier sql dans le dossier db
- dans la table core_config_data, changer le vhost
 dans la colonne "path" des champs web/unsecure/base_url et web/secure/base_url
- Ouvrez un cmd au niveau du projet et lancez la commande suivante:
php composer.phar install
- modifiez les informations de la base de données dans le fichier app/etc/env.php
- lancez les commandes qui suivent au niveau du projet
php -d memory_limit=-1 bin/magento cache:clean
php -d memory_limit=-1 bin/magento cache:flush
php -d memory_limit=-1 bin/magento setup:upgrade
// si c'est en mode production 
php -d memory_limit=-1 bin/magento setup:di:compile
//
php -d memory_limit=-1 bin/magento setup:static-content:deploy -f

- accédez dans l'url bo avec le login suivant:
 login: Kaliop / pwd: 123Admin456#
 - Pour tester l'import de fichiers csv et l'exécution du cron, accédez dans Store>configuration, puis: 
 - config cron, 
 ouvrez la section KALIOP et cliquez sur Cron configutaion, saisissez le "expression cron" et sauvegardez ; 
 ouvrez aussi ADVANCED>System>Cron configuration options for group: cron_kaliop et
 configurez le cron group selon vos besoins et sauvegardez
- import fichier csv,
 cliquez sur Import coupon code csv file, importez les fichiers csv et sauvegardez

- lancez la commande suivante pour tester l'enregistrement des coupons à l'intérieur de fichiers importés
dans la base de données avec cron
php bin/magento cron:run --group="cron_kaliop"
