<?php 


    function includes(){
$html = '<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">';
$html = $html . '<link rel="stylesheet" href="https://code.getmdl.io/1.2.0/material.indigo-pink.min.css">';
$html = $html . '<script defer src="https://code.getmdl.io/1.2.0/material.min.js"></script>';
$html = $html . '<style> .mdl-data-table {white-space:pre-line !important;} #results {margin:0 auto; padding:10px;}</style>';
return $html;
}

    function config(){
    $host = "localhost:3306";  
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
    
    function request($field, $pattern, $page){
        
    if ($pattern == ""){
        return array('total' => 0,
              'fetch' => array()
              );
    };
    
    $cfg = config();
    
    
    $limit = $cfg['limit'];
    if ($page < 1)     {$page = 1;};
    $offset = ($page - 1) * $limit;
    
    

    $connection = mysqli_connect($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['db'], $cfg['port']);
    
    $query = "SELECT count(*) as cnt FROM data WHERE $field LIKE '%$pattern%';";
    $result = mysqli_query($connection, $query);  
    $row = mysqli_fetch_assoc($result);
    $cnt = $row['cnt'];
     
    $query = "SELECT * FROM data WHERE $field LIKE '%$pattern%' LIMIT $limit OFFSET $offset;";
    
    
    
        
    $result = mysqli_query($connection, $query);  
    $arr = array();
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($arr, $row);        
    };
    return
        array('total' => $cnt,
              'fetch' => $arr);
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
    
    function onIndex($pattern){
        $str = "<form name='searchForm' id='searchForm' action='/magnetsdb.php' method='POST'>";
        $str = $str . "<input type='hidden' name='action' id='action' value='search'>";
        $str = $str . "<input type='hidden' name='page' id='page' value='1'>";
        $str = $str . '<div class="mdl-textfield mdl-js-textfield">';        
        $str = $str . '<input class="mdl-textfield__input" type="text" name="pattern" id="pattern" value="'.$pattern.'">';
        $str = $str . '<label class="mdl-textfield__label" for="pattern">Pattern...</label></div>';
        $str = $str . "<input class='mdl-button mdl-js-button mdl-button--raised mdl-button--colored' type='submit' value='Search'></form><hr/>";
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
    
    function script($pattern){
return <<<EOT
<script>
window.onload = function(){   
var elems = document.querySelectorAll('.sear');
console.log(elems.length);        
for (var e in elems){
        elems[e].onclick = function(elems, e){
            return function(){            
            document.getElementById("page").value = elems[e].dataset['page'];
            document.getElementById("pattern").value = '$pattern';
            document.getElementById("searchForm").submit();
            };
        }(elems, e);
};

};    
</script>        
EOT;
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
$t = $t . '      <a class="mdl-navigation__link" href="https://github.com/artnazarov">Other versions</a>';      
    $t = $t . '</nav>';
  $t = $t . '</div>';
  $t = $t . '<main class="mdl-layout__content">';
    $t = $t . "<div>$content</div>";
  $t = $t . '</main>';
$t = $t . '</div>';
return $t;
    }
    
    function document($pattern, $title,  $body){
        $includes = includes();
        $mdl = "<div class='mdl-layout mdl-js-layout'>$body</div>";
        $script = script($pattern);
        return "<html><head>$includes<title>$title</title><body>$mdl $script</body></html>";
    }
    
    function main(){
        $result = "";
        $titles = array('index'=>'Поиск', 'search'=>'Найдено');
        
        $action = getAction();
        
        $title = $titles[$action];
        
        $pattern = '';
        
        if ($action == 'search'){
              $pattern = $_POST['pattern'];
              $page = 1;
              if (isset($_POST['page'])){
                     $page = $_POST['page'];
                    };              
            $title = $title . $pattern . ' ' . $page;
        };
        
        switch ($action){
            case 'index' : {
                    
                $result = onIndex($pattern); 
                break;
            
            }
            case 'search' : {
                      
                        $rows = request('caption', $pattern, $page);
                        $view = onSearch($rows['fetch']); 
                        $result = onIndex($pattern) . $view . pagination($rows['total']) . '<br/>Total:' . $rows['total'];
                        break;
                    
            };
        };
        echo document($pattern, $titles[$action], mdl_template($title, $result));

    }
    
   main();
    
   

    
