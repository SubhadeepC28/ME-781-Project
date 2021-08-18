#!/bin/sh
sudo systemctl restart apache2.service
date>>/var/www/html/LiveChat/cronjob_logs.txt