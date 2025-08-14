# Warp Core Example Apps

This is a collection of simple applications for the [Warp Core microframework](https://github.com/gordonmcvey/warp-core-php).  They can be used to serve as examples of how to work with Warp Core, or as skeleton apps to serve as the foundation of a full Warp Core application.

## Running

You can run these applications directly from PHP's built-in web server (recommended only for local testing/development), in a Docker container, or directly on a web server configured to run PHP.  

### Running with PHP's web server

Once you've cloned the repo, `cd` into one of the sub-directories under `/examples` (for example `/examples/vanilla`).  Then configure, install dependencies, and run the PHP server from the `/public` subdirectory:

Setting up the environment is simply a matter of copying the `.env.example.*` file you wish to use as the basis of your config to `.env`.

For testing/development with all features to expose errors enabled: 
```shell
cp .env.example.dev .env
```

For or production with error-exposing features disabled:
```shell
cp .env.example.prod .env
```

Then, run the application:

```shell
composer install
cd ./public
php -S localhost:8000
```

### Running from a web server

The specifics of running Warp Core from a web server such as Apache or Nginx will depend on the specific setup you want to run the application under.  Generally though, you'd want to make `public` your document root directory, and have all incoming requests (that don't correspond to a static file) to `index.php`.  The `src` and `vendor` subdirectories should remain outside the document root and inaccessible from the outside.  

### Running from Docker

Coming soon

## Example applications

I've provided a number of example applications, all of which do the same thing, but which use different methods for managing dependency injection.  Warp Core was intended to be as flexible as possible, so it implements DI in an implementation-agnostic manner, and you should be able to adapt it to whichever DI manager you prefer.  

The provided examples include: 

* `vanilla`: This is a vanilla implementation that doesn't use a DI container at all; all dependencies are manually injected.  Should you want to implement DI using some mechanism other than the ones demonstrated in the other examples, this would be a good place to start.
* `league-container`: Automatic DI managed with the [League DI container](https://container.thephpleague.com/)
* `php-di:` Automatic DI managed with the [PHP-DI container](https://php-di.org/)
* `symfony-di`: Automatic DI managed with the [Symfony DI container](https://symfony.com/doc/current/components/dependency_injection.html) component

## Usage

When the application is running, you can trigger its endpoints with HTTP requests using any suitable method such as invocation from a browser, tools like Postman or Bruno, and so on.

### Endpoints

There are two endpoints available:

* `/health/ping`: Returns a simple response with when the request was received and when it was dispatched (can only be invoked with `GET`)
* `/health/echo-payload`: Takes whatever JSON is in the request body and echos it back (can only be invoked with `POST` or `PUT`)

### Bruno Collection

The repo includes a collection of [Bruno](https://www.usebruno.com/) requests that include documentation and examples.  You can import them into Bruno and run them from there.

As the `echo-payload` endpoint requires you to `POST` or `PUT` a JSON document, running it from a browser, from CURL, etc would be tricky, but the `ping` endpoint can be invoked with a simple `GET` request.

## Rationale

The main goal of this project are as follows: 

* Serve as an example Warp Core application
* Allow me to find bugs in Warp Core that might now show up in its unit/integration tests
* Try out different use cases in Warp Core
* Find ways of improving Warp Core's usability (if something is difficult to set up in this app then that would suggest we make changes to Warp Core's API to improve the situation)
* Determine a set of common dependencies that would make sense for a real Warp Core application (DI, .env handling, logging, etc)
* Determine how to best use Warp Core in an application

As such, there will be limited (if any) automated testing included in this application, as in a way it serves as a test in its own right.
