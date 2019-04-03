<?PHP

class Route{

  private static $routes = Array();
  private static $pathNF = null;
  private static $methodNA = null;


  public static function add($phrase, $fnc, $method = 'get'){
    array_push(self::$routes,Array(
      'phrase' => $phrase,
      'function' => $fnc,
      'method' => $method
    ));
  }

  public static function pathNF($fnc){
    self::$pathNF = $fnc;
  }

  public static function methodNotAllowed($fnc){
    self::$methodNA = $fnc;
  }

  public static function run($basepath = '/', $case_matters = false, $trailing_slash_matters = false){

    
    $parsed_url = parse_url($_SERVER['REQUEST_URI']);

    if(isset($parsed_url['path']) && $parsed_url['path'] != '/'){
	  if($trailing_slash_matters){
		$path = $parsed_url['path'];
	  }else{
		$path = rtrim($parsed_url['path'], '/');
	  }
    }else{
      $path = '/';
    }

    
    $method = $_SERVER['REQUEST_METHOD'];

    $path_match_found = false;

    $route_match_found = false;

    foreach(self::$routes as $route){

      
      if($basepath!=''&&$basepath!='/'){
        $route['phrase'] = '('.$basepath.')'.$route['phrase'];
      }

      
      $route['phrase'] = '^'.$route['phrase'];

     
      $route['phrase'] = $route['phrase'].'$';

     
      if(preg_match('#'.$route['phrase'].'#'.($case_matters ? '':'i'),$path,$matches)){

        $path_match_found = true;

       
        foreach ((array)$route['method'] as $allowedMethod) {
            
            if(strtolower($method) == strtolower($allowedMethod)){

                array_shift($matches);

                if($basepath!=''&&$basepath!='/'){
                    array_shift($matches);

                call_user_func_array($route['function'], $matches);

                $route_match_found = true;

                
                break;
            }
        }
      }
    }

   
    if(!$route_match_found){

     
      if($path_match_found){
        header("HTTP/1.0 405 Method Not Allowed");
        if(self::$methodNA){
          call_user_func_array(self::$methodNA, Array($path,$method));
        }
      }else{
        header("HTTP/1.0 404 Not Found");
        if(self::$pathNF){
          call_user_func_array(self::$pathNF, Array($path));
        }
      }

    }

  }

}
