<?php 

mb_internal_encoding("UTF-8");
mb_http_output ("UTF-8");

function numeric_expression($field, $expression){
	$values = explode("..", $expression);
	$start = intval($values[0]);
	$end = intval($values[1]);
	$sql = "";
	for ($i = $start; $i<$end; $i++){
			if ($sql == ""){
				$sql = " ( $field LIKE '%$i%' ) ";
			}
			else {
				$sql = $sql . " OR ( $field LIKE '%$i%' ) ";
			}
		};
  return $sql;
}

function nao_prefix($field, $word, $flag){
    
	$nt = "";
	$k = 0;
        
	if ($word[$k] === "-"){
		$nt = " NOT ";
		$k = 1;
	};
	
                    
        $oper = " AND "; // , as PROLOG
        if ($word[$k] === "?"){
                $k = $k+1; 
		$oper = " OR ";                
        };
                
        if (!$flag){                  
            $oper = "";
	};

	$v = substr($word, $k);
        
        $result = " $oper ( $nt $field LIKE '%$v%' )";
        
        if (strpos($v, "..")>0){            
            $result =  "$oper ( $nt ( ". numeric_expression($field, $v) . " ) ) ";
        };
	return  $result;
}

function like_expr_comb($field, $req){

$words = explode(",", $req);
$result = "";
foreach ($words as $k => $word){
	if ($result!=""){
		$result = $result . nao_prefix($field, $word, true);
	}
	else {
		$result = $result . nao_prefix($field, $word, false);
	};
};

return $result;

};

    function includes(){
$html = '<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">';
$html = $html . '<link rel="stylesheet" href="https://code.getmdl.io/1.2.0/material.indigo-pink.min.css">';
$html = $html . '<script defer src="https://code.getmdl.io/1.2.0/material.min.js"></script>';
$html = $html . '<style> .mdl-data-table {white-space:pre-line !important;} #results {margin:0 auto; padding:10px;}</style>';
$html .= $html . '<script defer src="/index.js"></script>';
return $html;
}

    function config(){
    $host = "localhost";
    $user = "";           
    $pass = "";        
    $db = "magnetsdb";         
    $port = 3306;              
    $limit = 10;
    return array(
            'host' => $host,
            'user' => $user,
            'pass' => $pass,
            'db'   => $db,
            'port' => $port,
            'limit' => $limit
        );
    }
    
    /*
    * $patterns = array('caption' => '...', 'labels' => ..., ...);
    */
    function patterns_to_sql($patterns){
			$sql = "";			
			foreach ($patterns as $field => $pattern){
			
                            if ($pattern !== ""){
                            
                                $expr = like_expr_comb($field, $pattern);
                            
				if ($sql !== "") {
						$sql = $sql . "AND ( $expr )";
				}
				else
				{
				$sql = "( $expr )";
				};
                            };
			};
                        
                        
			return $sql;
			
    }
    
    function request($patterns, $page){
        
    if (($patterns['caption'] == "")&&($patterns['category'] == "")&&($patterns['labels'] == "")){
        return array('total' => 0,
              'fetch' => array()                
              );
    };
    
    $cfg = config();
    
    
    $limit = $cfg['limit'];
    if ($page < 1)     {$page = 1;};
    $offset = ($page - 1) * $limit;
    
    

    $connection = mysqli_connect($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['db'], $cfg['port']);
    
    mysqli_set_charset($connection, "utf8");
    
    $like_expr = patterns_to_sql($patterns);
    
    $query = "SELECT count(*) as cnt FROM data WHERE $like_expr;";
    
    $result = mysqli_query($connection, $query);  
    $row = mysqli_fetch_assoc($result);
    $cnt = $row['cnt'];
    
    
     
    $query = "SELECT * FROM data WHERE $like_expr LIMIT $limit OFFSET $offset;";
    
    
    
        
    $result = mysqli_query($connection, $query);  
    $arr = array();
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($arr, $row);        
    };
    return
        array('total' => $cnt,
              'fetch' => $arr,
              'query' => $query);
    }
    
    function pagination($total){
        $cfg = config();        
        $pages = ceil($total / $cfg['limit'] );
        $html = "<div class='pagination'>";
        for ($i = 1;$i<($pages+1);$i++){
            $html = $html . "<span class='sear mdl-button' data-page='$i'>$i</span>";
        }
        $html = $html . '</div>';
        return $html;
    }
    
    
    
    function getAction() {
        $action = "index";        
        if (isset($_POST['action']))    {
            $action = $_POST['action']; 
        };    
    echo "<!-- $action -->";
    return $action;
    }
    
    function onIndex($patterns){
        $str = "<div style='padding:10px'><form name='searchForm' id='searchForm' action='/index.php' 
method='POST'>";
        $str = $str . "<input type='hidden' name='action' id='action' value='search'>";
        $str = $str . "<input type='hidden' name='page' id='page' value='1'>";
        $str = $str . "<h3>MagnetsDB</h3>";
        
        $str = $str . "<div style='margin-top:30px'>";
        
        $str = $str . 'Caption:<input class="mdl-textfield__input" type="text" name="caption" id="caption" value="'.$patterns['caption'].'">';
        
        
        
        $str = $str . 'Labels:<input class="mdl-textfield__input" type="text" name="labels" id="labels" value="'.$patterns['labels'].'">';
        
        
        
        $str = $str . 'Category:<input class="mdl-textfield__input" type="text" name="category" id="category" value="'.$patterns['category'].'">';
        
        $str = $str . "</div>";
        
        $str = $str . "<input style='margin-top:30px' class='mdl-button mdl-js-button mdl-button--raised mdl-button--colored' type='submit' value='Search'></form>";
        
        $str = $str . "</div><hr/>";
        
        return $str;
    }
        
    
    function onSearch($arr){   
        
        if (count($arr)==0){
            return "No results";
        };
        
        $html = "<div class='results'>";
        
        $table ='<table style="width:100%" class="mdl-data-table mdl-js-data-table mdl-data-table mdl-shadow--2dp">';
        $table = $table . '<thead>';
        $table = $table . '<tr>';
        $table = $table . '<th style="width:50% !important" class="mdl-data-table__cell--non-numeric">Caption</th>';
        $table = $table . '<th style="width:20% !important" class="mdl-data-table__cell--non-numeric">Category</th>';
        $table = $table . '<th style="width:30% !important" class="mdl-data-table__cell--non-numeric">Labels</th>';
        $table = $table .'</tr>';
        $table = $table . '</thead>';
        $table = $table . '<tbody>';
        
        $count = count($arr);
        foreach ($arr as $key => $elem){
            
            $magnet = $elem['hash'];
            $caption = $elem['caption'];
            $labels = $elem['labels'];
            $category = $elem['category'];
            
            
         
            
            
            $link = "<td class='mdl-data-table__cell--non-numeric'><a href='magnet:?xt=urn:btih:$magnet'>$caption</a></td>";
            $otherInfo = '<td>' . $category . "</td><td>". $labels . "</td>";
            $html_row = "<tr>$link $otherInfo</tr>";
            $table = $table . $html_row;
        };
        $table = $table . "</tbody></table>";
        $html = $html . $table . "</div>";
        return $html;
    }
    
    function script($patterns){
$caption = $patterns['caption'];    
$labels = $patterns['labels'];
$category = $patterns['category'];
return <<<EOT
<script>
window.onload = function(){   
var elems = document.querySelectorAll('.sear');
console.log(elems.length);        
for (var e in elems){
        elems[e].onclick = function(elems, e){
            return function(){            
            document.getElementById("page").value = elems[e].dataset['page'];
            document.getElementById("category").value = '$category';
            document.getElementById("caption").value = '$caption';
            document.getElementById("labels").value = '$labels';
            document.getElementById("searchForm").submit();
            };
        }(elems, e);
};

};    
</script>        
EOT;
    }


function load_categories(){
	// SELECT DISTINCT(CATEGORY) FROM DATA ORDER BY CATEGORY INTO FILE CATEGORIES.TXT
	$content = file_get_contents('CATEGORIES.TXT');
	$lines =  explode("\n", $content);
	$result = '<input id="cat" name="cat" type="text" value=""/>';
foreach($lines as $line) {
  $result .= '<p><input type="checkbox" value="'. $line.'"/>'.$line.'</p>';
}

return $result;
}
    
function mdl_template($title, $content){        
$t = "";
$t = $t . '<div class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer">';
$t = $t . '  <header class="mdl-layout__header">';
$t = $t . '<div class="mdl-layout__header-row">';
$t = $t . '<span class="mdl-layout__title">' . $title . '</span>';
$t = $t . '    </div>';
$t = $t . '</header>';
$t = $t . '  <div class="mdl-layout__drawer">';
$t = $t . '    <span class="mdl-layout__title">MagnetsDB</span>';
$t = $t . '    <nav class="mdl-navigation">';
$t = $t . load_categories();
$t = $t . '      <a class="mdl-navigation__link" href="https://github.com/artnazarov">Other versions</a>';      
    $t = $t . '</nav>';
  $t = $t . '</div>';
  $t = $t . '<main class="mdl-layout__content">';
    $t = $t . "<div>$content</div>";
  $t = $t . '</main>';
$t = $t . '</div>';
return $t;
    }
    
    function document($patterns, $title,  $body){
        $includes = includes();
        $mdl = "<div class='mdl-layout mdl-js-layout'>$body</div>";
        $script = script($patterns);
        return "<html><head>$includes<title>$title</title><body>$mdl $script</body></html>";
    }
    
    function main(){
        $result = "";
        $titles = array('index'=>'Поиск', 'search'=>'Найдено');
        
        $action = getAction();
        
        $title = $titles[$action];
        
        $patterns = array(
					'category' => '',
					'caption' => '',
					'labels' => ''
        );
        
        if ($action == 'search'){
              
              $patterns['caption'] = $_POST['caption'];
              $patterns['category'] = $_POST['category'];
              $patterns['labels'] = $_POST['labels'];
              
              $page = 1;
              if (isset($_POST['page'])){
                     $page = $_POST['page'];
                    };              
            $title = $title . $patterns['caption'] . ' ' . $page;
        };
        
        switch ($action){
            case 'index' : {
                    
                $result = onIndex($patterns); 
                break;
            
            }
            case 'search' : {
                      
                        $rows = request($patterns, $page);                        
                        $view = onSearch($rows['fetch']); 
                        $result = "<!-- " . $rows['query'] . " -->" . onIndex($patterns) . $view . pagination($rows['total']) . '<br/>Total:' . $rows['total'];
                        break;
                    
            };
        };
        echo document($patterns, $titles[$action], mdl_template($title, $result));

    }
    
   header('Content-Type: text/html; charset=utf-8');   
   main();
    
   

    
