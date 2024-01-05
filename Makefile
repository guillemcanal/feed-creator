.DEFAULT_GOAL := help
REPO = $(shell gh repo view --json owner,name -q '.owner.login + "/" + .name')

sync-config: # configure feeds
	test -f config.json && cat config.json | base64 | gh secret set CONFIG
.PHONY: sync-config

build-feeds: # generate and deploy feeds
	gh workflow run "Generate and Deploy Atom feeds to Pages"
.PHONY: build-feeds

send-config: # send the configuration file to your inbox in case you lost it
	gh workflow run "Send Config"
.PHONY: send-config

delete-workflow-runs: # delete all workflow runs
	gh api /repos/guillemcanal/feed-creator/actions/runs --paginate \
		| jq -r '.workflow_runs[].id' \
		| xargs -i -r gh api -X DELETE "/repos/${REPO}/actions/runs/{}"
.PHONY: delete-workflow-runs

help:
	 @grep -E '^[a-zA-Z_-]+:.*?# .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?# "}; {printf "  %-20s %s\n", $$1, $$2}'
.PHONY: help
