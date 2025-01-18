# PHP Built-in Webserver 🖥️

Since PHP version 5.4, PHP has included a [built-in web server](https://secure.php.net/manual/en/features.commandline.webserver.php), making it easier to serve applications during development without requiring a full-fledged web server like Apache or Nginx.

Magento, like many other applications, relies on server rewrites. The built-in PHP web server provides a **router script** to handle these rewrites. The router script does the following:

- Executes the requested PHP script using a server-side include.
- Returns `false` if the file isn't found, causing the server to return the file using a file system lookup.

### Example 🛠️

For example, a request to `/static/frontend/Magento/blank/en_US/mage/calendar.css` will either:

- Return the file if it exists.
- Execute `/static.php` if the file doesn’t exist.

Without the router script, this functionality isn’t possible with the PHP built-in server.

---

## 🚀 How to Install Magento

To install Magento, follow the [command line installation guide](https://experienceleague.adobe.com/docs/commerce-operations/installation-guide/advanced.html). Below is a sample installation command:

```php
php bin/magento setup:install --base-url=http://127.0.0.1:8082 \
--db-host=localhost --db-name=magento --db-user=magento --db-password=magento \
--admin-firstname=Magento --admin-lastname=User --admin-email=user@example.com \
--admin-user=admin --admin-password=admin123 --language=en_US \
--currency=USD --timezone=America/Chicago --use-rewrites=1 \
--search-engine=elasticsearch7 --elasticsearch-host=es-host.example.com --elasticsearch-port=9200
```

⚠️ Important Note: Magento generates a random Admin URI during installation. Be sure to write it down, as it’s needed to access the Magento Admin later (e.g., <http://127.0.0.1:8082/index.php/admin_1vpn01>).

For more details, check out the developer documentation.

🏃‍♂️ How to Run Magento

Once Magento is installed, you can start the server with the following command:

php -S 127.0.0.1:8082 -t ./pub/ ./phpserver/router.php

🔍 What Does the Script Do?

The router.php script handles various tasks, including logging, server rewrites, and file routing.

🔄 Forwarding Rules:

- Requests for favicon.ico or paths starting with index.php, get.php, static.php are processed normally.
- Requests to pub/errors/default are rewritten to errors/default for compatibility with older versions.
- Requests to media, opt, or static paths are tested for file existence:
- If the file exists, it is served.
- If the file does not exist:
- Static files are forwarded to static.php.
- Media files are forwarded to get.php.
- If none of the above rules match, a 404 Not Found is returned.

📝 Rewriting Paths:

- For backward compatibility with older Magento versions, paths like pub/errors/default/ are rewritten by removing the pub/ prefix.
- Requests for paths starting with media/, opt/, or static/ are tested:
- If the file exists, it’s served.
- If the file does not exist, requests for static are forwarded to static.php, and requests for media are forwarded to get.php.

If no rules match, a 404 Not Found error is returned. Alternatively, you can include index.php if you prefer to handle 404s within Magento or wish to have URLs without index.php/.

Accessing the Admin Dashboard 🛒

Once the installation is complete, you can access the Magento store and admin dashboard:

- Storefront: <your Magento base URL>
- Magento Admin: <your Magento base URL>/<admin URI>

This setup is great for development purposes. If you’re deploying Magento to a production environment, be sure to use a more robust web server like Apache or Nginx. 🚀
