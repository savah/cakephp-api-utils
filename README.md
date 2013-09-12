# API Utils

[WizeHive's REST API](https://developers.wizehive.com/rest-api/) is built on top of CakePHP 2.x. During its development, there were many crucial decisions that needed to be made and code that needed to be written to support a complex API that would be truly usable with room to grow.

This repository **is** a demo API. It contains a directory of plugins which power most of it. While these plugins can theoretically be used separately in other apps, it is highly recommended that they be used as a bundle in a new app. In other words, this code is best used as a framework for an entirely new REST API project.

In the future, we will strive to provide detailed documentation for all of the complex functionality available in these plugins.

For now, the best way to get started is to set up the demo, experience it firsthand, and explore the code behind it.

## Hello World:

1. Download/clone this repository
2. Import the SQL in `/path/to/cakephp-api-utils/app/Config/Schema/demo.sql` to a database named `demo_api`. If you need to change any database configurations, do so in `/path/to/cakephp-api-utils/app/Config/database.php`
3. Set up the following virtual hosts:
  * api-demo.dev -> `/path/to/cakephp-api-utils/app`
  * auth.api-demo.dev -> `/path/to/cakephp-api-utils/app`
  * client.api-demo.dev -> `/path/to/cakephp-api-utils/oauth2-sample`
4. Configure your hosts file to point these locations to your localhost
5. Go to [http://client.api-demo.dev](http://client.api-demo.dev) in your browser
6. Click login. Sign up for your first account, then login with it.
7. You should be presented with an access token. Copy this.
8. Using this access token, make POST, GET, PUT and DELETE requests to your API. For example:
  * GET http://api-demo.dev/users/me?access_token={your access token}
9. That's it! You have a running API. Explore it, bring the plugins into your own app, customize to suit.
