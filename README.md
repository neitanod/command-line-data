Data
====

A mysql browsing tool for command line lovers.

Configuration:
--------------

First, create a folder in your project to store your 'data' command line tool
configuration and runtime information.

        $ cd myproject
        $ mkdir data
        $ cd data

Second, configure your server data. This needs to be done once per project.

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

Bootstrap:
----------

Bootstrapping the 'data' command line tool means that you set some base values to work with:
Choose a database to connect to, a table to query, etc.


Usage:
------

TODO: Describe querying commands here.
