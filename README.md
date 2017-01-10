# Verisure
Unofficial PHP library to interact with the Verisure Alarm System.

With this library you can get the status of the alarm system, connected locks, smart plugs and temperature sensors to integrate with your own Home Automation or IoT project.

Warning! Programming knowledge is required at this point of the project.

# Features

- Get status of alarm system (armed, unarmed, armedhome)
- Get status of locks (locked, unlocked)
- Get and change status of SmartPlugs (on/off)
- Get temperature from all sensors

# Install

First you need a Web Server with PHP5 or 7 installed and then:

```bash
$ cd /var/www/html/
$ git clone https://github.com/xibriz/Verisure.git
$ cd /tmp/
$ > verisure_cookiefile.txt && chmod 777 verisure_cookiefile.txt
$ > verisure_curl_error.txt && chmod 777 verisure_curl_error.txt
$ > verisure_x_csrf_token.txt && chmod 777 verisure_x_csrf_token.txt
```

# Configuration

You must put your credentials and devices in the config file located in `config/default.config.php`

