<?php 

    # Check if the entry have variable to make redirection
    if(!empty($_GET["redirect"])){
        $co = new PDO("mysql:host=localhost;dbname=minlynk;charset=utf8", "root", "");
        $query = $co->prepare("SELECT link FROM urls WHERE shortcut = ?");
        $query->execute(array($_GET["redirect"]));
        $result = $query->fetch();
        $link = $result["link"];

        header("location: $link");
    }

    # Check if data have been sent
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
    <link rel="stylesheet" href="./fontawesome/css/all.css">
    <link rel="stylesheet" href="./tailwind/output.css">
    <!-- <script src="tailwind.js"></script> -->
    <title>minLynk</title>
</head>
<body >
    <div class="w-screen h-screen flex items-center justify-center bg-[url('./fond.png')] bg-cover bg-no-repeat font-mono text-center">
        <div class="flex flex-col items-center h-screen w-screen sm:w-full sm:h-4/5 lg:w-4/5 xl:w-3/5 md:w-full lg:h-2/5 md:h-3/5 backdrop-blur-md backdrop-brightness-150 bg-white/30 justify-center">
            <h1 class="text-white text-3xl mb-6">URL too much long ? Shrink it !</h1>
            <div class="flex flex-col w-4/5 h-2/5 justify-around">
                <form action="#" method="POST" class="w-full h-2/5 flex items-center justify-around sm:justify-between flex-col sm:flex-row">
                    <div class="w-full sm:w-4/5 relative">
                        <label class="hidden sm:inline absolute left-0 bg-black text-white p-3 px-7 rounded-full" for="url">URL</label>
                        <input id="url" class="w-full outline-none px-9 sm:pl-28 sm:pr-10 h-12 rounded-full" name="url" type="text" required placeholder="Paste your URL here">
                    </div>
                    <input class="bg-black text-white sm:ml-10 px-7 py-3 rounded-full duration-300 hover:cursor-pointer hover:bg-white hover:text-black" type="submit" value="Minify">    
                </form>
                <?php 
                    if (isset($final)){
                ?>
                        <div class="text-white bg-black px-10 relative flex items-center mt-5 h-10 w-full rounded-full">
                            <label class="bg-white h-full px-5 absolute left-0 text-black flex items-center rounded-full">Your mini URL</label>
                            <input class="bg-transparent w-full h-full pl-40" name="result" id="result" type="text" disabled value="<?php echo $final ?>"></span>
                            <button class="bg-white h-full px-3 absolute right-0 text-black rounded-full" id="copy"><i class="fas fa-copy"></i></button>
                        </div>
                <?php        
                    }else {
                        if (!empty($_GET["error"])) {
                            if($_GET["error"] == "tooShort"){
                                echo '<div class ="text-white flex items-center justify-center mt-5 h-10 w-full">';
                                echo '      <div class="border-b-2 border-white px-5 py-3 rounded-lg flex flex-col sm:flex-row">';
                                echo '          <span> Your URL is already too short </span>';
                                echo '          <span> ( ^ - ^ )"</span>';
                                echo '      </div>';
                                echo '</div>';       
                            }
                        }
                    }         
                ?>
            </div>
        </div>            
    </div>
   
    <script src="./fontawesome/js/all.js"></script>
    <script>
        let minLynk = document.getElementById("result");
        let copyButton = document.getElementById("copy");
        copyButton.onclick = () => {
            navigator.clipboard.writeText(minLynk.value);
            alert("Copied");
        } 
    </script>
</body>
</html>