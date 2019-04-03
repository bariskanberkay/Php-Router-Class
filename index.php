<?PHP

include('system/route/Route.php');

Route::add('/',function(){

	echo "main page";

},['get','post']);

Route::add('/test',function(){

	echo "test page";

	}
},['get','post']);


Route::run('/');













?>
