php -d memory_limit=-1 bin/magento cache:clean
php -d memory_limit=-1 bin/magento cache:flush
php -d memory_limit=-1 bin/magento setup:upgrade
php -d memory_limit=-1 bin/magento setup:static-content:deploy -f
