# wpsb-class-selector
And then instantiate the appropriate class to the template to be loaded.
(Use Autoloader)

## example(setup after Autoloader)
```PHP
require './WPsB_Class_Selector.php';

$args = array(
    'prefix' => "WPsB",
    'suffix' => "Controller",
    'store_name' => 'my_controller'
);

$WPsB_Class_Selector = new WPsB_Class_Selector($args);
$WPsB_Class_Selector->register();
```
post type is `post`, template `single.php`,
`WPsB_Single_Post_Controller` instance is stored in the `set_query_var($args['store_name'])`

in theme template,
```PHP
$my_data = get_query_var( 'my_controller' );


echo esc_html( $my_data->foo );
```
## feature

* getter/setter static function
* Corresponding to the `attachment', `paged`, `comment_popup`
* The name of the class should always be set in the snake case