<?php
require_once("./include/header.php");

$pagination = "none";
?>
<div id="container" class="clearfix">
    <?php require_once("./include/sidebar.php"); ?>
    <div id="content">
        <div id="books-menu" class="clearfix">
            <ul>
                <li><a href="/books" id="books-icon-1">Last Posted Books</a></li>
                <li><a href="/book/post-book" id="books-icon-2">Post Book Ad</a></li>
                <li><a href="/book/my-books" id="books-icon-3">My Book Ads</a></li>
            </ul>
        </div>
        <div class="title">FIND BOOKS</div>
        <div  class="content-box clearfix">
            <form action="/books" method="post" name="books-form">
                <ul class="books-elements">
                    <li><input type="text" class="books-input" name="books-search" placeholder="Type the book name..." value="<?php echo $books_search; ?>"></li>
                    <li>
                        <select name="book-type" class="books-select">
                            <option value="All">All</option>
                            <option value="Biography">Biography</option>
                            <option value="Children's Book">Children's Book</option>
                            <option value="Comics & Graphic Novels">Comics & Graphic Novels</option>
                            <option value="Study Book">Study Book</option>
                            <option value="Young Adult">Young Adult</option>
                        </select>
                    </li>
                    <li><input type="submit" class="button-4" name="submit" value="Find Books"></li>
                </ul>
            </form>
        </div>
        <div class="title">LAST POSTED BOOKS</div>
            <div  class="content-box clearfix">
            <?php
            /* Paging connection requests */
            $page = isset($_GET['Page']) ? (int)$_GET['Page'] : 1; //If page is not set, set the default 1.
            $per_page = isset($_GET['PerPage']) && $_GET['PerPage'] <= 50 ? (int)$_GET['PerPage'] : 15;
            $start = ($page > 1) ? ($page * $per_page) - $per_page : 0; //Every page, it will display the next 15.

            $query = "SELECT SQL_CALC_FOUND_ROWS * FROM BOOKS ORDER BY BOOK_DATE DESC LIMIT {$start}, {$per_page};";
            $result = mysqli_query($dbConnection, $query);
            setlocale(LC_MONETARY, 'en_CA'); //To use money_format();

            /* Paging links for books */
            $query2 = "SELECT FOUND_ROWS() as TOTAL";
            $result2 = mysqli_query($dbConnection, $query2) or die(mysqli_error($dbConnection));
            while($data = mysqli_fetch_assoc($result2)){ $total = $data['TOTAL']; }
            $pages = ceil($total / $per_page); //Ceil rounds up.

            if($total > 15 && $pages > 1){
                $pagination = "display: block;";
            }else{
                $pagination = "display: none;";
                $style = "books-books-inline"; /* If books are less than 15, remove margin-bottom (activating class) */
            }

            if(mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_assoc($result)){
                ?>
                <div class="books-books <?php echo $style; ?>">
                    <table width="744px">
                        <tr>
                            <td width="66" rowspan="3">
                                <a href="/book/<?php echo $row['BOOK_ID']; ?>">
                                    <img src="/image/book/<?php echo $row['BOOK_PICTURE_ID'] = ($row['BOOK_PICTURE_ID'] != NULL ? $row['BOOK_PICTURE_ID'] : "Default.png"); ?>" >
                                </a>
                            </td>
                            <td valign="top"><a href='/book/<?php echo $row['BOOK_ID']; ?>' title='<?php echo $row['BOOK_TITLE']; ?>'> <?php echo $row['BOOK_TITLE']; ?><a/></td>
                            <td width="75" align="right" class="font-14 color-666"><?php echo money_format('$%!i', $row['BOOK_PRICE']); ?></td>
                            <?php $date = date_create($row['BOOK_DATE']); ?>
                            <td width="140" align="right" class="font-14 color-666"><?php echo date_format($date,"F j, \a\\t g:ia"); ?></td>
                        </tr>
                        <tr class="books-content">
                            <td rowspan="2" colspan="3" valign="top" class="font-14">
                                <?php
                                /* Display maximum 40 words or 250 character in the content. Cut the rest off. */
                                $content = $row['BOOK_CONTENT'];
                                $words = array();
                                $words = explode(" ", $content, 41);
                                if(count($words) == 41){
                                    $words[40] = "...";
                                    $content = implode(" ", $words);
                                }else if(strlen($row['BOOK_CONTENT']) > 250 && count($words) != 41){
                                    $content = substr($row['BOOK_CONTENT'], 0, 250) . "...";
                                }
                                echo $content;
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
                }
            }else{
                echo "<div class='font-14' style='text-align: center'>There is no book ad at the moment.</div>";
            }
            ?>
            <div class="pagination" style="<?php echo $pagination; ?>">
                <?php for($i = 1; $i <= $pages; $i++){ ?>
                    <a href="?Page=<?php echo $i; ?>&PerPage=<?php echo $per_page; ?>"<?php if($page == $i){echo " class='books-selected'";} ?>><?php echo $i; ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php
require_once("./include/footer.php");
?>
