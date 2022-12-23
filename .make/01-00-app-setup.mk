
##@ [Application: Setup]

.PHONY: setup
setup: ## Setup the application
	"$(MAKE)" composer ARGS="install"
	"$(MAKE)" setup-db
