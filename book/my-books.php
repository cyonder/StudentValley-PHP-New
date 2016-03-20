<?php
require_once("../include/header.php");

$user1_id = return_user_id($dbConnection, $_SESSION['email']);

/* The query below returns all the necessary information for book page. */
$query = "SELECT * FROM BOOKS WHERE BOOK_OWNER = '$user1_id' ORDER BY BOOK_DATE DESC";
$result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));
$num_of_ads = mysqli_num_rows($result);

$book_id = array();
$school = array();
$date = array();
$type = array();
$title = array();
$content = array();
$price = array();
$owner = array();
$picture = array();

if($num_of_ads > 0){
    while($row = mysqli_fetch_assoc($result)){
        $book_id[] = $row['BOOK_ID'];
        $school[] = $row['BOOK_SCHOOL'];
        $date[] = date_create($row['BOOK_DATE']);
        $type[] = $row['BOOK_TYPE'];
        $title[] = $row['BOOK_TITLE'];
        $content[] = $row['BOOK_CONTENT'];
        $price[] = $row['BOOK_PRICE'];
        $owner[] = $row['BOOK_OWNER'];
        $picture[] = $row['BOOK_PICTURE_ID'];
    }
}else{
    /* You don't have any book advertisement yet. */
}
?>
<div id="container" class="clearfix">
    <?php require_once("../include/sidebar.php"); ?>
    <div id="content">
        <div id="books-menu" class="clearfix">
            <ul>
                <li><a href="/books" id="books-icon-1">Last Posted Books</a></li>
                <li><a href="/book/post-book" id="books-icon-2">Post Book Ad</a></li>
                <li><a href="/book/my-books" id="books-icon-3">My Book Ads</a></li>
            </ul>
        </div>
        <div id="my-books-box-wrapper">
            <?php for($i = 0; $i < $num_of_ads; $i++){ ?>
                <div class="my-books-box">
                    <div class="title my-books-title"><a href="/book/<?php echo $book_id[$i]; ?>"><?php echo $title[$i]; ?></a></div>
                    <div class="content-box">
                        <a href="/image/book/<?php echo $picture[$i] ?>" data-lightbox="image">
                            <div class="my-books-image"><img src="/image/book/<?php echo $picture[$i] ?>"></div>
                        </a>
                        <?php setlocale(LC_MONETARY, 'en_CA'); //To use money_format(); ?>
                        <div class="my-books-item"><span>Posted Date: </span><?php echo date_format($date[$i],"F j, \a\\t g:ia") ?></div>
                        <div class="my-books-item"><span>Price: </span><?php echo  money_format('$%!i', $price[$i]) ?></div>
                        <div class="my-books-item"><a href="/book/post-book?Edit=<?php echo $book_id[$i]; ?>">Edit This Ad</a></div>
                        <div class="my-books-item"><a href="/book/post-book?Delete=<?php echo $book_id[$i]; ?>">Delete This Ad</a></div>
                        <div class="my-books-item"><a href="/book/<?php echo $book_id[$i]; ?>">Open This Ad</a></div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php
require_once("../include/footer.php");
?>
