<?php

if(!empty($_POST['url'])) {
    
    $url = $_POST['url'];
    if (strlen($url) <= (strlen("http://".$_SERVER['HTTP_HOST']."/minLynk/index.php?redirect=")+6)) {
        header("location: ./index.php?error=tooShort");
    }
    
    $co = new PDO("mysql:host=localhost;dbname=minlynk;charset=utf8", "root", "");

    # Verify if url already exist in database
    $query = $co->prepare("SELECT * FROM urls WHERE link = ?");
    $query->execute(array($url));
    $row = $query->fetch(); 
    
    if(!$row) {
        # Insert if it's not in
        $key = "".time();
        $short = "Y".substr($key, -5);
        $query2 = $co->prepare("INSERT INTO urls(link, shortcut) VALUES (?,?)");
        $query2->execute(array($url, $short));        
    
    }else {
        # Get shortcut if it's in
        $short = $row["shortcut"];  
    }
    
    $final = "http://".$_SERVER['HTTP_HOST']."/minLynk/index.php?redirect=$short";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="index.php" method="post" id="formResult">
        <input type="text" name="result" value="<?php echo $final?>">
    </form>
    <script>
        document.getElementById("formResult").submit();
    </script>
</body>
</html>