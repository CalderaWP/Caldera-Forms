# Running Tests

* Switch to Dir (if using VM, should be done inside of VM)
* On Josh's VVV:

    cd /srv/www/cf-dev/htdocs/wp-content/plugins/Caldera-Forms
    
* If needed install tests and then switch back to main repo

    cd bin

    ./install-wp-tests.sh cf_tests wp wp localhost
    
    cd ../
    
* Run tests:

    phpunit
