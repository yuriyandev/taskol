include .env
export

up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose restart

logs:
	docker compose logs -f

ps:
	docker compose ps

.PHONY: mysql
mysql:
	docker exec -it $(MYSQL_CONTAINER) mysql -u$(MYSQL_USER) -p$(MYSQL_PASSWORD) $(MYSQL_DB)

mysqlbash:
	docker exec -it $(MYSQL_CONTAINER) bash