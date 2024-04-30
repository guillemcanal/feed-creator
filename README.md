# Feed Creator

Leverage GitHub Actions to effortlessly generate and update ATOM feeds from webpages.

This process is scheduled to run daily at 4 AM, 10 AM, and 4 PM.

## Configuration

1. Fork this repo.
2. In <kbd>Settings</kbd> <kbd>Pages</kbd> select <kbd>Github Actions</kbd> as a **Build and deployment** source.
3. Generate a `config.json` based on `config.schema.json`
4. Create the following secrets and variable in <kbd>Settings</kbd> <kbd>Secrets and variables</kbd> <kbd>Actions</kbd>, or by using the [GitHub CLI](https://cli.github.com/) : 
   | name     | type     | description                                                                               |
   | -------- | -------- | ----------------------------------------------------------------------------------------- |
   | PASSWORD | secret   | A password to access your feeds located in a secret folder                                |
   | CONFIG   | secret   | Your `config.json` encoded in base 64. `cat config.json \| base64 \| gh secret set CONFIG` |
   | BASE_URL | variable | The deployment URL used by Github Pages (ex: https://guillemcanal.github.io/feed-creator), it's used to generate an OPML file with your feed subscriptions. |
1. Run <kbd>Actions</kbd> / <kbd> Generate and Deploy Atom feeds to Pages</kbd>.

## Config example

```json5
{
    // List of content extraction providers
    "providers": [
        {
            // Regex matching a feed URL
            "match": "^https://example.com",
            // Used to generate the title of the feed
            "feedTitle": {
                "selector": "h1"
            },
            // Used to retrieve items for the feed
            "items": {
                "selector": ".feed-items",
            },
            // Used to generate the title of an item
            "title": {
                "selector": ".item-title"
            },
            // Used to generate the URL of an item
            "link": {
                "selector": ".item-link",
                "attr": "href"
            },
            // Used to generate the published and updated attributes of an item
            "date": {
                "selector": "time",
                // Use a date format to create a datetime object (optional)
                "dateFormat": "M d,Y H:i"
            },
            // Used to generate the description of an item (optional)
            "description": {
                "selector": "a.magnet-link",
                "attr": "href",
                // Decorate the extracted text (optional)
                "template": "<a href=\"%s\">download</a>"
            }
        }
    ],
    // List of URLs used to generate Atom feeds
    "urls": [
        "https://example.com/news",
        "https://example.com/articles"
    ]
}
```

Plop.