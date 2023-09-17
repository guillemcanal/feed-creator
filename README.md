# Feed Creator

A project for creating ATOM Feeds from Webpages.

## Configuration

1. Select `Github Actions` in `Source` located in Settings/Pages/Build and deployment.
2. Generate a secret password `gh secret set PASSWORD`
3. Create a `config.json`
4. Store the config in Gihub Secrets `cat config.json | base64 | gh secret set CONFIG`