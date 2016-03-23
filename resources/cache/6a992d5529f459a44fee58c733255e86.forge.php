<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>

    <link href="resources/assets/css/styles.css", type="text/css" rel="stylesheet">
    <script src="resources/assets/js/test.js", type="text/javascript"></script>
</head>
<body>
    <div class="nav">
        
    </div>
    <div class="container">
        
    <?php foreach($myArray as $a): ?>
        <?php echo htmlentities($a) ?>
    <?php endforeach;if(count($myArray) == 0): ?>
        There are no items in your array
    <?php endif; ?>

    </div>
</body>
</html>