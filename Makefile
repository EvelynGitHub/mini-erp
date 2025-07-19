# Caminhos e variáveis
EXEC_PHP = docker exec mini-erp-php
COMPOSE = docker compose -f docker/docker-compose.yml

.PHONY: check-docker
.PHONY: artisan

# Docker
up:
	$(COMPOSE) up -d

# Remove cache/volume: down -v
down:
	$(COMPOSE) down

build:
	$(COMPOSE) build

check-docker:
	@count=$$(docker ps -q | wc -l); \
	if [ "$$count" -eq 0 ]; then \
		echo "⚠️  Nenhum container está rodando."; \
	else \
		echo "✅ Existem $$count containers em execução."; \
	fi

restart: down up

# Composer
composer-install:
	$(EXEC_PHP) composer install