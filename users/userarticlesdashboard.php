<?php
    session_start();

    try{
        $base = new PDO('mysql:host=localhost; dbname=sensei; charset=utf8',"root","");
        // echo "connexion done";
    }
    catch(Exception $e){
        // echo "connexion not done";
    };
//   echo $_SESSION['id_user']; 

    $errors=array( 'article_title' => '', 'category_title' => '', 'category_selection' => '', 'article_description' => '', 'srcimage' => '','text_editor' => '');

    if (isset($_POST['publish'])) {
        $articletitle = $_POST['article_title'];
        $categorytitle = $_POST['category_selection'];
        $articledescription = $_POST['article_description'];
        $texteditor = $_POST['text_editor'];


        $_SESSION['article_title'] = $articletitle;
        $_SESSION['category_selection'] = $categorytitle;
        $_SESSION['article_description'] = $articledescription;
        $_SESSION['text_content'] = $texteditor;

        // var_dump($_SESSION['article_title']);
        // var_dump($_SESSION['category_selection']);
        // var_dump($_SESSION['article_description']);
        // var_dump($_SESSION['text_content']);
        

        if(empty($_POST['article_title'])){
            $errors['article_title']="Article Title Required";
        }
        if(empty($_POST['article_description'])){
            $errors['article_description']="Article Description Required";
        }

        

    
            if(isset( $_FILES['srcimage'])    and  $_FILES['srcimage']['error'] == 0)
            {
                if( $_FILES['srcimage']['size']< 3000000)
                {
                    $list_extensions = array('png','jpg', 'jpeg', 'gif'); 
                    $details = pathinfo($_FILES['srcimage']['name']); 
                    $extension = $details['extension']; 
                    $resultat = in_array($extension, $list_extensions); 
                    if($resultat)
                    {
                        move_uploaded_file($_FILES['srcimage']['tmp_name'], 'images-blog/'.$details['basename']); 
                        $link_image = 'images/'.$details['basename'] ; 
                        $_SESSION['image_article']=$link_image;

                        // var_dump($_SESSION['image_article']);
 
                         $requette = $base->prepare("UPDATE users SET image_article = ?  WHERE article_title = ?");
                         $resultat = $requette->execute(array($link_image, $_SESSION['article_title']));
                        //  var_dump($_SESSION['article_title']);
                    }
                }
            }

            if(empty($_POST['text_editor'])){
                $errors['text_editor']="Article Content Required";
            }
     
         
            $requette1 = $base->prepare("insert into articles(title,description,image_article,content,id_category,id_user) values(?,?,?,?,?,?)");
            $requette1->execute(array($_SESSION['article_title'],$_SESSION['article_description'],$_SESSION['image_article'],$_SESSION['text_content'],$_SESSION['id_category'],$_SESSION['id_user']));

            
    }

?>






<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DASHBOARD/ARTICLES</title>
    <link rel="stylesheet" href="css/stylearticle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        
    </style>
</head>
<body>
    <div class="top-section">
        <div class="header">
            <div class="logo"><a href="LandingPage.php"><span>SEN</span>SEI</a></div>

            <div class="nav-links">
                <a href="aboutuspage.php">ABOUT US</a>
                <a href="#">PARTENERS</a>
                <a href="#">BLOG</a>
                <a href="#">CONTACTS</a>
            </div>

            <div class="menu">
                <a href="#"><i class="fa fa-list"></i>MENU</a>
            </div>

            <div class="social-links">
                <a href="#"><i class="fa fa-twitter"></i></a>
                <a href="#"><i class="fa fa-facebook"></i></a>
                <a href="#"><i class="fa fa-instagram"></i></a>
                <a href="#"><i class="fa fa-linkedin"></i></a>
            </div>
        </div>
    </div>

    <div class="dashboard-section">

        <div class="dashboard-header">

        <div class="username"><?php   echo $_SESSION['username'] ;  ?></div>

            <nav class="search-bar">
                <form class="form-inline">
                    <input class="search-input" type="search" name="search" placeholder="Search" aria-label="Search">
                    <input class="search-btn" type="submit" name="btn-search" value="search">
                </form>
            </nav>

        </div>


        <div class="dashboard-main-content">

            <div class="dashboard-side-content">
                <ul class="links-list">
                    <li><a href="userprofiledash.php">Profile</a></li>
                    <li><a href="userarticlesdashboard.php">Manage Articles</a></li>
                    <li><a href="usercommentsdashboard.php">Manage Comments</a></li>
                    <li><a href="deconnexion.php">Disconnect</a></li>
                </ul>
            </div>

            
            <div class="dashboard-article-content">
            <form class="article-editor" method="POST" enctype="multipart/form-data"">

<div class="add-title article-div">
    <h1>Add Title</h1>
    <input type="text" class="article-inputs" name="article_title" id="">
    <select name="category_selection" id="">
        <?php     $reponse = $base->query("select * from categories");
            while($ligne=$reponse->fetch()) {
                $_SESSION['title_category']=$ligne['title_category'];
                $_SESSION['id_category']=$ligne['id_category'];
        ?>
        <option value="<?php   echo $_SESSION['title_category'] ;   ?>" class="options"><?php   echo $_SESSION['title_category'] ;   ?></option>
        <?php } ?>
    </select>
</div>
 <h4 style='color: #ec1a13; font-size:15px;'><?php echo $errors['article_title']; ?></h4>


<div class="add-descr article-div">
    <h1>Add Description</h1>
    <input type="text" class="article-inputs" name="article_description" id="">
</div>
<h4 style='color: #ec1a13; font-size:15px;'><?php echo $errors['article_description']; ?></h4>


<div class="add-image article-div">
    <input type="file" name="srcimage" id="file" style="display:none">
    <label for="file" style="color:#141517; background-color:#ecc113; height:30px; width: 40px; padding:10px 20px;cursor:pointer;">Choose a Picture</label>
</div>


<div class="add-article article-div">
    <h1>Add Article</h1>
    <textarea name="text_editor" id="text_editor2" cols="30" rows="10"></textarea>
</div>
<h4 style='color: #ec1a13; font-size:15px;'><?php echo $errors['article_description']; ?></h4>


<div class="publish-article article-div">
    <h1>Publish Article</h1>
    <input type="submit" name="publish" value="Publish" style="color:#141517; background-color:#ecc113;border:none;padding:10px 30px;cursor:pointer;">
</div>

<div class="manage-articles article-div">
    <table class="table">
        <thead>
            <tr>
            <th scope="col">#Articles</th>
            <th scope="col">Modify</th>
            <th scope="col">Discard</th>
            </tr>
        </thead>
        <tbody>
        <?php     $reponse = $base->query("select * from articles");
            while($ligne=$reponse->fetch()) { 
                $_SESSION['article_title']=$ligne['title'];
                $_SESSION['id_category']=$ligne['id_category'];
        ?> 
        
            <tr>
            <td><a href="#"><h2><?php   echo $_SESSION['article_title'] ;   ?></h2></a></td>
            <td><a href="#?id=<?php echo $ligne['id_category'];?>&category=<?php echo $ligne['title_category']?>">Update</a></td>
            <td><a name="delete" href="adminarticlesdashboard.php?id=<?php echo $ligne['id_article'];?>&title=<?php echo $ligne['title']?>">Delete</a></td>
            <?php
                if(isset($_GET["id"])){
                    $category = $_GET["category"];


                    $requette = $base->prepare("DELETE FROM categories WHERE id_category='" . $_GET['id'] . "'");
                    $resultat = $requette->execute(array());
                    // var_dump($requette);
                    header('location:userarticlesdashboard.php');

                }
    
            ?>
            </tr>
        
        <?php } ?>
        </tbody>
    </table>
</div>
</form>
            </div>
                
        </div>
    </div>



    <script src="ckeditor5-build-classic/ckeditor.js"></script>
    <script>
        ClassicEditor.create(document.getElementById('text_editor2'));

        // ClassicEditor
		// .create( document.querySelector( '#editor' ), {
		// 	// toolbar: [ 'heading', '|', 'bold', 'italic', 'link' ]
		// } )
		// .then( editor => {
		// 	window.editor = editor;
		// } )
		// .catch( err => {
		// 	console.error( err.stack );
		// } );
    </script>
</body>
</html>