#!/usr/bin/env bash

docker-compose build;
docker-compose stop;
docker-compose -f docker-compose.yml up -d;
docker-compose exec -T php composer install;
docker-compose exec -T php npm install;
docker-compose exec -T php npm run start;
docker-compose exec -T php vendor/bin/phinx migrate;
docker-compose exec -T php vendor/bin/phinx seed:run;

docker inspect --format='{{.Name}} - {{range .NetworkSettings.Networks}} {{.IPAddress}}{{end}} - {{range $p, $conf := .NetworkSettings.Ports}} {{$p}} {{end}}' $(docker ps -aq)|grep genesis-test