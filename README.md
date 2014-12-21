Data
====

A mysql querying tool for command line lovers.

Usage:
------

Example:

        $ data config set server.localServer1.dsn "mysql:host=127.0.0.1;dbname=test;charset=UTF-8"
        $ data config set server.localServer1.user "root"
        $ data config set server.localServer1.pass "secret"
        $ data config show

        {
            "server": {
                "previouslyConfiguredServer": {
                    "dsn": "mysql:host=127.0.0.1;dbname=test;",
                    "user": "someusername",
                    "pass": "somesecret"
                },
                "localServer1": {
                    "dsn": "mysql:host=127.0.0.1;dbname=test;charset=UTF-8",
                    "user": "root",
                    "pass": "secret"
                }
            }
        }

        $ data server list
        
        prevoiuslyConfiguredServer
        localServer1
        
        $ data server use localServer1 
        $ data server using
        
        localServer1
        
        $ data server show
        
        {
            "dsn": "mysql:host=127.0.0.1;dbname=test;charset=UTF-8",
            "user": "root",
            "pass": "secret"
        }
        
        $ data server leave
