#!/bin/bash
# This script is used to prepare and run the test
#
# Run it :
# bash scripts/build/test.sh

# Include functions
_DIR_=`dirname $0`
source $_DIR_/functions.sh

say_loud "Preparing..." "TEST"
bash $_DIR_/selenium.sh start

# Debug
say "Run..." "DEBUG"
java -jar /usr/lib/selenium/selenium-server-standalone-2.21.0.jar > /var/log/selenium/selenium-output.log 2> /var/log/selenium/selenium-error.log & echo $! > /tmp/selenium.pid

# Idle
say_loud "Waiting... [20 seconds]" "TEST"
sleep 5 
ps aux | grep "selenium-server"
say_loud "Waiting... [15 seconds]" "TEST"
sleep 5 
ps aux | grep "selenium-server"
say_loud "Waiting... [10 seconds]" "TEST"
sleep 5 
ps aux | grep "selenium-server"
say_loud "Waiting... [5 seconds]" "TEST"
sleep 5 
ps aux | grep "selenium-server"

# Run
say_info "Assuming finished." "TEST"
say_loud "Running test-suite" "TEST"
vendor/bin/phpunit