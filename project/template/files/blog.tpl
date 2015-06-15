<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Carousel Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Custom styles for this template -->
    <link href="template/css/carousel.css" rel="stylesheet">
</head>
<!-- NAVBAR
================================================== -->
<body>

        <div class="jumbotron">
            <h1>BLOG</h1>



        </div>

        <div class="col-sm-8 blog-main">

            <ol class="breadcrumb">
                <li><a href="#">Home</a></li>
                <li><a href="#">Library</a></li>
                <li class="active">Data</li>
            </ol>
            <div class="blog-post">
                <p>
                    <a href="index.php?pageid=6">Overzicht</a>
                    </p>
                <!-- START BLOCK : BLOGLIST -->

                <div>
                    <form class="form-inline" action="index.php?pageid=6" method="post">
                        <div class="form-group">
                            <input type="text" class="form-control" id="search" placeholder="search" name="search" value="{SEARCH}">
                        </div>
                        <button type="submit" class="btn btn-default">zoek</button>
                    </form>

                        <!-- START BLOCK : BLOGROWA -->
                        <div>
                            <div class=" col-sm-12" id="blog">
                                <div class=" col-sm-12" id="blog">
                                    <div class="col-sm-12" id="blogcontent">
                                        <img src="include/blogg.png" class="blogg"/>
                                    </div>
                                <div class="col-sm-8">
                                    <h2>{TITLE}</h2>
                                </div>
                                <div class="col-sm-8">
                                    By: {USERNAME}
                                </div>
                                <div class="col-sm-8">
                                    {CONTENT}
                                </div>
                                <div class="col-sm-4">
                                    <a href="index.php?pageid=6&action=details&idblog={BLOGID}" class="btn btn-default">Lees verder > </a>
                                </div>
                            </div>
                        </div>
                            </div>
                        <!-- END BLOCK : BLOGROWA -->




                </div>
                <!-- END BLOCK : BLOGLIST -->
                <!-- START BLOCK : BLOGROW -->
                <div>
                    <div class=" col-sm-12" id="blog">
                        <div class="col-sm-12" id="blogcontent">
                            <img src="include/blogg.png" class="blogg"/>
                        </div>
                        <div class="col-sm-12">
                            <h2>{TITLE}</h2>
                        </div>
                        <div class="col-sm-12">
                            By: {USERNAME}
                        </div>
                        <div class="col-sm-8">
                            {CONTENT}
                        </div>

                    </div>
                </div>
                <!-- END BLOCK : BLOGROW -->
                    <div>
                        <!-- START BLOCK : DETAILSFORM -->
                        <form class="form-horizontal" action="index.php?pageid=7&action=toevoegen" method="post">
                            <div class="form-group">
                                    <div class="col-sm-6">
                                        <textarea class="form-control" {READONLY1} id="inputtext" placeholder="place comment here" maxlength="255" name="text">{TEXT}</textarea>
                                    </div>
                            </div>
                            <input type="hidden" name="blogid" value="{BLOGID}">
                            <input type="hidden" name="commentid" value="{COMMENTID}">
                            <input type="hidden" name="productid" value="{PRODUCTID}">
                            <button type="submit" class="btn btn-default">plaats comment</button>
                        </form>

                        <!-- END BLOCK : DETAILSFORM -->

                        <!-- START BLOCK : NEE -->
                        <div>
                            <p>u moet ingelogd zijn om een comment te plaatsen</p>
                        </div>
                        <!-- END BLOCK : NEE -->

                        <!-- START BLOCK : COMMENTS -->
                        <div class="col-sm-12">
                            <h2>By: {USERNAME}</h2>
                        </div>
                        <div class="col-sm-12">
                            {TEXT}
                        </div>
                        <!-- START BLOCK : COMMENTSADMIN -->
                        <div class="col-sm-12">
                            <p class="col-sm-8"></p>
                            <p class="col-sm-4">
                            <a href="index.php?pageid=7&action=wijzigen&idcomment={COMMENTID}" class="btn btn-primary">wijzigen</a>
                            <a href="index.php?pageid=7&action=verwijderen&idcomment={COMMENTID}" class="btn btn-danger">verwijderen</a>
                            </p>
                        </div>
                        <!-- END BLOCK : COMMENTSADMIN -->
                        <!-- END BLOCK : COMMENTS -->

                    </div>
            </div><!-- /.blog-post -->

        </div>

        <div class="col-sm-3 col-sm-offset-1 blog-sidebar">




            <div class="sidebar-module sidebar-module-inset">
                <h4>About</h4>
                <p>Etiam porta <em>sem malesuada magna</em> mollis euismod. Cras mattis consectetur purus sit amet fermentum. Aenean lacinia bibendum nulla sed consectetur.</p>
            </div>
            <div class="sidebar-module">
                <h4>Archives</h4>
                <ol class="list-unstyled">
                    <li><a href="#">March 2014</a></li>
                    <li><a href="#">February 2014</a></li>
                    <li><a href="#">January 2014</a></li>
                    <li><a href="#">December 2013</a></li>
                    <li><a href="#">November 2013</a></li>
                    <li><a href="#">October 2013</a></li>
                    <li><a href="#">September 2013</a></li>
                    <li><a href="#">August 2013</a></li>
                    <li><a href="#">July 2013</a></li>
                    <li><a href="#">June 2013</a></li>
                    <li><a href="#">May 2013</a></li>
                    <li><a href="#">April 2013</a></li>
                </ol>
            </div>
            <div class="sidebar-module">
                <h4>Elsewhere</h4>
                <ol class="list-unstyled">
                    <li><a href="#">GitHub</a></li>
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">Facebook</a></li>
                </ol>
            </div>
        </div>





<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="template/js/bootstrap.min.js"></script>
<script src="template/js/docs.min.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="template/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>
