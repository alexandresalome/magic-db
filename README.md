# MagicDB - Proof Of Concept

A small project to show how it's possible to use docker to manage databases in a "git" manner. Here is a sample workflow:

* Create an empty database
* Import a production dump in it
* Snapshot the database
* Create a database from the snapshot
* Delete all data
* Drop database
* Recreate database from snapshot

Those operations are blazing fast, thanks to docker, so that you can get a big production database in a few seconds.

**Credits**: this is a Proof Of Concept from an application developed at Auchan e-Commerce France.

## Limitations

This project is a POC developed during a Docker Meetup, held at **Epitech** on April 24th, 2014.

Code is rough and not very maintenable, but it's an effective POC. You will need a MySQL image (``alexandresalome/mysql`` here) where the ``/var/lib/mysql`` is not a volume, so that it gets saved when you snapshot your database.

## Usage

Given you start the application on http://localhost:8080 and you have in ``web/images.json`` the following content:

    {
        "alexandresalome/mysql": "empty"
    }

### Start a container

    curl -X POST http://localhost:8080/create -d '{"image":"empty"}'

This command will return information about your database, especially:

* **ID**: the ID to use for stop/snapshot actions
* **PORT**: the port to use to connect to the database

## Snapshot a container

    curl -X POST http://localhost:8080/snapshot \
    -d '{"id":"<ID>","name":"<NAME>"}'

You must provide two things:

* **ID**: the ID returned by the start command
* **NAME**: the name of the docker image to create from it

## Stop a container

    curl -X POST http://localhost:8080/stop \
    -d '{"id":"<ID>"}'

You must provide two things:

* **ID**: the ID returned by the start command
