# Executables (local)
DOCKER_COMPOSE = docker-compose

# Docker containers
APP = $(DOCKER_COMPOSE) exec app

# Executables
PHP      = $(APP) php
COMPOSER = $(APP) composer
LARAVEL  = $(PHP) artisan
ANALYSER  = $(PHP) ./vendor/bin/phpstan analyse

# Misc
.DEFAULT_GOAL = help

## ————— Pet Shop Docker Makefile —————————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMPOSE) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMPOSE) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMPOSE) down --remove-orphans

stop: ## Stop the docker hub
	@$(DOCKER_COMPOSE) stop

logs: ## Show live logs
	@$(DOCKER_COMPOSE) logs --tail=0 --follow

sh: ## Connect to the PHP FPM container
	@$(APP) sh

## —— Composer ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "p=" to run a given command, example: make composer p='update'
	@$(eval p ?=)
	@$(COMPOSER) $(p)

vendor: ## Install vendors according to the current composer.lock file
vendor: p=install --prefer-dist --no-progress --no-scripts --no-interaction
vendor: composer

## —— Laravel ———————————————————————————————————————————————————————————————
artisan-command: ## List all Laravel commands or pass the parameter "p=" to run a given command, example: make artisan p='key:generate'
	@$(eval p ?=)
	@$(LARAVEL) $(p)

run-code-analysis: ## Run Larastan analyser
	@$(eval p ?=)
	@$(ANALYSER) $(p)

clear-cache: p=cache:clear ## Clear the cache
clear-cache: artisan-command

generate-appkey: ## Generate laravel application key.
generate-appkey: p=key:generate
generate-appkey: artisan-command

generate-jwt-key: ## Generate jwt secret key.
generate-jwt-key: p=jwt:generate-secret
generate-jwt-key: artisan-command

migrate-database: ## Run all laravel migrations present in the laravel migration directory.
migrate-database: p=migrate
migrate-database: artisan-command

migrate-reset: ## Reset All Database Migrations.
migrate-reset: p=migrate:reset
migrate-reset: artisan-command

run-test: ## Run All Test Present in the laravel application
run-test: p=test
run-test: artisan-command

insight: ## Run All PHP insight.
insight: p=insight
insight: artisan-command

setup-application: start vendor generate-appkey
