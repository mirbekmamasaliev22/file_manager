build:
	@sudo docker-compose build

upload_db:
	@sudo cat data/dump.sql | sudo docker exec -i mad_forum_mysql_service_1 mysql -u root --password=password forum_db

logs:
	@sudo docker-compose logs -f

run:
	@sudo docker-compose up -d

stop:
	@sudo docker-compose down

view_i:
	@sudo docker images

view_c:
	@sudo docker ps -a

enter_nginx:
	@sudo docker exec -it nginx_container bash

enter_mysql:
	@sudo docker exec -it mysql_container bash

enter_php:
	@sudo docker exec -it php_container bash

enter_composer:
	@sudo docker exec -it composer_container bash

perm:
	@sudo chmod 777 -R var/cache
	@sudo chmod 777 -R var/logs