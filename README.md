# Service injects for Nette 2.1+

Nette Framework DI extension for injecting services into other services' properties.


Motivation
------
Writing constructor/setter injection is sometimes annoyingand while developing,
it may eat a lot of time. Also it is simpler to not bother with explicit injection
as long as it is not needed (e.g. in small application, not public project etc.).


Requirements
------
- PHP 5.3.1 or newer
- Nette 2.1 or newer


Installation
------

1. Add "`majkl578/nette-injects-extension`" to your dependencies in composer.json.
    Don't forget to run `composer update`.
2. Register this DI extension as last one in your configuration file in extensions section
    ```
    injects: Majkl578\NetteAddons\Injects\DI\Extension\InjectsExtension
    ```

3. Delete cache.

You're done. ;)


Usage
-----

All types of instance properties are supported - public, protected as well as private.
You can also inject a set of classes of a type, e.g. all implementers of an interface.
Also works well with namespaced code and uses.


```php
class Foo
{
	/**
	 * @inject
	 * @var Foo
	 */
	public $foo;

	/**
	 * @inject
	 * @var Bar
	 */
	protected $bar;

	/**
	 * @inject
	 * @var Baz
	 */
	private $baz;

	/**
	 * @inject
	 * @var IWatcher[]
	 */
	 protected $watchers;
}
```


Issues
------

In case of any problems, just leave an issue here on GitHub (or, better, send a pull request).
