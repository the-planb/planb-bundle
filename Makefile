.PHONY: tests/unit tests/coverage analysis fix-style pre-commit

# Añadimos pre-commit a la lista de palabras que filtramos de los argumentos
ARGS = $(filter-out $@ pre-commit fix-style analysis tests/unit tests/coverage,$(MAKECMDGOALS))

# Variables de binarios
PHP_CS_FIXER = bin/php-cs-fixer
PHPSTAN = bin/phpstan
PHPUNIT = bin/phpunit
PHP_CS_CONFIG = .php-cs-fixer.dist.php
CLEAN_FILES = $(strip $(FILES))

tests/run:
	$(PHPUNIT) --no-coverage --display-all-issues $(ARGS)

tests/coverage:
	XDEBUG_MODE=coverage $(PHPUNIT) $(ARGS)

show/coverage:
	xdg-open build/reports/coverage/dashboard.html

git/massive-fix:
	git add . && git diff --staged --name-only --diff-filter=M | xargs -I {} sh -c '\
		hash=$$(git log -n 1 --pretty=format:%H -- "$$1"); \
		if [ -n "$$hash" ]; then \
			git commit --no-verify --fixup="$$hash" "$$1"; \
		fi \
	' -- {}

analysis:
	$(PHPSTAN) analyse src --level=max

# Formateo de código. Si se pasa FILES, actúa solo sobre ellos.
fix-style:
	@if [ -z "$(strip $(FILES))" ]; then \
		$(PHP_CS_FIXER) fix --config=$(PHP_CS_CONFIG) .; \
	else \
		$(PHP_CS_FIXER) fix --config=$(PHP_CS_CONFIG) --path-mode=intersection -- $(strip $(FILES)); \
	fi


# Meta-tarea para el hook de git
pre-commit: fix-style analysis tests/unit

%:
	@:

