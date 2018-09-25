<?PHP
    /**
     *   Getting ADE ID from user ID to access to ADE API
     **/
    
    include('access.php');
    
    $urlID=$ADE_WEBAPI_SERVER."?function=connect&login=$LOGIN&password=$PASSWORD" ;
    $tmp=implode('', file($urlID));
    $contentXML = simplexml_load_string($tmp);
    $id= $contentXML['id'];
    
    /**
     *   Customize display from different configurations
     **/
    //Step 1: init project ID
    $urlStep1=$ADE_WEBAPI_SERVER.'?sessionId='.$id.'&function=setProject&projectId='.$PROJECTID;
    file_get_contents($urlStep1);
    
    //Step 2: get EDT image from various settings
    $width=400;
    $height=300;
    // default mode (to recover it go to ADE client, in the configuration display, select add "code" field in the top front of the tab).
    $modeAffichage=411;
    // Should be set according the ressource to be displayed
    $idtree=1;
    
    if(isset($_GET['width']) && is_numeric($_GET['width'])){
        $width = $_GET['width'];
    }
    if(isset($_GET['height']) && is_numeric($_GET['height'])){
        $height = $_GET['height'];
    }
    if(isset($_GET['idTree'])){
        $idtree = $_GET['idTree'];
    }
    
    // Week number manual set the app, to be removed to full set from the querystring parameters...
    if(isset($_GET['week'])&& is_numeric($_GET['week'])){
        // cas affichage JL
        $numSemaine=($_GET['week']+22)%53;
        $numSemaine+=0;
    }else{
        // cas affichage TV IUT
        $numSemaine= (date("W",time())+20)%53;
    }
    // pour application IUT ...
    if(!isset($_GET['source'])&&isset($_GET['week'])){
        $numSemaine=($_GET['week']+22)%53;
    }
    

    if(isset($_GET['displayMode'])&&is_numeric($_GET['displayMode'])){
        $modeAffichage = $_GET['displayMode'];
    }

    // constructing the final request:
    $urlStep2=$ADE_WEBAPI_SERVER.'?sessionId='.$id.'&function=imageET&resources='.$idtree.'&width='.$width.'&height='.$height.'&weeks='.$numSemaine.'&displayConfId='.$modeAffichage.'&days=0,1,2,3,4,5';
    
   
    $imageRes = file_get_contents($urlStep2);
    file_put_contents('edt.gif', $imageRes);

    header ("Content-type: image/png");
    $image=imagecreatefromgif("edt.gif");
    imagepng($image);
    imagedestroy($image);

?>
